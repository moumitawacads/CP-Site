<?php

namespace App\Classes;

use App\Models\ClinicianLearner;

class ClinicianLearnerLink
{
    public static function run($clinician_id = null, $learner_encrypt_data_id, $learner_id = null, $service_type = null)
    {
        $clinicianLearner = new ClinicianLearner();
        $clinicianLearner->clinician_id = $clinician_id;
        $clinicianLearner->learner_id = $learner_id;
        $clinicianLearner->learner_encrypt_data_id = $learner_encrypt_data_id;
        $clinicianLearner->service_type = $service_type;
        $clinicianLearner->save();

        return $clinicianLearner;
    }
}
