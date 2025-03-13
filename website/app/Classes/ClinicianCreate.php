<?php

namespace App\Classes;

use App\Models\Clinician;
use App\Models\ClinicianSetting;
use App\Models\Occupation;
use App\Models\User;
use Illuminate\Http\Request;

class ClinicianCreate
{
    public function run(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // exploding first name and last name
            $fullname = FirstNameLastName::run($request->fullname);
            $first_name = $fullname[0];
            $last_name = $fullname[1];

            $filePath = "";
            if ($request->hasFile('upload_clinician_certificate')) {
                // Store file in the `public/uploads` directory
                $filePath = $request->file('upload_clinician_certificate')->store('uploads', 'public');
            }

            // clinician user creation
            $user = UserCreation::run(
                $request->fullname,
                $request->email,
                $request->phone_number,
                $request->password,
                'clinician',
                $request->preferred_language,
                json_decode($request->consent_of_speech_language_form, true),
                json_decode($request->virtual_language_instruction_form, true),
                json_decode($request->gdpr_form, true)
            );

            // clinician creation
            $clinician = new Clinician();
            $clinician->user_id = $user->id;
            $clinician->first_name = $first_name;
            $clinician->last_name = $last_name;
            $clinician->address_1 = $request->address_1;
            $clinician->address_2 = $request->address_2;
            $clinician->city = $request->city;
            $clinician->province = $request->province;
            $clinician->postal_code = $request->postal_code;
            $clinician->country = $request->country;
            $clinician->occupation_id = $request->occupation_id;
            $clinician->upload_clinician_certificate = $filePath;
            $clinician->clinician_code = GenerateCode::run();
            $clinician->save();

            // clinician setting create
            $clinician_settings = new ClinicianSetting();
            $clinician_settings->clinician_id = $clinician->id;
            $clinician_settings->save();

            // create contacts to cxm
            $occupation_name = Occupation::where('id', $request->occupation_id)->first()->name;
            $data = [
                "email" => $request->email,
                "phone" => $request->phone_number,
                "firstName" => $first_name,
                "lastName" => $last_name,
                "name" => $request->fullname,
                "address1" => $request->address_1,
                "city" => $request->city,
                "country" => $request->country,
                "postalCode" => $request->postal_code,
                "tags" => [
                    "clinician",
                ],
            ];
            $additionalInfo = [
                "province" => $request->province,
                "occupation" => $occupation_name,
                "preferred_language" => $request->preferred_language,
            ];
            (new CreateContactsToCXM())->run($data, $clinician, $additionalInfo);
        }

        return $user;
    }
}
