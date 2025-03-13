<?php

namespace App\Http\Controllers;

use App\Http\Resources\LearnerEncryptDataResource;
use App\Http\Resources\LearnerResource;
use App\Http\Resources\ParentResource;
use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\Learner;
use App\Models\LearnerEncryptData;
use App\Models\Parents;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use App\Classes\ClinicianLearnerLink;
use App\Events\SendNotifiactionToClinician;
use App\Http\Resources\ClinicianResource;
use App\Http\Resources\UserResource;
use App\Mail\LearnerLinkMail;
use App\Models\User;
use App\Models\UserHomeworkHelper;
use App\Models\UserLiteracyDiagnostic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    protected const MINI_ARTIC_ASSESSMENT = 'mini-artic-assessment';
    protected const MINI_LITERACY_DIAGNOSTIC = 'mini-literacy-diagnostic';
    protected const HOMEWORK_HELPER = 'homework-helper';

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $clinicians_count = Clinician::count();
        $learners_count = Learner::count();
        $parents_count = Parents::count();
        $userAssessments_count = UserAssessment::count();
        $userLiteracyDiagnostics_count = UserLiteracyDiagnostic::count();
        $data = [
            "clinicians_count" => $clinicians_count,
            "learners_count" => $learners_count,
            "parents_count" => $parents_count,
            "assessments_count" => $userAssessments_count,
            "literacy_diagnostics_count" => $userLiteracyDiagnostics_count,
            "services_count" => ($userAssessments_count + $userLiteracyDiagnostics_count)
        ];

        return response()->json(["data" => $data]);
    }

    public function getLearnersByClinicianId(Request $request)
    {
        $learnerData = [];
        $perPage = $request->get('per_page', 100);
        if ($request->has('clinician_id') && $request->clinician_id != '') {
            $learners = ClinicianLearner::where('clinician_id', $request->clinician_id)->latest()->paginate($perPage);
        } else if ($request->has('parent_id') && $request->parent_id != '') {
            $learners = Learner::where('parent_id', $request->parent_id)->latest()->paginate($perPage);
        }


        if ($learners->count() > 0) {
            foreach ($learners as $learner) {
                if ($request->has('clinician_id') && $request->clinician_id != '') {
                    $learnerDetails = Learner::where('id', $learner['learner_id'])->first();
                    $learnerEnceyptData = LearnerEncryptData::where('id', $learner['learner_encrypt_data_id'])->first();

                    $learnerData[] = [
                        'learner_data' => new LearnerResource($learnerDetails->load(['characterCustomization', 'sessions'])),
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'homework_helper_flag' => $learner->homework_helper_flag,
                        'parent_approval_received' => $learner->parent_approval_received,
                        'id' => $learner->id
                    ];
                } else if ($request->has('parent_id') && $request->parent_id != '') {
                    $learnerEnceyptData = LearnerEncryptData::where('parent_id', $request->parent_id)->where('user_id', $learner->user_id)->first();
                    $clincianLearnerLink = ClinicianLearner::where('learner_id', $learner->id)->whereNotNull('clinician_id')->latest()->first();
                    $clinician_name = "";
                    if ($clincianLearnerLink) {
                        $clinician = Clinician::where('id', $clincianLearnerLink->clinician_id)->first();
                        $clinician_name = $clinician->first_name . " " . $clinician->last_name;
                    }
                    $learnerData[] = [
                        'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'clinician_name' => $clinician_name
                    ];
                }
            }
        }

        return response()->json([
            'data' => $learnerData,
            'pagination' => $this->getPagination($learners)
        ]);
    }

    public function assessmentsList(Request $request)
    {
        $query = UserAssessment::query();

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('id', $request->id);
        }

        $userAssessments = $query->get();

        $learnerData = [];
        if (count($userAssessments) > 0) {
            foreach ($userAssessments as $userAssessment) {
                $user = User::where('id', $userAssessment->user_id)->first();
                $clinicianLearner = ClinicianLearner::where('learner_id', $userAssessment->learner_id)->where('service_type', self::MINI_ARTIC_ASSESSMENT)->first();
                $clinician = null;
                if ($clinicianLearner) {
                    $clinician = Clinician::where('id', $clinicianLearner->clinician_id)->first();
                }
                $learner = Learner::where('id', $userAssessment->learner_id)->first();
                $learnerData[] = [
                    'assessment_data' => $userAssessment,
                    'user_data' => new UserResource($user),
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'clinician_data' => !is_null($clinician) ? new ClinicianResource($clinician) : null
                ];
            }
        }

        return response()->json(['data' => $learnerData]);
    }

    public function assignClinicianForAssessmentService(Request $request, $id)
    {
        $clinician = Clinician::where('id', $request->clinician_id)->first();
        if ($clinician) {
            $learner = Learner::where('id', $request->learner_id)->first();

            $clinicianLearner = ClinicianLearner::where('clinician_id', $clinician->id)->where('learner_id', $request->learner_id)->where('service_type', self::MINI_ARTIC_ASSESSMENT)->first();
            if (!$clinicianLearner) {
                $parent = Parents::where('user_id', $request->parents_id)->first();
                $learnerEncryptedData = LearnerEncryptData::where('user_id', $learner->user_id)->where('parent_id', $parent->id)->first();

                // clinician learner link
                $clinicianLearnerResponse = ClinicianLearnerLink::run($clinician->id, $learnerEncryptedData->id, $request->learner_id, self::MINI_ARTIC_ASSESSMENT);

                $clinicianLearnerResponse->assessment_viewed = 1;
                $clinicianLearnerResponse->save();
            }

            $userAssessment = UserAssessment::find($id);
            $userAssessment->clinician_id = $clinician->id;
            $userAssessment->assign_by = Auth::user()->id;
            $userAssessment->save();

            // mail send to clinician
            $data = [
                'name' => $clinician->first_name,
                'message' => 'You have been assigned to the Mini Artic Assessment service with a learner. Learner Name: ',
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
        }
    }

    public function literacyDiagnosticsList(Request $request)
    {
        $query = UserLiteracyDiagnostic::query();

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('id', $request->id);
        }

        $userLiteracyDiagnostics = $query->get();

        $learnerData = [];
        if (count($userLiteracyDiagnostics) > 0) {
            foreach ($userLiteracyDiagnostics as $userLiteracyDiagnostic) {
                $user = User::where('id', $userLiteracyDiagnostic->user_id)->first();
                $clinicianLearner = ClinicianLearner::where('learner_id', $userLiteracyDiagnostic->learner_id)->where('service_type', self::MINI_LITERACY_DIAGNOSTIC)->first();
                $clinician = null;
                if ($clinicianLearner) {
                    $clinician = Clinician::where('id', $clinicianLearner->clinician_id)->first();
                }
                $learner = Learner::where('id', $userLiteracyDiagnostic->learner_id)->first();
                $learnerData[] = [
                    'literacy_data' => $userLiteracyDiagnostic,
                    'user_data' => new UserResource($user),
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'clinician_data' => !is_null($clinician) ? new ClinicianResource($clinician) : null
                ];
            }
        }

        return response()->json(['data' => $learnerData]);
    }

    public function assignClinicianForLiteracyService(Request $request, $id)
    {
        $clinician = Clinician::where('id', $request->clinician_id)->first();
        if ($clinician) {
            $learner = Learner::where('id', $request->learner_id)->first();

            $clinicianLearner = ClinicianLearner::where('clinician_id', $clinician->id)->where('learner_id', $request->learner_id)->where('service_type', self::MINI_LITERACY_DIAGNOSTIC)->first();
            if (!$clinicianLearner) {
                $parent = Parents::where('user_id', $request->parents_id)->first();
                $learnerEncryptedData = LearnerEncryptData::where('user_id', $learner->user_id)->where('parent_id', $parent->id)->first();

                // clinician learner link
                $clinicianLearnerResponse = ClinicianLearnerLink::run($clinician->id, $learnerEncryptedData->id, $request->learner_id, self::MINI_LITERACY_DIAGNOSTIC);

                $clinicianLearnerResponse->literacy_viewed = 1;
                $clinicianLearnerResponse->save();
            }

            $userLiteracyDiagnostic = UserLiteracyDiagnostic::find($id);
            $userLiteracyDiagnostic->clinician_id = $clinician->id;
            $userLiteracyDiagnostic->assign_by = Auth::user()->id;
            $userLiteracyDiagnostic->save();

            // mail send to clinician
            $data = [
                'name' => $clinician->first_name,
                'message' => 'You have been assigned to the Mini Literacy Diagnostic service with a learner. Learner Name: ',
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
        }
    }

    public function homeworkHelpersList(Request $request)
    {
        $query = UserHomeworkHelper::query();

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('id', $request->id);
        }

        $userHomeworkHelpers = $query->get();

        $learnerData = [];
        if (count($userHomeworkHelpers) > 0) {
            foreach ($userHomeworkHelpers as $userHomeworkHelper) {
                $clinicianLearner = ClinicianLearner::where('learner_id', $userHomeworkHelper->learner_id)->where('service_type', self::HOMEWORK_HELPER)->first();
                $clinician = null;
                if ($clinicianLearner) {
                    $clinician = Clinician::where('id', $clinicianLearner->clinician_id)->first();
                }
                $learner = Learner::where('id', $userHomeworkHelper->learner_id)->first();
                $learnerData[] = [
                    'homework_helper_data' => $userHomeworkHelper,
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'clinician_data' => !is_null($clinician) ? new ClinicianResource($clinician) : null
                ];
            }
        }

        return response()->json(['data' => $learnerData]);
    }

    public function assignClinicianForHomeworkHelperService(Request $request, $id)
    {
        $clinician = Clinician::where('id', $request->clinician_id)->first();
        if ($clinician) {
            $learner = Learner::where('id', $request->learner_id)->first();

            $clinicianLearner = ClinicianLearner::where('clinician_id', $clinician->id)->where('learner_id', $request->learner_id)->where('service_type', self::HOMEWORK_HELPER)->first();
            if (!$clinicianLearner) {
                $parent = Parents::where('user_id', $request->parents_id)->first();
                $learnerEncryptedData = LearnerEncryptData::where('user_id', $learner->user_id)->where('parent_id', $parent->id)->first();

                // clinician learner link
                $clinicianLearnerResponse = ClinicianLearnerLink::run($clinician->id, $learnerEncryptedData->id, $request->learner_id, self::HOMEWORK_HELPER);

                $clinicianLearnerResponse->homework_helper_flag = 1;
                $clinicianLearnerResponse->homework_helper_viewed = 1;
                $clinicianLearnerResponse->save();
            }

            $userHomeworkHelper = UserHomeworkHelper::find($id);
            $userHomeworkHelper->clinician_id = $clinician->id;
            $userHomeworkHelper->assign_by = Auth::user()->id;
            $userHomeworkHelper->save();

            // mail send to clinician
            $data = [
                'name' => $clinician->first_name,
                'message' => 'You have been assigned to the Homework Helper service with a learner. Learner Name: ',
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
        }
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
