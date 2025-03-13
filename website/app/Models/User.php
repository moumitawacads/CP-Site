<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'assessment_viewed' => 'array'
    ];

    protected $appends = [
        'additional_comments_decrypted',
    ];


    public function clinicians()
    {
        return $this->hasMany(Clinician::class, 'user_id', 'id');
    }

    public function learners()
    {
        return $this->hasMany(Learner::class, 'user_id', 'id');
    }

    public function parents()
    {
        return $this->hasMany(Parents::class, 'user_id', 'id');
    }

    public function virtualLanguageInstructionAgreed()
    {
        return $this->hasOne(VirtualLanguageInstructionAgree::class, 'user_id', 'id');
    }

    public function gdprAgreed()
    {
        return $this->hasOne(GdprAgree::class, 'user_id', 'id');
    }

    public function getAdditionalCommentsDecryptedAttribute()
    {
        return $this->additional_comments && $this->additional_comments != "" ? Crypt::decryptString($this->additional_comments) : "";
    }

    public function whatsNew()
    {
        return $this->hasMany(WhatsNew::class, 'author_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
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
        return $this->hasMany(UserLiteracyDiagnostic::class);
    }

    public function getPlanByStripePriceId($stripe_price_id)
    {
        return PlanPrice::with('plan')->where('stripe_price_id', $stripe_price_id)->first();
    }

    public function userAssessments()
    {
        return $this->hasOne(UserAssessment::class, 'assign_by', 'id');
    }

    public function userLiteracyDiagnostics()
    {
        return $this->hasOne(UserLiteracyDiagnostic::class, 'assign_by', 'id');
    }

    public function userHomeworkHelpers()
    {
        return $this->hasOne(UserHomeworkHelper::class, 'assign_by', 'id');
    }
}
