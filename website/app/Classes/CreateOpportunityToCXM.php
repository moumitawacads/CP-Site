<?php

namespace App\Classes;

use App\Traits\InteractWithCXMCalendarTrait;

class CreateOpportunityToCXM
{
    use InteractWithCXMCalendarTrait;

    public function run($data, $source = null, $additionalInfo = null)
    {
        $response = $this->createOpportunity($data, $additionalInfo);
        $source->opportunity_id = isset($response['id']) ? $response['id'] : null;
        $source->save();
    }
}
