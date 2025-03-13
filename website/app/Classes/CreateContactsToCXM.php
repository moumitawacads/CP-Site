<?php

namespace App\Classes;

use App\Traits\InteractWithCXMCalendarTrait;

class CreateContactsToCXM
{
    use InteractWithCXMCalendarTrait;

    public function run($data, $source = null, $additionalInfo = null)
    {
        $response = $this->createContactsWithTag($data, $additionalInfo);
        $source->contacts_id = isset($response['contact']) ? $response['contact']['id'] : null;
        $source->save();
    }
}
