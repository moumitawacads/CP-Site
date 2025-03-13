<?php

namespace App\Http\Resources;

use App\Models\LearnerSessionGame;
use Illuminate\Http\Resources\Json\JsonResource;

class LearnerSessionResource extends JsonResource
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
            "learner_id" => $this->learner_id,
            "session_start_time" => $this->session_start_time,
            "atrium" => $this->atrium,
            "world" => $this->world,
            "target_sound" => $this->target_sound,
            "pick_possition" => $this->pick_possition,
            "pick_syllabus" => $this->pick_syllabus,
            "spell_word" => $this->spell_word,
            "coins_earned" => $this->coins_earned,
            "session_end_time" => $this->session_end_time,
            "session_games" => LearnerSessionGameResource::collection($this->sessionGames),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
