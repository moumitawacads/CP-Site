<?php

namespace App\Http\Controllers;

use App\Classes\ClinicianLearnerLink;
use App\Classes\FirstNameLastName;
use App\Classes\GenerateCode;
use App\Classes\UpdateLatestActions;
use App\Classes\UserCreation;
use App\Http\Resources\LearnerEncryptDataResource;
use App\Http\Resources\LearnerResource;
use App\Http\Resources\ParentResource;
use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\Learner;
use App\Models\LearnerEncryptData;
use App\Models\Parents;
use App\Models\UserHomeworkHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LearnerController extends Controller
{
    protected const LEARNER = 'learner';
    protected const CLINICIAN = 'clinician';
    protected const PARENTS = 'parents';
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('register');
    }

    public function index(Request $request)
    {
        $learnerData = [];
        $perPage = $request->get('per_page', 10);
        $clinicianLearners = ClinicianLearner::where('clinician_id', $request->clinician_id)->latest()->paginate($perPage);
        if ($clinicianLearners->count() > 0) {
            foreach ($clinicianLearners as $clinicianLearner) {
                if ($clinicianLearner['learner_id'] && $clinicianLearner['learner_encrypt_data_id']) {
                    $learner = Learner::where('id', $clinicianLearner['learner_id'])->first();
                    $learnerEnceyptData = LearnerEncryptData::where('id', $clinicianLearner['learner_encrypt_data_id'])->first();
                    $learnerData[] = [
                        'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'homework_helper_flag' => $clinicianLearner->homework_helper_flag,
                        'parent_approval_received' => $clinicianLearner->parent_approval_received,
                        'id' => $clinicianLearner->id
                    ];
                } else {
                    $learnerEnceyptData = LearnerEncryptData::where('id', $clinicianLearner['learner_encrypt_data_id'])->first();
                    $learnerData[] = [
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'homework_helper_flag' => $clinicianLearner->homework_helper_flag,
                        'parent_approval_received' => $clinicianLearner->parent_approval_received,
                        'id' => $clinicianLearner->id
                    ];
                }
            }
        }

        return response()->json([
            'data' => $learnerData,
            'pagination' => $this->getPagination($clinicianLearners)
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinician_id' => 'nullable|string|max:255',
            'speech_language_diagnosis' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'clinician_name' => 'nullable|string|max:255',
            'gender' => 'required|string|max:255',
            'learner_age' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'culture' => 'required|string|max:255',
            'family_diagnosis' => 'required|string|max:255',
            'history' => 'required|string|max:255',
            'learner_type' => 'required|string|max:255',
            'learner_email' => 'required|string|email|unique:users,email',
            'learner_phone_number' => 'required|string|max:255',
            'password' => 'required|min:7|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{7,}$/',
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

            // user creation
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
            $learner->learner_type = $request->learner_type;
            $learner->clinician_name = $request->clinician_name;
            $learner->save();

            // secure data store in separate encrypted database
            $learnerEncryptData = new LearnerEncryptData();
            $learnerEncryptData->clinician_id = $request->clinician_id;
            $learnerEncryptData->user_id = $user->id;
            $learnerEncryptData->speech_language_diagnosis = Crypt::encryptString($request->speech_language_diagnosis);
            $learnerEncryptData->culture = Crypt::encryptString($request->culture);
            $learnerEncryptData->family_diagnosis = Crypt::encryptString($request->family_diagnosis);
            $learnerEncryptData->history = Crypt::encryptString($request->history);
            $learnerEncryptData->learner_code = $learner_code;
            $learnerEncryptData->save();

            // clinician learner link
            ClinicianLearnerLink::run($request->clinician_id, $learnerEncryptData->id, $learner->id);

            return response()->json([
                "message" => "Learner Registration Successfully",
                "user" => array_merge($user->toArray(), ["access_token" => $user->createToken("token-key")->plainTextToken])
            ]);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function updateHomeworkHelper(Request $request, $id)
    {
        $clinicianLearner = ClinicianLearner::where('id', $id)->first();
        if (!$clinicianLearner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $clinicianLearner->homework_helper_flag = $request->homework_helper_flag;
        $clinicianLearner->service_type = $request->homework_helper_flag === 1 ? "homework-helper" : null;
        $clinicianLearner->save();

        $clinician = Clinician::where('id', $clinicianLearner->clinician_id)->first();
        if ($request->homework_helper_flag === 1) {
            $userHomeworkHelper = new UserHomeworkHelper();
            $userHomeworkHelper->user_id = $clinician->user_id;
            $userHomeworkHelper->learner_id = $clinicianLearner->learner_id;
            $userHomeworkHelper->clinician_id = $clinician->id;
            $userHomeworkHelper->save();
        } else {
            $userHomeworkHelper = UserHomeworkHelper::where('user_id', $clinician->user_id)->where('learner_id', $clinicianLearner->learner_id)->first();
            $userHomeworkHelper->delete();
        }

        // update clinician actions
        $learner = Learner::where('id', $clinicianLearner->learner_id)->first();
        $action_description = "Homework Helper " . ($request->homework_helper_flag == 1 ? "enabled" : "disabled") . " for " . $learner->first_name . " " . mb_substr($learner->last_name, 0, 1);
        $dynamic_value = [
            "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1)
        ];

        UpdateLatestActions::run($clinicianLearner->clinician_id, $request->tab_id, $action_description, self::CLINICIAN, $dynamic_value);

        return response()->json(['message' => 'Homework Helper option updated successfully']);
    }

    public function getLearnersByParentsId(Request $request, $parent_id)
    {
        $learnerData = [];
        $perPage = $request->get('per_page', 10);
        if ($request->has('page') && $request->page === 'all') {
            $learners = Learner::where('parent_id', $parent_id)->get();
        } else {
            $learners = Learner::where('parent_id', $parent_id)->latest()->paginate($perPage);
        }
        if ($learners->count() > 0) {
            foreach ($learners as $learner) {
                $learnerEnceyptData = LearnerEncryptData::where('parent_id', $parent_id)->where('user_id', $learner->user_id)->first();
                $clincianLearnerLink = ClinicianLearner::where('learner_id', $learner->id)->whereNotNull('clinician_id')->latest()->first();
                $learnerData[] = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                ];
            }
        }

        return response()->json([
            'data' => $learnerData,
            'pagination' => $request->has('page') && $request->page === 'all' ? null : $this->getPagination($learners)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'learner_id' => 'required|integer',
            'learner_encrypt_data_id' => 'required|integer',
            'speech_language_diagnosis' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:255',
            'culture' => 'nullable|string|max:255',
            'tab_id' => 'nullable|string',
            'parent_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            // normal data update in normal database
            $learner = Learner::where('id', $request->learner_id)->first();
            $learner->gender = $request->gender;
            $learner->grade = $request->grade;
            $learner->save();

            // secure data update in separate encrypted database
            $learnerEncryptData = LearnerEncryptData::where('id', $request->learner_encrypt_data_id)->first();
            $learnerEncryptData->speech_language_diagnosis = Crypt::encryptString($request->speech_language_diagnosis);
            $learnerEncryptData->culture = Crypt::encryptString($request->culture);
            $learnerEncryptData->save();

            // update clinician actions
            $action_description = $learner->first_name . " " . mb_substr($learner->last_name, 0, 1) . " data has been updated";
            $dynamic_value = [
                "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1)
            ];

            UpdateLatestActions::run(
                $request->parent_id,
                $request->tab_id,
                $action_description,
                self::PARENTS,
                $dynamic_value
            );

            return response()->json(["message" => "Learner Updated Successfully"]);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 500);
        }

        return response()->json();
    }

    public function show($id)
    {
        $learner = Learner::find($id);
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerData = null;
        $clincianLearner = ClinicianLearner::where('learner_id', $id)->latest()->first();
        if ($clincianLearner) {
            $learnerEnceyptData = LearnerEncryptData::where('id', $clincianLearner->learner_encrypt_data_id)->first();
            $learnerData = [
                'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions', 'sessions.sessionGames'])),
                'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                'homework_helper_flag' => $clincianLearner->homework_helper_flag,
                'parent_approval_received' => $clincianLearner->parent_approval_received,
                'id' => $clincianLearner->id
            ];
        }

        return response()->json(['data' => $learnerData]);
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
