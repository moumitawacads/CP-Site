<?php

namespace App\Http\Controllers;

use App\Classes\ClinicianLearnerLink;
use App\Classes\CreateContactsToCXM;
use App\Classes\FirstNameLastName;
use App\Classes\UpdateLatestActions;
use App\Classes\UserCreation;
use App\Events\SendNotifiactionToClinician;
use App\Exceptions\ResourceNotFound;
use App\Http\Resources\LearnerEncryptDataResource;
use App\Http\Resources\LearnerResource;
use App\Http\Resources\ParentResource;
use App\Mail\LearnerLinkMail;
use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\Learner;
use App\Models\LearnerEncryptData;
use App\Models\Parents;
use App\Models\ParentsLatestAction;
use App\Models\PlanPrice;
use App\Models\UserAssessment;
use App\Models\UserHomeworkHelper;
use App\Models\UserLiteracyDiagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Subscription;

class ParentController extends Controller
{
    protected const PARENTS = 'parents';
    protected const LEARNER = 'learner';
    protected const MINI_ARTIC_ASSESSMENT = "Mini Artic Assessment";
    protected const MINI_LITERACY_DIAGNOSTIC = "Mini Literacy Diagnostic";
    protected const GROUP_COACHING = "Group Coaching";
    protected const HOMEWORK_HELPER = "Homework Helper";
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('validation', 'register', 'learnerRegistrationByParent');
    }

    public function index(Request $request)
    {
        $query = Parents::with(['user', 'latest_actions']);
        if ($request->has('user_id') && $request->user_id != '') {
            $query = $query->where('user_id', $request->user_id);
        }
        $parents = $query->paginate($request->input('per_page', 100));

        if ($parents->total() == 0) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        return ParentResource::collection($parents);
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone_number' => 'required|string|max:255',
            'password' => 'required|min:7|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{7,}$/',
            'preferred_language' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return response()->json([
            'message' => "Clinician validation successfully"
        ]);
    }

    public function register(Request $request)
    {
        try {
            // user creation
            // $user = UserCreation::run($request->fullname, $request->email, $request->phone_number, $request->password, self::PARENTS, $request->preferred_language);

            // parents data insert
            $parent = new Parents();
            // $parent->user_id = $user->id;
            $parent->fullname = $request->fullname;
            $parent->type = $request->type;
            $parent->reason = $request->reason;
            $parent->save();

            return response()->json([
                "message" => "Parents Registration Successfully",
                "data" => new ParentResource($parent->load(['user']))
            ]);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function learnerRegistrationByParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|integer|max:255',
            'speech_language_diagnosis' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'name_of_school' => 'required|string|max:255',
            'learner_age' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'culture' => 'required|string|max:255',
            'family_diagnosis' => 'required|string|max:255',
            'history' => 'required|string|max:255',
            'learner_type' => 'required|string|max:255',
            'learner_email' => 'required|string|email|unique:users,email',
            'learner_phone_number' => 'required|string|unique:users,phone_number|max:255',
            'password' => 'required|min:7|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{7,}$/',
            'tab_id' => 'nullable|string',
            'preferred_language' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            // exploding first name and last name
            $fullname = FirstNameLastName::run($request->fullname);
            $first_name = $fullname[0];
            $last_name = $fullname[1];
            // learner code generate
            // $learner_code = GenerateCode::run();

            // user creation
            $user = UserCreation::run($request->fullname, $request->learner_email, $request->learner_phone_number, $request->password, self::LEARNER, $request->preferred_language);

            // normal data store in normal database
            $learner = new Learner();
            $learner->parent_id = $request->parent_id;
            $learner->user_id = $user->id;
            $learner->first_name = $first_name;
            $learner->last_name = $last_name;
            $learner->gender = $request->gender;
            $learner->grade = $request->grade;
            $learner->learner_age = $request->learner_age;
            $learner->name_of_school = $request->name_of_school;
            $learner->save();

            // secure data store in separate encrypted database
            $learnerEncryptData = new LearnerEncryptData();
            $learnerEncryptData->parent_id = $request->parent_id;
            $learnerEncryptData->user_id = $user->id;
            $learnerEncryptData->speech_language_diagnosis = Crypt::encryptString($request->speech_language_diagnosis);
            $learnerEncryptData->culture = Crypt::encryptString($request->culture);
            $learnerEncryptData->family_diagnosis = Crypt::encryptString($request->family_diagnosis);
            $learnerEncryptData->history = Crypt::encryptString($request->history);
            $learnerEncryptData->learner_type = Crypt::encryptString($request->learner_type);
            $learnerEncryptData->save();

            if ($request->has('tab_id') && $request->tab_id != '') {
                // update parents actions
                $action_description = "New learner account created for " . $learner->first_name . " " . mb_substr($learner->last_name, 0, 1);
                $dynamic_value = [
                    "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1)
                ];

                UpdateLatestActions::run($request->parent_id, $request->tab_id, $action_description, self::PARENTS, $dynamic_value);

                return response()->json([
                    "message" => "Learner Registration Successfully By Parent",
                    "data" => [
                        "learner_id" => $learner->id,
                        "learner_encrypt_data_id" => $learnerEncryptData->id
                    ]
                ]);
            } else {
                $accessToken = null;
                $parent = Parents::where('id', $request->parent_id)->first();
                if ($parent) {
                    $accessToken = $parent->user->createToken("token-key")->plainTextToken;
                }

                return response()->json([
                    "message" => "Learner Registration Successfully By Parent",
                    "user" => array_merge($parent->user->toArray(), [
                        "access_token" => $accessToken,
                        "learner_id" => $learner->id,
                        "learner_encrypt_data_id" => $learnerEncryptData->id
                    ])
                ]);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function clinicianLearnerLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'learner_id' => 'required|integer|exists:learners,id',
            'learner_encrypt_data_id' => 'required|integer',
            'clinician_name' => 'required|string|max:255',
            'clinician_code' => 'required|string|max:255',
            'tab_id' => 'nullable|string',
            'parent_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $clinician = Clinician::where('clinician_code', $request->clinician_code)->first();
        if ($clinician) {
            $clinicianLearner = ClinicianLearner::where('clinician_id', $clinician->id)->where('learner_id', $request->learner_id)->first();
            if (!$clinicianLearner) {
                // clinician learner link
                ClinicianLearnerLink::run($clinician->id, $request->learner_encrypt_data_id, $request->learner_id);

                $learner = Learner::where('id', $request->learner_id)->first();
                $learnerEncryptedData = LearnerEncryptData::where('id', $request->learner_encrypt_data_id)->first();

                if ($request->has('tab_id') && $request->tab_id != '') {
                    // update parents actions
                    $action_description = $learner->first_name . " " . mb_substr($learner->last_name, 0, 1) . ", now connected with " . $clinician->first_name . " " . mb_substr($clinician->last_name, 0, 1);
                    $dynamic_value = [
                        "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1),
                        "clinician_name" => $clinician->first_name . " " . mb_substr($clinician->last_name, 0, 1)
                    ];

                    UpdateLatestActions::run($request->parent_id, $request->tab_id, $action_description, self::PARENTS, $dynamic_value);
                }

                // mail send to clinician
                $data = [
                    'name' => $clinician->first_name,
                    'message' => 'You linked with one learner. Learner Name: ',
                    'learner_name' => $learner->first_name . ' ' . $learner->last_name
                ];
                Mail::to($clinician->user->email)->send(new LearnerLinkMail($data, 'clinician_link'));

                $data = [
                    "clinician_id" => $clinician->id,
                    "learner_id" => $learner->id,
                    "message" =>  $learner->first_name . " " . mb_substr($learner->last_name, 0, 1) . ", new learner now associated with you"

                ];

                event(new SendNotifiactionToClinician($data));

                return response()->json(["message" => "Learner linked with a clinician successfully"]);
            } else {
                return response()->json(["message" => "This learner already linked with " . $clinician->first_name . " " . mb_substr($clinician->last_name, 0, 1)], 400);
            }
        }

        return response()->json(["message" => "Invalid Clinician Code"], 400);
    }

    public function getLatestActions(Request $request)
    {
        $query = ParentsLatestAction::where('parents_id', $request->id)->latest();

        $parents_latest_actions = $query->paginate($request->input('per_page', 10));

        return response()->json($parents_latest_actions);
    }

    public function getLearnerDetails($parent_id, $learner_id)
    {
        $learnerData = null;
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learner = Learner::where('id', $learner_id)->where('parent_id', $parent_id)->first();
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parent_id)->where('user_id', $learner->user_id)->first();
        $clincianLearnerLink = ClinicianLearner::where('learner_id', $learner->id)->whereNotNull('clinician_id')->latest()->first();

        $learnerData = [
            'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
            'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
        ];

        return response()->json(['data' => $learnerData]);
    }

    public function quickLearnerCreate(Request $request, $parent_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        try {
            if (is_null($request->learnerId) && $request->type === 'new' && !$request->used) {
                // exploding first name and last name
                $fullname = FirstNameLastName::run($request->learnerName);
                $first_name = $fullname[0];
                $last_name = $fullname[1];

                // user creation
                $user = UserCreation::run($request->learnerName, null, null, null, self::LEARNER, null);

                // normal data store in normal database
                $learner = new Learner();
                $learner->parent_id = $parent_id;
                $learner->user_id = $user->id;
                $learner->first_name = $first_name;
                $learner->last_name = $last_name;
                $learner->learner_age = $request->learnerAge;
                $learner->save();

                // secure data store in separate encrypted database
                $learnerEncryptData = new LearnerEncryptData();
                $learnerEncryptData->parent_id = $parent_id;
                $learnerEncryptData->user_id = $user->id;
                $learnerEncryptData->save();

                // update parents actions
                $action_description = "New learner account created for " . $learner->first_name . " " . mb_substr($learner->last_name, 0, 1);
                $dynamic_value = [
                    "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1)
                ];

                UpdateLatestActions::run($parent_id, "my-learners", $action_description, self::PARENTS, $dynamic_value);

                // assessment or literacy data insert
                $this->assignLearnerPlan($request, $parents->user_id, $learner->id);

                // create contacts to cxm
                $data = [
                    "phone" => $parents->user->phone_number,
                    "firstName" => $first_name,
                    "lastName" => $last_name,
                    "name" => $request->learnerName,
                    "tags" => [
                        "learner",
                    ],
                ];
                $additionalInfo = [
                    "creator_type" => "parents",
                ];

                (new CreateContactsToCXM())->run($data, $learner, $additionalInfo);
            } else if ($request->learnerId && $request->type === 'existing' && !$request->used) {
                $learner = Learner::find($request->learnerId);

                // assessment or literacy data insert
                $this->assignLearnerPlan($request, $parents->user_id, $learner->id);
            }

            return response()->json([
                "message" => "Quick Learner Created Successfully",
                "learner" => new LearnerResource($learner->load(['characterCustomization', 'sessions']))
            ]);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function getServiceLearners($parent_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerData = [];
        $userAssessments = UserAssessment::where('user_id', $parents->user_id)->where('status', 0)->get();
        if (count($userAssessments) > 0) {
            foreach ($userAssessments as $userAssessment) {
                $learner = Learner::where('id', $userAssessment->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parents->id)->where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions', 'sessions.sessionGames'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'plan_name' => "Mini Artic Assessment"
                ];
            }
        }

        $userLiteracyDiagnostics = UserLiteracyDiagnostic::where('user_id', $parents->user_id)->where('status', 0)->get();
        if (count($userLiteracyDiagnostics) > 0) {
            foreach ($userLiteracyDiagnostics as $userLiteracyDiagnostic) {
                $learner = Learner::where('id', $userLiteracyDiagnostic->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parents->id)->where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions', 'sessions.sessionGames'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'plan_name' => "Mini Literacy Diagnostic"
                ];
            }
        }

        return response()->json([
            'data' => $learnerData,
        ]);
    }

    private function assignLearnerPlan(Request $request, $user_id, $learner_id)
    {
        if ($request->planName === self::MINI_ARTIC_ASSESSMENT) {
            $userAssessment = new UserAssessment();
            $userAssessment->user_id = $user_id;
            $userAssessment->learner_id = $learner_id;
            $userAssessment->save();
        } else if ($request->planName === self::MINI_LITERACY_DIAGNOSTIC) {
            $userLiteracyDiagnostic = new UserLiteracyDiagnostic();
            $userLiteracyDiagnostic->user_id = $user_id;
            $userLiteracyDiagnostic->learner_id = $learner_id;
            $userLiteracyDiagnostic->save();
        } else if ($request->planName === self::HOMEWORK_HELPER) {
            $userHomeworkHelper = new UserHomeworkHelper();
            $userHomeworkHelper->user_id = $user_id;
            $userHomeworkHelper->learner_id = $learner_id;
            $userHomeworkHelper->save();
        } else if ($request->planName === self::GROUP_COACHING) {
        }
    }

    public function getLearnerServiceDetails($parent_id, $learner_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learner = Learner::where('id', $learner_id)->where('parent_id', $parent_id)->first();
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $message = "This learner didn't complete the assessment";
        $assessment = false;
        $userAssessment = UserAssessment::where('user_id', $parents->user_id)->where('learner_id', $learner->id)->where('status', 1)->latest()->first();
        if ($userAssessment) {
            $message = "This learner completed the assessment";
            $assessment = true;
        }

        return response()->json([
            'message' => $message,
            'assessment' => $assessment
        ]);
    }

    public function getHomeworkHelper(Request $request, $parent_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $query = UserHomeworkHelper::where('user_id', $parents->user_id)->latest();
        $userHomeworkHelpers = $query->paginate($request->input('per_page', 30));
        $learnerData = [];
        if ($userHomeworkHelpers->count() > 0) {
            foreach ($userHomeworkHelpers as $userHomeworkHelper) {
                $learner = Learner::where('id', $userHomeworkHelper->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parent_id)->where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions', 'sessions.sessionGames'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                ];
            }
        }

        return response()->json(['data' => $learnerData, 'pagination' => $this->getPagination($userHomeworkHelpers)]);
    }

    public function getAssessment(Request $request, $parent_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $query = UserAssessment::where('user_id', $parents->user_id)->latest();
        $userAssessments = $query->paginate($request->input('per_page', 30));
        $learnerData = [];
        if ($userAssessments->count() > 0) {
            foreach ($userAssessments as $userAssessment) {
                $learner = Learner::where('id', $userAssessment->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parent_id)->where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'user_assessment_data' => $userAssessment,
                ];
            }
        }

        return response()->json(['data' => $learnerData, 'pagination' => $this->getPagination($userAssessments)]);
    }

    public function getLiteracyDiagnostic(Request $request, $parent_id)
    {
        $parents = Parents::find($parent_id);
        if (!$parents) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $query = UserLiteracyDiagnostic::where('user_id', $parents->user_id)->latest();
        $userLiteracyDiagnostics = $query->paginate($request->input('per_page', 30));
        $learnerData = [];
        if ($userLiteracyDiagnostics->count() > 0) {
            foreach ($userLiteracyDiagnostics as $userLiteracyDiagnostic) {
                $learner = Learner::where('id', $userLiteracyDiagnostic->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parent_id)->where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'user_literacy_diagnostic_data' => $userLiteracyDiagnostic,
                ];
            }
        }

        return response()->json(['data' => $learnerData, 'pagination' => $this->getPagination($userLiteracyDiagnostics)]);
    }

    private function getPagination($data)
    {
        return [
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'links' => $data->linkCollection()->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active'],
                ];
            })->toArray(),
        ];
    }
}
