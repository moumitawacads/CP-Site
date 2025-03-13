<?php

namespace App\Http\Resources;

use App\Models\LearnerGameLevel;
use Illuminate\Http\Resources\Json\JsonResource;

class LearnerSessionGameResource extends JsonResource
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
            "learner_session_id" => $this->learner_session_id,
            "game_name" => $this->game_name,
            "game_score" => $this->game_score,
            "number_of_attempts" => $this->number_of_attempts,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "learner_game_levels" => new LearnerGameLevelResource($this->learnerGameLevels),
        ];
    }
}
