<?php

namespace App\Http\Controllers;

use App\Classes\GenerateCode;
use App\Http\Resources\UserResource;
use App\Mail\LoginVerificationMail;
use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request, $id)
    {
        $user = User::find($id);
        return new UserResource($user->load(['subscriptions', 'virtualLanguageInstructionAgreed', 'gdprAgreed']));
    }

    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Determine if the login input is an email or phone number
        $loginType = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // Attempt login using the identified type and password
        $credentials = [
            $loginType => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // administrator account check
            if ($user->role_slug == 'administrator') {
                return response()->json([
                    'message' => 'Admin Login Successfully',
                    'user' => array_merge($user->toArray(), ["access_token" => $user->createToken("token-key")->plainTextToken]),
                ], 200);
            } else {
                return response()->json([
                    'message' => 'You are not authorized to access this resource',
                ], 403);
            }
        } else {
            return response()->json(["message" => "Invalid Credentials"], 400);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Determine if the login input is an email or phone number
        $loginType = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // Attempt login using the identified type and password
        $credentials = [
            $loginType => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $currentDateTime = Carbon::now();

            // $date = Carbon::parse($user->verification_expires_at);
            // $diff = $currentDateTime->diffInMinutes($date);
            // dd($currentDateTime, $date, $diff);
            // if ($diff < 5) {
            //     return response()->json([
            //         'message' => 'Too many requests in less than 5 minutes',
            //     ], 400);
            // }

            // administrator account check
            if ($user->role_slug == 'administrator') {
                return response()->json([
                    'message' => 'Administrator account cannot be used for this operation',
                ], 400);
            }

            // Check if the user has an active subscription for any of the plans
            $hasActiveSubscription = $user->subscriptions()
                ->where('name', 'default')
                ->where('stripe_status', 'active')
                ->exists();

            if (!$hasActiveSubscription) {
                return response()->json([
                    'message' => 'Your subscription is inactive. Please renew to access this resource.'
                ], 403);
            }

            // verfication code generate
            $verification_code = GenerateCode::run(9, true);


            $expire_at = $currentDateTime->addMinutes(15);
            // verification code update on users table
            $user->verification_code = $verification_code;
            $user->verification_status = 'active';
            $user->verification_expires_at = $expire_at;
            $user->save();

            // mail send to learner
            $data = [
                'name' => $user->fullname,
                'message' => 'This is your verification code: ',
                'code' => $verification_code
            ];
            Mail::to($user->email)->send(new LoginVerificationMail($data));
            return response()->json([
                "message" => "Verification code sent to your email address",
                "user" => new UserResource($user)
            ], 200);
        } else {
            return response()->json(["message" => "Invalid Credentials"], 400);
        }
    }

    public function verifyCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $date_expiration_date = Carbon::parse($user->verification_expires_at);
        $current_time = Carbon::now();
        $diff = $date_expiration_date->diffInMinutes($current_time);
        if ($diff <= 15) {
            if ($user->verification_status == 'active') {
                if ($user->verification_code == $request->verification_code) {
                    $user->verification_status = "used";
                    $user->save();

                    // Limit active sessions
                    $maxDevices = 3; // Set your device limit here
                    if ($user->sessions()->count() >= $maxDevices) {
                        // Or return an error
                        return response()->json(['message' => 'Maximum device limit reached'], 403);
                    }

                    $token = $user->createToken("token-key")->plainTextToken;

                    // Create a session entry
                    $user->sessions()->create([
                        'device_name' => $request->device_name ?? 'Unknown Device',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'token' => $token,
                        'last_active_at' => now(),
                    ]);

                    return response()->json([
                        "message" => "Login Successfully",
                        "user" => array_merge($user->toArray(), ["access_token" => $token]),
                    ], 200);
                } else {
                    return response()->json([
                        'status' => "Failed",
                        'message' => "Invalid Verification Code",
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => "Failed",
                    'message' => "This code is already used",
                ], 400);
            }
        } else {
            return response()->json([
                'status' => "Failed",
                'message' => "Your code has expired. Please request a new code",
            ], 400);
        }
    }

    public function addAdditionalComments(Request $request, $id)
    {
        $user = User::where('id', $id)->update(['additional_comments' => Crypt::encryptString($request->additional_comments)]);

        return response()->json(['message' => 'Additional comments added successfully']);
    }

    public function updateViewed(Request $request, $id)
    {
        $clinicianLearner = ClinicianLearner::where('clinician_id', $request->clinician_id)->where('learner_id', $id)->first();
        $clinicianLearner->assessment_viewed = $request->assessment_viewed;
        $clinicianLearner->literacy_viewed = $request->literacy_viewed;
        $clinicianLearner->homework_helper_viewed = $request->homework_helper_viewed;
        $clinicianLearner->save();

        return response()->json([
            'message' => 'Updated view',
            'user' => $clinicianLearner
        ]);
    }

    public function updateViewedAssessment(Request $request, $id)
    {
        $clinicianLearner = ClinicianLearner::where('clinician_id', $request->clinician_id)->where('learner_id', $id)->first();
        $clinicianLearner->assessment_viewed = $request->assessment_viewed;
        $clinicianLearner->save();

        return response()->json([
            'message' => 'Updated assessment view',
            'user' => $clinicianLearner
        ]);
    }

    public function updateViewedLiteracy(Request $request, $id)
    {
        $clinicianLearner = ClinicianLearner::where('clinician_id', $request->clinician_id)->where('learner_id', $id)->first();
        $clinicianLearner->literacy_viewed = $request->literacy_viewed;
        $clinicianLearner->save();

        return response()->json([
            'message' => 'Updated literacy view',
            'user' => $clinicianLearner
        ]);
    }

    public function updateViewedHomeworkHelper(Request $request, $id)
    {
        $clinicianLearner = ClinicianLearner::where('clinician_id', $request->clinician_id)->where('learner_id', $id)->first();
        $clinicianLearner->homework_helper_viewed = $request->homework_helper_viewed;
        $clinicianLearner->save();

        return response()->json([
            'message' => 'Updated homework helper view',
            'user' => $clinicianLearner
        ]);
    }

    public function getCalendarWebhookResponse(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        return response()->json(['message' => 'Calendar Webhook Response', 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->last_login_datetime = Carbon::parse($request->login_time)->format('Y-m-d H:i:s');
        $user->last_logout_datetime = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        $token = $request->bearerToken();
        $user->sessions()->where('token', $token)->delete();

        return response()->json(['message' => 'Logout Successfully']);
    }
}
