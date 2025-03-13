<?php

namespace App\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCreation
{
    public static function run(
        $fullname,
        $email,
        $phone_number,
        $password,
        $role_slug,
        $preferred_language,
        $consent_of_speech_languageformData = null,
        $virtual_readiness_assessmentformData = null,
        $gdprformData = null
    ) {
        $user = new User();
        $user->fullname = $fullname;
        $user->email = $email;
        $user->phone_number = $phone_number;
        $user->password = $password ? Hash::make($password) : null;
        $user->role_slug = $role_slug;
        $user->preferred_language = $preferred_language;
        $user->consent_of_speech_language_flag = $consent_of_speech_languageformData ? ($consent_of_speech_languageformData['consentOfSpeechLanguage'] ? 1 : 0) : $consent_of_speech_languageformData;
        $user->consent_of_speech_language_datetime = $consent_of_speech_languageformData ? $consent_of_speech_languageformData['consentOfSpeechLanguageDatetime'] : $consent_of_speech_languageformData;
        $user->virtual_readiness_assessment_flag = $virtual_readiness_assessmentformData ? ($virtual_readiness_assessmentformData['virtualReadinessAssessment'] ? 1 : 0) : $virtual_readiness_assessmentformData;
        $user->virtual_readiness_assessment_datetime = $virtual_readiness_assessmentformData ? $virtual_readiness_assessmentformData['virtualReadinessAssessmentDatetime'] : $virtual_readiness_assessmentformData;

        $user->gdpr_flag = $gdprformData ? ($gdprformData['gdpr'] ? 1 : 0) : $gdprformData;
        $user->gdpr_datetime = $gdprformData ? $gdprformData['gdprDatetime'] : $gdprformData;
        $user->save();

        return $user;
    }
}
