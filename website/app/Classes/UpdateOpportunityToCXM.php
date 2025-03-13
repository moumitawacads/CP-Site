<?php

namespace App\Classes;

use App\Traits\InteractWithCXMCalendarTrait;

class UpdateOpportunityToCXM
{
    use InteractWithCXMCalendarTrait;

    public function run($data, $source = null, $additionalInfo = null)
    {
        $response = $this->updateOpportunity($data, $additionalInfo);
    }
}
