<?php

namespace App\Http\Resources;

use App\Models\Learner;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $parentLearners = Learner::where('parent_id', $this->id)->get()->map->only('id');

        return [
            "id" => $this->id,
            "fullname" => $this->fullname,
            "type" => $this->type,
            "reason" => $this->reason,
            "contacts_id" => $this->contacts_id,
            "latest_actions" => $this->latest_actions,
            "user" => new UserResource($this->user),
            "learners_count" => count($parentLearners),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
