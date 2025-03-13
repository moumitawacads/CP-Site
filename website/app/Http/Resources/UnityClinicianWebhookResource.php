<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnityClinicianWebhookResource extends JsonResource
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
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "address_1" => $this->address_1,
            "address_2" => $this->address_2,
            "city" => $this->city,
            "province" => $this->province,
            "postal_code" => $this->postal_code,
            "country" => $this->country,
            "occupation_id" => $this->occupation_id,
            "upload_clinician_certificate" => $this->upload_clinician_certificate,
            "clinician_code" => $this->clinician_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => new UnityUserWebhookResource($this->user),
            "learners" => $this->learners,
            "occupation" => $this->occupation,
            "settings" => $this->settings
        ];
    }
}
