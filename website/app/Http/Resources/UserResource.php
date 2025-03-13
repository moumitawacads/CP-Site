<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $plans = [];
        if ($this->subscriptions && count($this->subscriptions) > 0) {
            foreach ($this->subscriptions as $subscription) {
                $plan = $this->getPlanByStripePriceId($subscription->items[0]->stripe_price);
                if ($plan) {
                    $plans[] = $plan;
                }
            }
        }

        return [
            "id" => $this->id,
            "fullname" => $this->fullname,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "role_slug" => $this->role_slug,
            "preferred_language" => $this->preferred_language,
            "access_token" => $this->access_token,
            "additional_comments" => $this->additional_comments,
            "additional_comments_decrypted" => $this->additional_comments_decrypted,
            "calendar_webhook_response" => $this->calendar_webhook_response,
            "consent_of_speech_language_datetime" => $this->consent_of_speech_language_datetime,
            "consent_of_speech_language_flag" => $this->consent_of_speech_language_flag,
            "created_at" => $this->created_at,
            "email_verified_at" => $this->email_verified_at,
            "gdpr_datetime" => $this->gdpr_datetime,
            "gdpr_flag" => $this->gdpr_flag,
            "last_login_datetime" => $this->last_login_datetime,
            "last_logout_datetime" => $this->last_logout_datetime,
            "pm_last_four" => $this->pm_last_four,
            "pm_type" => $this->pm_type,
            "scheduled_meeting_end_datetime" => $this->scheduled_meeting_end_datetime,
            "scheduled_meeting_start_datetime" => $this->scheduled_meeting_start_datetime,
            "scheduled_meeting_timezone" => $this->scheduled_meeting_timezone,
            "session_datetime" => $this->session_datetime,
            "stripe_id" => $this->stripe_id,
            "trial_ends_at" => $this->trial_ends_at,
            "updated_at" => $this->updated_at,
            "verification_code" => $this->verification_code,
            "verification_expires_at" => $this->verification_expires_at,
            "verification_status" => $this->verification_status,
            "virtual_readiness_assessment_datetime" => $this->virtual_readiness_assessment_datetime,
            "virtual_readiness_assessment_flag" => $this->virtual_readiness_assessment_flag,
            "subscriptions" => $this->subscriptions ?? null,
            "virtual_language_instruction_agreed" => $this->virtualLanguageInstructionAgreed ?? null,
            "gdpr_agreed" => $this->gdprAgreed ?? null,
            "assessments" => $this->assessments ?? null,
            "literacyDiagnostic" => $this->literacyDiagnostic ?? null,
            "homeworkHelpers" => $this->homeworkHelpers ?? null,
            "plans" => $plans
        ];
    }
}
