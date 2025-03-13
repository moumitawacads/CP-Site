<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserHomeworkHelperResource extends JsonResource
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
            "assign_by" => $this->assign_by,
            "status" => $this->status,
            "opportunity_id" => $this->opportunity_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => new UserResource($this->user),
            "assign_by" => new UserResource($this->assignBy),
            "clinician" => new ClinicianResource($this->clinician)
        ];
    }
}
