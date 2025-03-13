<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\InteractWithCXMCalendarTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarWebhookController extends Controller
{
    use InteractWithCXMCalendarTrait;

    public function handleWebhook(Request $request)
    {
        Log::info('Calendar Response content=>' . $request->getContent());

        $payload = $request->getContent();
        // Decode JSON into an associative array
        $data = json_decode($payload, true);
        // Log::info($data['first_name']);

        $email = $data['email'];
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->removeAppointmentToCXM($data['calendar']['appointmentId']);
        } else {
            $user->scheduled_meeting_start_datetime = $data['calendar']['startTime'];
            $user->scheduled_meeting_end_datetime = $data['calendar']['endTime'];
            $user->scheduled_meeting_timezone = $data['calendar']['selectedTimezone'];
            $user->calendar_webhook_response = $data;
            $user->save();
        }

        return response()->json(['message' => 'Calendar Webhook handled'], 200);
    }
}
