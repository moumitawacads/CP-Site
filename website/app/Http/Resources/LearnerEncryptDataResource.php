<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class LearnerEncryptDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "clinician_id" => $this->clinician_id,
            "user_id" => $this->user_id,
            "parent_id" => $this->parent_id,
            "speech_language_diagnosis" => $this->speech_language_diagnosis ? Crypt::decryptString($this->speech_language_diagnosis) : "",
            "first_name" => $this->first_name ? Crypt::decryptString($this->first_name) : "",
            "last_name" => $this->last_name ? Crypt::decryptString($this->last_name) : "",
            "clinician_name" => $this->clinician_name ? Crypt::decryptString($this->clinician_name) : "",
            "gender" => $this->gender ? Crypt::decryptString($this->gender) : "",
            "name_of_school" => $this->name_of_school ? Crypt::decryptString($this->name_of_school) : "",
            "grade" => $this->grade ? Crypt::decryptString($this->grade) : "",
            "culture" => $this->culture ? Crypt::decryptString($this->culture) : "",
            "family_diagnosis" => $this->family_diagnosis ? Crypt::decryptString($this->family_diagnosis) : "",
            "history" => $this->history ? Crypt::decryptString($this->history) : "",
            "learner_type" => $this->learner_type ? Crypt::decryptString($this->learner_type) : "",
            "learner_email" => $this->learner_email ? Crypt::decryptString($this->learner_email) : "",
            "learner_phone_number" => $this->learner_phone_number ? Crypt::decryptString($this->learner_phone_number) : "",
            "flag" => $this->flag,
            "learner_code" => $this->learner_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
