<?php

namespace App\Http\Controllers;

use App\Classes\FirstNameLastName;
use App\Classes\UserCreation;
use App\Exceptions\ResourceNotFound;
use App\Http\Resources\ClinicianResource;
use App\Models\Clinician;
use App\Services\HighLevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Classes\ClinicianLearnerLink;
use App\Classes\CreateContactsToCXM;
use App\Classes\GenerateCode;
use App\Classes\UpdateLatestActions;
use App\Http\Resources\LearnerEncryptDataResource;
use App\Http\Resources\LearnerResource;
use App\Http\Resources\ParentResource;
use App\Mail\LearnerLinkMail;
use App\Models\ClinicianLatestAction;
use App\Models\ClinicianLearner;
use App\Models\ClinicianSetting;
use App\Models\Learner;
use App\Models\LearnerEncryptData;
use App\Models\Parents;
use App\Models\UserAssessment;
use App\Models\UserHomeworkHelper;
use App\Models\UserLiteracyDiagnostic;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class ClinicianController extends Controller
{
    protected const CLINICIAN = 'clinician';
    protected const LEARNER = 'learner';
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('validation', 'register', 'learnerRegistrationByClinician', 'send');
    }

    public function index(Request $request)
    {
        $query = Clinician::with(['user', 'occupation', 'settings', 'latest_actions']);
        if ($request->has('user_id') && $request->user_id != '') {
            $query = $query->where('user_id', $request->user_id);
        }
        $clinician = $query->paginate($request->input('per_page', 100));

        if ($clinician->total() == 0) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        return ClinicianResource::collection($clinician);
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'email|unique:users,email',
            'phone_number' => 'required|string||unique:users,phone_number',
            'password' => 'required|min:7|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{7,}$/',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'occupation_id' => 'required|integer|exists:occupations,id',
            'upload_clinician_certificate' => 'required|file|mimes:jpg,png,pdf|max:2048',
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
            // exploding first name and last name
            $fullname = FirstNameLastName::run($request->fullname);
            $first_name = $fullname[0];
            $last_name = $fullname[1];

            $filePath = "";
            if ($request->hasFile('upload_clinician_certificate')) {
                // Store file in the `public/uploads` directory
                $filePath = $request->file('upload_clinician_certificate')->store('uploads', 'public');
            }

            // clinician user creation
            // $user = UserCreation::run($request->fullname, $request->email, $request->phone_number, $request->password, self::CLINICIAN, $request->preferred_language);

            // clinician creation
            $clinician = new Clinician();
            // $clinician->user_id = $user->id;
            $clinician->first_name = $first_name;
            $clinician->last_name = $last_name;
            $clinician->address_1 = $request->address_1;
            $clinician->address_2 = $request->address_2;
            $clinician->city = $request->city;
            $clinician->province = $request->province;
            $clinician->postal_code = $request->postal_code;
            $clinician->country = $request->country;
            $clinician->occupation_id = $request->occupation_id;
            $clinician->upload_clinician_certificate = $filePath;
            $clinician->save();

            // clinician setting create
            $clinician_settings = new ClinicianSetting();
            $clinician_settings->clinician_id = $clinician->id;
            $clinician_settings->save();

            return response()->json([
                'message' => "Clinician registed successfully",
                'data' => new ClinicianResource($clinician->load(['user', 'occupation', 'settings', 'latest_actions']))
            ]);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function learnerRegistrationByClinician(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinician_id' => 'required|integer|max:255',
            'speech_language_diagnosis' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'clinician_name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'name_of_school' => 'required|string|max:255',
            'learner_age' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'culture' => 'required|string|max:255',
            'family_diagnosis' => 'required|string|max:255',
            'history' => 'required|string|max:255',
            'learner_type' => 'required|string|max:255',
            'learner_email' => 'nullable|string|email|unique:users,email',
            'learner_phone_number' => 'nullable|string|unique:users,phone_number|max:255',
            'password' => 'nullable|min:7|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{7,}$/',
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
            $learner_code = GenerateCode::run();

            // learner user creation
            $user = UserCreation::run($request->fullname, $request->learner_email, $request->learner_phone_number, $request->password, self::LEARNER, $request->preferred_language);

            // normal data store in normal database
            $learner = new Learner();
            $learner->clinician_id = $request->clinician_id;
            $learner->user_id = $user->id;
            $learner->first_name = $first_name;
            $learner->last_name = $last_name;
            $learner->gender = $request->gender;
            $learner->grade = $request->grade;
            $learner->learner_age = $request->learner_age;
            $learner->clinician_name = $request->clinician_name;
            $learner->save();

            // secure data store in separate encrypted database
            $learnerEncryptData = new LearnerEncryptData();
            $learnerEncryptData->clinician_id = $request->clinician_id;
            $learnerEncryptData->user_id = $user->id;
            $learnerEncryptData->speech_language_diagnosis = Crypt::encryptString($request->speech_language_diagnosis);
            $learnerEncryptData->first_name = Crypt::encryptString($first_name);
            $learnerEncryptData->last_name = Crypt::encryptString($last_name);
            $learnerEncryptData->clinician_name = Crypt::encryptString($request->clinician_name);
            $learnerEncryptData->gender = Crypt::encryptString($request->gender);
            $learnerEncryptData->name_of_school = Crypt::encryptString($request->name_of_school);
            $learnerEncryptData->grade = Crypt::encryptString($request->grade);
            $learnerEncryptData->culture = Crypt::encryptString($request->culture);
            $learnerEncryptData->family_diagnosis = Crypt::encryptString($request->family_diagnosis);
            $learnerEncryptData->history = Crypt::encryptString($request->history);
            $learnerEncryptData->learner_type = Crypt::encryptString($request->learner_type);
            $learnerEncryptData->learner_email = Crypt::encryptString($request->learner_email);
            $learnerEncryptData->learner_phone_number = Crypt::encryptString($request->learner_phone_number);
            $learnerEncryptData->learner_code = $learner_code;
            $learnerEncryptData->save();

            // create contacts to cxm
            $clinician = Clinician::find($request->clinician_id);
            $data = [
                "phone" => $clinician->user->phone_number,
                "firstName" => $first_name,
                "lastName" => $last_name,
                "name" => $request->fullname,
                "tags" => [
                    "learner",
                ],
            ];
            $additionalInfo = [
                "clinician_name" => $request->clinician_name,
                "gender" => $request->gender,
                "learner_age" => $request->learner_age,
                "name_of_school" => $request->name_of_school,
                "grade" => $request->grade,
                "culture" => $request->culture,
                "family_diagnosis" => $request->family_diagnosis,
                "history" => $request->history,
                "learner_type" => $request->learner_type,
                "speech_language_diagnosis" => $request->speech_language_diagnosis,
                "preferred_language" => $request->preferred_language,
                "creator_type" => "clinician",
            ];
            (new CreateContactsToCXM())->run($data, $learner, $additionalInfo);

            if ($request->has('tab_id') && $request->tab_id != '') {
                // clinician learner link
                ClinicianLearnerLink::run($request->clinician_id, $learnerEncryptData->id, $learner->id);

                // update parents actions
                $action_description = "New learner account created for " . $learner->first_name . " " . mb_substr($learner->last_name, 0, 1);
                $dynamic_value = [
                    "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1)
                ];

                UpdateLatestActions::run($request->clinician_id, $request->tab_id, $action_description, self::CLINICIAN, $dynamic_value);

                return response()->json([
                    "message" => "Learner Registration Successfully By Clinician"
                ]);
            } else {
                // clinician code generate
                $accessToken = null;
                $clinician = Clinician::where('id', $request->clinician_id)->first();
                if ($clinician) {
                    $clinician->clinician_code = GenerateCode::run();
                    $clinician->save();

                    $accessToken = $clinician->user->createToken("token-key")->plainTextToken;

                    // clinician learner link
                    ClinicianLearnerLink::run($clinician->id, $learnerEncryptData->id, $learner->id);
                }

                // mail send to learner
                $data = [
                    'name' => $first_name,
                    'message' => 'We have created your link and code is: ',
                    'code' => $learnerEncryptData->learner_code
                ];
                Mail::to($request->learner_email)->send(new LearnerLinkMail($data));


                return response()->json([
                    "message" => "Learner Registration Successfully By Clinician",
                    "user" => array_merge($clinician->user->toArray(), ["access_token" => $accessToken])
                ]);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function updateSystemSettings(Request $request, $id)
    {
        $clinicianSettings = ClinicianSetting::where('clinician_id', $id)->first();
        if (!$clinicianSettings) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }
        $clinicianSettings->update([$request->key => $request->value]);

        // update clinician actions
        $action_description = ucwords(str_replace("_", " ", str_replace("_flag", "", $request->key))) . " system setting is " . ($request->value == 1 ? "enabled" : "disabled");
        $dynamic_value = [
            "system_setting" => ucwords(str_replace("_", " ", str_replace("_flag", "", $request->key)))
        ];

        UpdateLatestActions::run($id, $request->tab_id, $action_description, self::CLINICIAN, $dynamic_value);

        return response()->json(['message' => 'System settings updated successfully']);
    }

    public function getLatestActions(Request $request)
    {
        $query = ClinicianLatestAction::where('clinician_id', $request->id)->latest();

        $clinician_latest_actions = $query->paginate($request->input('per_page', 10));

        return response()->json($clinician_latest_actions);
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->highLevelService->sendSms($request->phone, $request->message);

        return response()->json($response);
    }

    public function getHomeworkHelper(Request $request, $clinician_id)
    {
        $clinician = Clinician::find($clinician_id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $query = UserHomeworkHelper::where('clinician_id', $clinician->id)->latest();
        $userHomeworkHelpers = $query->paginate($request->input('per_page', 30));
        $learnerData = [];
        if ($userHomeworkHelpers->count() > 0) {
            foreach ($userHomeworkHelpers as $userHomeworkHelper) {
                $learner = Learner::where('id', $userHomeworkHelper->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                ];
            }
        }

        return response()->json(['data' => $learnerData, 'pagination' => $this->getPagination($userHomeworkHelpers)]);
    }

    public function getAssessment(Request $request, $clinician_id)
    {
        $clinician = Clinician::find($clinician_id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerData = [];
        $query = UserAssessment::where('clinician_id', $clinician_id)->latest();
        $userAssessments = $query->paginate($request->input('per_page', 30));

        if (count($userAssessments) > 0) {
            foreach ($userAssessments as $userAssessment) {
                $learner = Learner::where('id', $userAssessment->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('user_id', $learner->user_id)->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'user_assessment_data' => $userAssessment
                ];
            }
        }

        return response()->json(['data' => $learnerData, 'pagination' => $this->getPagination($userAssessments)]);
    }

    public function getLiteracyDiagnostic(Request $request, $clinician_id)
    {
        $clinician = Clinician::find($clinician_id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $query = UserLiteracyDiagnostic::where('clinician_id', $clinician_id)->latest();
        $userLiteracyDiagnostics = $query->paginate($request->input('per_page', 30));
        $learnerData = [];
        if ($userLiteracyDiagnostics->count() > 0) {
            foreach ($userLiteracyDiagnostics as $userLiteracyDiagnostic) {
                $learner = Learner::where('id', $userLiteracyDiagnostic->learner_id)->first();
                $learnerEnceyptData = LearnerEncryptData::where('user_id', $learner->user_id)->first();
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
