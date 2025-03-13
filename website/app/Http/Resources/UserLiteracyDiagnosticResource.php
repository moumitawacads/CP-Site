<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLiteracyDiagnosticResource extends JsonResource
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
            "user_id" => $this->user_id,
            "learner_id" => $this->learner_id,
            "clinician_id" => $this->clinician_id,
            "learner_unique_code" => $this->learner_unique_code,
            "scheduled_meeting_end_datetime" => $this->scheduled_meeting_end_datetime,
            "scheduled_meeting_start_datetime" => $this->scheduled_meeting_start_datetime,
            "scheduled_meeting_timezone" => $this->scheduled_meeting_timezone,
            "status" => $this->status,
            "calendar_webhook_response" => $this->calendar_webhook_response,
            "opportunity_id" => $this->opportunity_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => new UserResource($this->user),
            "assign_by" => new UserResource($this->assignBy),
            "clinician" => new ClinicianResource($this->clinician)
        ];
    }
}
