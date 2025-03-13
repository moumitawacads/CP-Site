<?php

namespace App\Classes;

use App\Models\GdprAgree;
use Illuminate\Http\Request;

class GDPRCreate
{
    public static function run($formData, $user)
    {
        $gdprAgreed = GdprAgree::where('user_id', $user->id)->first();
        if (!$gdprAgreed) {
            $gdprAgreed = new GdprAgree();
            $gdprAgreed->user_id = $user->id;
            $gdprAgreed->receive_updates_promotions_and_other = $formData["receive_updates_promotions_and_other"];
            $gdprAgreed->consent_to_the_processing_of_your_personal_data = $formData["consent_to_the_processing_of_your_personal_data"];
            $gdprAgreed->save();
        }

        return $gdprAgreed;
    }
}
