<?php

namespace App\Classes;

use App\Models\Parents;
use App\Models\User;
use Illuminate\Http\Request;

class ParentCreate
{
    public function run(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $fullname = FirstNameLastName::run($request->fullname);
            $first_name = $fullname[0];
            $last_name = $fullname[1];

            // user creation
            $user = UserCreation::run(
                $request->fullname,
                $request->email,
                $request->phone_number,
                $request->password,
                'parents',
                $request->preferred_language,
                json_decode($request->consent_of_speech_language_form, true),
                json_decode($request->virtual_language_instruction_form, true),
                json_decode($request->gdpr_form, true)
            );

            // parents data insert
            $parent = new Parents();
            $parent->user_id = $user->id;
            $parent->fullname = $request->fullname;
            $parent->type = $request->type;
            $parent->reason = $request->reason;
            $parent->save();

            // create contacts to cxm
            $data = [
                "email" => $request->email,
                "phone" => $request->phone_number,
                "firstName" => $first_name,
                "lastName" => $last_name,
                "name" => $request->fullname,
                "city" => $request->city,
                "country" => $request->country,
                "tags" => [
                    "parents",
                ],
            ];
            $additionalInfo = [
                "reason" => $request->reason,
                "preferred_language" => $request->preferred_language,
            ];
            (new CreateContactsToCXM())->run($data, $parent, $additionalInfo);
        }

        return $user;
    }
}
