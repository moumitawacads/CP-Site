<?php

namespace App\Classes;

use App\Models\ClinicianLatestAction;
use App\Models\ParentsLatestAction;

class UpdateLatestActions
{
    protected const CLINICIAN = 'clinician';
    protected const LEARNER = 'learner';
    protected const PARENTS = 'parents';

    public static function run($id = null, $tab_id, $action_description = null, $type, $dynamic_value)
    {
        if ($type == self::CLINICIAN) {
            $latestActions = new ClinicianLatestAction();
            $latestActions->clinician_id = $id;
        } elseif ($type == self::PARENTS) {
            $latestActions = new ParentsLatestAction();
            $latestActions->parents_id = $id;
        }
        $latestActions->tab_id = $tab_id;
        $latestActions->action_description = $action_description;
        $latestActions->dynamic_value = json_encode($dynamic_value);
        $latestActions->save();

        return $latestActions;
    }
}
