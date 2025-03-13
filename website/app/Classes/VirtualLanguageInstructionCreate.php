<?php

namespace App\Classes;

use App\Models\VirtualLanguageInstructionAgree;
use Illuminate\Http\Request;

class VirtualLanguageInstructionCreate
{
    public static function run($formData, $user)
    {
        $virtualLanguageInstruction = VirtualLanguageInstructionAgree::where('user_id', $user->id)->first();
        if (!$virtualLanguageInstruction) {
            $virtualLanguageInstruction = new VirtualLanguageInstructionAgree();
            $virtualLanguageInstruction->user_id = $user->id;
            $virtualLanguageInstruction->access_to_a_computer_or_tablet = $formData["access_to_a_computer_or_tablet"];
            $virtualLanguageInstruction->have_a_headset_or_microphone = $formData["have_a_headset_or_microphone"];
            $virtualLanguageInstruction->adult_be_available_to_assist_your_child = $formData["adult_be_available_to_assist_your_child"];
            $virtualLanguageInstruction->distraction_free_space_for_your_child = $formData["distraction_free_space_for_your_child"];
            $virtualLanguageInstruction->comfortable_participating_in_interactive_activities = $formData["comfortable_participating_in_interactive_activities"];
            $virtualLanguageInstruction->save();
        }

        return $virtualLanguageInstruction;
    }
}
