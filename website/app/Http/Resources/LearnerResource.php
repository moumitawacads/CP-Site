<?php

namespace App\Http\Resources;

use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\LearnerCharacterCustomization;
use App\Models\Parents;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LearnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);
        $parent = null;
        if ($this->parent_id) {
            $parent = Parents::where('id', $this->parent_id)->first();
        }

        $clinicians_data = [];
        $learnerClinicians = ClinicianLearner::where('learner_id', $this->id)->get();
        if ($learnerClinicians && count($learnerClinicians) > 0) {
            foreach ($learnerClinicians as $learnerClinician) {
                $clinician = Clinician::where('id', $learnerClinician->clinician_id)->first();
                $clinicians_data[] = [
                    'clinician_data' => new ClinicianResource($clinician),
                    'clinician_learner_link_data' => $learnerClinician
                ];
            }
        }

        return [
            "id" => $this->id,
            "clinician_id" => $this->clinician_id,
            "user_id" => $this->user_id,
            "parent_id" => $this->parent_id,
            "speech_language_diagnosis" => $this->speech_language_diagnosis,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "clinician_name" => $this->clinician_name,
            "gender" => $this->gender,
            "name_of_school" => $this->name_of_school,
            "learner_age" => $this->learner_age,
            "grade" => $this->grade,
            "culture" => $this->culture,
            "family_diagnosis" => $this->family_diagnosis,
            "history" => $this->history,
            "learner_type" => $this->learner_type,
            "contacts_id" => $this->contacts_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "clinicians_data" => $clinicians_data,
            "user" => new UserResource($user),
            "parent_data" => $this->parent_id ? new ParentResource($parent) : $parent,
            "character_customization" => new LearnerCharacterCustomizationResource($this->characterCustomization),
            "sessions" => LearnerSessionResource::collection($this->sessions),
            "assessments" => UserAssessmentResource::collection($this->assessments) ?? null,
            "literacy_diagnostics" => UserLiteracyDiagnosticResource::collection($this->literacyDiagnostic) ?? null,
            "homework_helpers" => UserHomeworkHelperResource::collection($this->homeworkHelpers) ?? null,
        ];
    }
}
