<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinician extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'address_1',
        'address_2',
        'city',
        'province',
        'postal_code',
        'country',
        'occupation_id',
        'upload_clinician_certificate',
        'clinician_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    public function settings()
    {
        return $this->hasOne(ClinicianSetting::class, 'clinician_id', 'id');
    }

    public function latest_actions()
    {
        return $this->hasMany(ClinicianLatestAction::class, 'clinician_id', 'id');
    }

    public function learners()
    {
        return $this->hasMany(ClinicianLearner::class, 'clinician_id', 'id');
    }

    public function assessments()
    {
        return $this->hasMany(UserAssessment::class);
    }

    public function literacyDiagnostics()
    {
        return $this->hasMany(UserLiteracyDiagnostic::class);
    }

    public function homeworkHelpers()
    {
        return $this->hasMany(UserHomeworkHelper::class);
    }
}
