<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFound;
use App\Http\Resources\LearnerCharacterCustomizationResource;
use App\Http\Resources\LearnerEncryptDataResource;
use App\Http\Resources\LearnerResource;
use App\Http\Resources\LearnerSessionResource;
use App\Http\Resources\UnityClinicianWebhookResource;
use App\Http\Resources\UnityParentWebhookResource;
use App\Http\Resources\UnityUserWebhookResource;
use App\Models\Clinician;
use App\Models\ClinicianLearner;
use App\Models\Learner;
use App\Models\LearnerCharacterCustomization;
use App\Models\LearnerEncryptData;
use App\Models\LearnerGameLevel;
use App\Models\LearnerSession;
use App\Models\LearnerSessionGame;
use App\Models\Parents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UnityWebhookController extends Controller
{
    protected $resourceNotFound = "Resource not found";

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

            $user['type'] = 'login';
            $user["access_token"] = $user->createToken("token-key")->plainTextToken;

            return response()->json([
                "message" => "Login Successfully",
                "user" => new UnityUserWebhookResource($user),
            ], 200);
        } else {
            return response()->json(["message" => "Invalid Credentials"], 400);
        }
    }

    public function getUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'role_slug' => 'required|exists:users,role_slug',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($request->role_slug == 'clinician') {
            $user = Clinician::with(['user', 'learners', 'user.subscriptions', 'occupation', 'settings'])->where('user_id', $request->id)->first();
        } else if ($request->role_slug == 'parents') {
            $user = Parents::with(['user', 'user.subscriptions'])->where('user_id', $request->id)->first();
        }

        return response()->json([
            'data' => $request->role_slug == 'clinician' ? new UnityClinicianWebhookResource($user) : new UnityParentWebhookResource($user)
        ]);
    }

    public function getLearners($id)
    {
        $clinician = Clinician::find($id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }
        $learnerData = [];
        $clincianLearners = ClinicianLearner::where('clinician_id', $id)->get();
        if (count($clincianLearners) > 0) {
            foreach ($clincianLearners as $clincianLearner) {
                if ($clincianLearner['learner_id'] && $clincianLearner['learner_encrypt_data_id']) {
                    $learner = Learner::where('id', $clincianLearner['learner_id'])->first();
                    $learnerEnceyptData = LearnerEncryptData::where('id', $clincianLearner['learner_encrypt_data_id'])->first();
                    $learnerData[] = [
                        'learner_data' => new LearnerResource($learner),
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'homework_helper_flag' => $clincianLearner->homework_helper_flag,
                        'parent_approval_received' => $clincianLearner->parent_approval_received,
                        'id' => $clincianLearner->id
                    ];
                } else {
                    $learnerEnceyptData = LearnerEncryptData::where('id', $clincianLearner['learner_encrypt_data_id'])->first();
                    $learnerData[] = [
                        'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                        'homework_helper_flag' => $clincianLearner->homework_helper_flag,
                        'parent_approval_received' => $clincianLearner->parent_approval_received,
                        'id' => $clincianLearner->id
                    ];
                }
            }
        } else {
            $learnerData = "No Learners associated with you";
        }

        return response()->json([
            'data' => $learnerData
        ]);
    }

    public function getLearnerDetails($id, $learner_id)
    {
        // checking clinician is exists or not
        $clinician = Clinician::find($id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        // checking learner is exists or not
        $learner = Learner::find($learner_id);
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerData = null;
        $clincianLearner = ClinicianLearner::where('clinician_id', $id)->where('learner_id', $learner_id)->first();
        if ($clincianLearner) {
            if ($clincianLearner['learner_id'] && $clincianLearner['learner_encrypt_data_id']) {
                $learnerEnceyptData = LearnerEncryptData::where('id', $clincianLearner['learner_encrypt_data_id'])->first();
                $learnerData = [
                    'learner_data' => new LearnerResource($learner->load(['characterCustomization', 'sessions'])),
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'homework_helper_flag' => $clincianLearner->homework_helper_flag,
                    'parent_approval_received' => $clincianLearner->parent_approval_received,
                    'id' => $clincianLearner->id
                ];
            } else {
                $learnerEnceyptData = LearnerEncryptData::where('id', $clincianLearner['learner_encrypt_data_id'])->first();
                $learnerData = [
                    'learner_encrypt_data' => new LearnerEncryptDataResource($learnerEnceyptData),
                    'homework_helper_flag' => $clincianLearner->homework_helper_flag,
                    'parent_approval_received' => $clincianLearner->parent_approval_received,
                    'id' => $clincianLearner->id
                ];
            }
        } else {
            $learnerData = "No Learners associated with you";
        }

        return response()->json([
            'data' => $learnerData
        ]);
    }

    public function updateLearnerDetails(Request $request, $id, $learner_id)
    {
        // checking clinician is exists or not
        $clinician = Clinician::find($id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        // checking learner is exists or not
        $learner = Learner::find($learner_id);
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerCharacterCustomization = LearnerCharacterCustomization::firstOrNew([
            'learner_id' => $learner->id,
        ]);
        $learnerCharacterCustomization->selected_bodycolor = $request->Selected_bodycolor;
        $learnerCharacterCustomization->selected_eyecolor = $request->Selected_eyecolor;
        $learnerCharacterCustomization->selected_eyebrow = $request->Selected_eyebrow;
        $learnerCharacterCustomization->selected_hair = $request->Selected_hair;
        $learnerCharacterCustomization->selected_upperwear = $request->Selected_upperwear;
        $learnerCharacterCustomization->selected_pant = $request->Selected_pant;
        $learnerCharacterCustomization->selected_backpack = $request->Selected_backpack;
        $learnerCharacterCustomization->selected_shoe = $request->Selected_shoe;
        $learnerCharacterCustomization->selected_hat = $request->Selected_hat;
        $learnerCharacterCustomization->selected_glove = $request->Selected_glove;
        $learnerCharacterCustomization->selected_glasses = $request->Selected_glasses;
        $learnerCharacterCustomization->save();

        return response()->json([
            'message' => 'Learner Character Customization data updated successfully',
            'data' => new LearnerCharacterCustomizationResource($learnerCharacterCustomization)
        ]);
    }

    public function getLearnerSession(Request $request, $id, $learner_id)
    {
        $validator = Validator::make($request->all(), [
            'session_start_time' => 'required',
            'atrium' => 'required',
            'world' => 'required',
            'target_sound' => 'required',
            'pick_possition' => 'required',
            'pick_syllabus' => 'required',
            'spell_word' => 'required',
            'game_details' => 'required',
            'session_end_time' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // checking clinician is exists or not
        $clinician = Clinician::find($id);
        if (!$clinician) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        // checking learner is exists or not
        $learner = Learner::find($learner_id);
        if (!$learner) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $learnerSession = new LearnerSession();
        $learnerSession->learner_id = $learner->id;
        $learnerSession->session_start_time = Carbon::parse($request->session_start_time)->format('Y-m-d H:i:s');
        $learnerSession->atrium = $request->atrium;
        $learnerSession->world = $request->world;
        $learnerSession->target_sound = $request->target_sound;
        $learnerSession->pick_possition = $request->pick_possition;
        $learnerSession->pick_syllabus = $request->pick_syllabus;
        $learnerSession->spell_word = $request->spell_word;
        $learnerSession->coins_earned = $request->coins_earned;
        $learnerSession->session_end_time = Carbon::parse($request->session_end_time)->format('Y-m-d H:i:s');
        $learnerSession->save();

        $gameDetails = $request->game_details;
        if (count($gameDetails) > 0) {
            foreach ($gameDetails as $gameDetail) {
                $learnerSessionGames = new LearnerSessionGame();
                $learnerSessionGames->learner_session_id = $learnerSession->id;
                $learnerSessionGames->game_name = $gameDetail['game_name'];
                $learnerSessionGames->game_score = $gameDetail['game_score'];
                $learnerSessionGames->number_of_attempts = $gameDetail['no_of_attempt'];
                $learnerSessionGames->save();

                if (isset($gameDetail['game_levels']) && count($gameDetail['game_levels']) > 0) {
                    foreach ($gameDetail['game_levels'] as $gameLevel) {

                        $audioFilePath = "";
                        $audioFile = $gameLevel['audio'];
                        $audioFileName = time() . rand(1, 9999) . '.mp3';
                        $audioFilePath = $audioFile->storeAs('audio', $audioFileName, 'public');

                        $learnerGameLevel = new LearnerGameLevel();
                        $learnerGameLevel->learner_session_game_id = $learnerSessionGames->id;
                        $learnerGameLevel->audio_file_path = $audioFilePath;
                        $learnerGameLevel->answer_type = $gameLevel['answer_type'];
                        $learnerGameLevel->words = isset($gameLevel['words']) ? $gameLevel['words'] : null;
                        $learnerGameLevel->two_words_phrase = isset($gameLevel['two_words_phrase']) ? $gameLevel['two_words_phrase'] : null;
                        $learnerGameLevel->silly_sentence = isset($gameLevel['silly_sentence']) ? $gameLevel['silly_sentence'] : null;
                        $learnerGameLevel->story = isset($gameLevel['story']) ? $gameLevel['story'] : null;
                        $learnerGameLevel->save();
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Learner game session added successfully',
            'data' => new LearnerSessionResource($learnerSession->load(['sessionGames', 'sessionGames.learnerGameLevels']))
        ]);
    }
}
