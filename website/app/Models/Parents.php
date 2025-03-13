<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = "parents";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function learner()
    {
        return $this->hasMany(Learner::class, 'parents_id', 'id');
    }

    public function latest_actions()
    {
        return $this->hasMany(ParentsLatestAction::class, 'parents_id', 'id');
    }
}
