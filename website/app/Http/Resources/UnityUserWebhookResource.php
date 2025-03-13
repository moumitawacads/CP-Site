<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnityUserWebhookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            "id" => $this->id,
            "fullname" => $this->fullname,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "role_slug" => $this->role_slug,
            "preferred_language" => $this->preferred_language,
        ];

        if ($this->type) {
            $data["access_token"] = $this->access_token;
        } else {
            $data["subscriptions"] = $this->subscriptions;
        }

        return $data;
    }
}
