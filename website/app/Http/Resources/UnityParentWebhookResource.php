<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnityParentWebhookResource extends JsonResource
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
            "fullname" => $this->fullname,
            "type" => $this->type,
            "reason" => $this->reason,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => new UnityUserWebhookResource($this->user),
        ];
    }
}
