<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parents()
    {
        return $this->belongsTo(Parents::class, 'parents_id', 'id');
    }

    public function characterCustomization()
    {
        return $this->hasOne(LearnerCharacterCustomization::class, 'learner_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(LearnerSession::class, 'learner_id', 'id');
    }

    public function assessments()
    {
        return $this->hasMany(UserAssessment::class);
    }

    public function literacyDiagnostic()
    {
        return $this->hasMany(UserLiteracyDiagnostic::class);
    }

    public function homeworkHelpers()
    {
        return $this->hasMany(UserHomeworkHelper::class);
    }
}
