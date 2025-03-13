<?php

namespace App\Http\Controllers;

use App\Classes\ClinicianCreate;
use App\Classes\CreateOpportunityToCXM;
use App\Classes\GDPRCreate;
use App\Classes\ParentCreate;
use App\Classes\UpdateLatestActions;
use App\Classes\UpdateOpportunityToCXM;
use App\Classes\VirtualLanguageInstructionCreate;
use App\Models\Clinician;
use App\Models\Learner;
use App\Models\Parents;
use App\Models\User;
use App\Models\UserAssessment;
use App\Models\UserHomeworkHelper;
use App\Models\UserLiteracyDiagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'user_type' => 'required',
            'cart' => 'required',
            'consent_of_speech_language_form' => 'required',
            'virtual_language_instruction_form' => 'required',
            'gdpr_form' => 'required',
        ]);

        // $user = User::find(170);
        $user = $request->user_type == 'parent' ? (new ParentCreate())->run($request) : (new ClinicianCreate())->run($request);
        // insert virtual language instruction data
        VirtualLanguageInstructionCreate::run(json_decode($request->virtual_language_instruction_form, true), $user);
        // insert gdpr data
        GDPRCreate::run(json_decode($request->gdpr_form, true), $user);

        try {
            $options = [
                "name" => $request->fullname,
                "email" => $request->email,
                "phone" => $request->phone_number,
                "metadata" => [
                    "user_id" => $user->id
                ]
            ];
            // Update payment method
            $user->createOrGetStripeCustomer($options);
            $user->updateDefaultPaymentMethod($request->payment_method);

            $carts = json_decode($request->cart, true);
            if (count($carts) > 0) {
                foreach ($carts as $cart) {
                    $priceId = $cart['selectedPlanPrice']['stripe_price_id'];
                    $subscriptionName = strtolower(str_replace(" ", "_", $cart['name']));
                    $defaultPlans = ["basic", "professional", "professional_plus"];
                    if (in_array($subscriptionName, $defaultPlans)) {
                        $subscriptionName = 'default';
                    }

                    // Subscribe user to a monthly plan //price_1QZ4ESC1Ha0VowUIkLl1dMwQ
                    $subscription = $user->newSubscription($subscriptionName, $priceId)
                        // $subscription = $user->newSubscription('test_daily_product', 'price_1QZ4ESC1Ha0VowUIkLl1dMwQ')
                        ->quantity($cart['quantity'])
                        ->create($request->payment_method);

                    // Retrieve Stripe subscription details
                    $stripeSubscription = $subscription->asStripeSubscription();
                    $upcomingInvoice = $user->upcomingInvoice();

                    // Update the subscriptions table with additional details
                    $subscription->update([
                        'payment_date' => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
                        'expiry_date' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
                        'amount' => $upcomingInvoice ? $upcomingInvoice->total / 100 : null,
                        'next_payment_date' => $upcomingInvoice ? $upcomingInvoice->date()->toDateTimeString() : null,
                        'remaining_service_quantity' => $cart['quantity'],
                    ]);
                    $this->createOpportunity($cart, $user);
                }
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
                'message' => 'Subscription created successfully!',
                'status' => 'active',
                'user' => array_merge($user->toArray(), ["access_token" => $token])
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function serviceSubscribe(Request $request)
    {
        $request->validate([
            'provider_id' => 'required',
            'payment_method' => 'required',
            'user_type' => 'required',
            'cart' => 'required',
        ]);

        if ($request->user_type === 'parents') {
            $provider = Parents::find($request->provider_id);
        } else {
            $provider = Clinician::find($request->provider_id);
        }
        $user = User::where('id', $provider->user_id)->first();

        try {
            // Update payment method
            $user->updateDefaultPaymentMethod($request->payment_method);

            $carts = json_decode($request->cart, true);

            if (count($carts) > 0) {
                foreach ($carts as $cart) {
                    $priceId = $cart['selectedPlanPrice']['stripe_price_id'];
                    $subscriptionName = strtolower(str_replace(" ", "_", $cart['name']));

                    if ($request->plan_type && $request->plan_type === 'upgrade') {
                        $subscription = $user->subscription('default')->swap($priceId);
                        $this->updateOpportunity($cart, $user);

                        $this->updateLatestaction(null, $provider, "homework-helper", $cart['name'], 'upgrade');
                    } else {
                        $subscription = $user->newSubscription($subscriptionName, $priceId)
                            ->quantity($cart['quantity'])
                            ->create($request->payment_method);
                        $this->createOpportunity($cart, $user);

                        foreach ($cart['learnerOptions'] as $learnerOption) {
                            $learner = Learner::find($learnerOption["newLearnerID"]);

                            if ($cart['name'] === "Mini Artic Assessment") {
                                $tabId = "assessment";
                            } else if ($cart['name'] === "Mini Literacy Diagnostic") {
                                $tabId = "literacy-diagnostic";
                            } else if ($cart['name'] === "Homework Helper") {
                                $tabId = "homework-helper";
                            }

                            $this->updateLatestaction($learner, $provider, $tabId, $cart['name']);
                        }
                    }

                    // Retrieve Stripe subscription details
                    $stripeSubscription = $subscription->asStripeSubscription();

                    // Update the subscriptions table with additional details
                    $subscription->update([
                        'payment_date' => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
                        'expiry_date' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
                        'amount' => $cart['selectedPlanPrice']['price'],
                        'next_payment_date' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
                        'remaining_service_quantity' => 0,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Service Subscription created successfully!',
                'status' => 'active',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function createOpportunity($cart, $user)
    {
        $defaultPlans = ["Basic", "Professional", "Professional Plus"];
        if (in_array($cart["name"], $defaultPlans)) {
            $defaultPlan = $user->subscriptions('default')->whereNull('opportunity_id')->first()->toArray();
            if ($user->role_slug === 'clinician') {
                $provider = Clinician::where('user_id', $user->id)->first();
            } else {
                $provider = Parents::where('user_id', $user->id)->first();
            }

            $data = [
                "title" => $provider->user->fullname . " added " . $cart['name'] . " opportunity",
                "status" => "open",
                "email" => $provider->user->email,
                "phone" => $provider->user->phone_number,
                "monetaryValue" => $cart['selectedPlanPrice']['price'],
                "source" => "public api",
                "contactId" => $provider->contacts_id,
                "name" => $provider->user->fullname,
                "tags" => [
                    "default plan"
                ],
            ];
            $additionalInfo = [
                "plan_name" => $cart["name"],
                "plan_description" => $cart["description"]
            ];

            $serviceModel = Subscription::where('id', $defaultPlan['id'])->first();

            (new CreateOpportunityToCXM())->run($data, $serviceModel, $additionalInfo);
        } else {
            if ($cart['name'] === "Mini Artic Assessment") {
                $userServices = $user->assessments->whereNull('opportunity_id')->toArray();
            } else if ($cart['name'] === "Mini Literacy Diagnostic") {
                $userServices = $user->literacyDiagnostic->whereNull('opportunity_id')->toArray();
            } else if ($cart['name'] === "Group Coaching") {
            } else if ($cart['name'] === "Homework Helper") {
                $userServices = $user->homeworkHelpers->whereNull('opportunity_id')->toArray();
            }

            if (count($userServices) > 0) {
                foreach ($userServices as $userService) {
                    $learner = Learner::where('id', $userService['learner_id'])->first();
                    $parents = Parents::where('user_id', $userService['user_id'])->first();

                    $data = [
                        "title" => $parents->user->fullname . " added this opportunity for " . $learner->user->fullname,
                        "status" => "open",
                        "email" => $parents->user->email,
                        "phone" => $parents->user->phone_number,
                        // "assignedTo" => $learner->contacts_id,
                        "monetaryValue" => $cart['selectedPlanPrice']['price'],
                        "source" => "public api",
                        "contactId" => $learner->contacts_id,
                        "name" => $learner->user->fullname,
                        "tags" => [
                            "service"
                        ]
                    ];
                    $additionalInfo = [
                        "plan_name" => $cart["name"],
                        "plan_description" => $cart["description"]
                    ];

                    $serviceModel = $this->getUserServiceModel($cart['name'], $userService['id']);

                    (new CreateOpportunityToCXM())->run($data, $serviceModel, $additionalInfo);
                }
            }
        }
    }

    private function updateOpportunity($cart, $user)
    {
        $defaultPlans = ["Basic", "Professional", "Professional Plus"];
        if (in_array($cart["name"], $defaultPlans)) {
            $defaultPlan = $user->subscription('default');
            if ($user->role_slug === 'clinician') {
                $provider = Clinician::where('user_id', $user->id)->first();
            } else {
                $provider = Parents::where('user_id', $user->id)->first();
            }

            $data = [
                "title" => $provider->user->fullname . " upgrade to " . $cart['name'] . " opportunity",
                "status" => "open",
                "monetaryValue" => $cart['selectedPlanPrice']['price'],
                "contactId" => $provider->contacts_id,
                "tags" => [
                    "default plan"
                ],
            ];
            $additionalInfo = [
                "plan_name" => $cart["name"],
                "plan_description" => $cart["description"],
                "opportunity_id" => $defaultPlan->opportunity_id
            ];

            (new UpdateOpportunityToCXM())->run($data, $defaultPlan, $additionalInfo);
        }
    }

    private function getUserServiceModel($planName, $id)
    {
        if ($planName === "Mini Artic Assessment") {
            $model = UserAssessment::find($id);
        } else  if ($planName === "Mini Literacy Diagnostic") {
            $model = UserLiteracyDiagnostic::find($id);
        } else if ($planName === "Group Coaching") {
        } else if ($planName === "Homework Helper") {
            $model = UserHomeworkHelper::find($id);
        }

        return $model;
    }

    private function updateLatestaction($learner = null, $provider, $tab_id, $serviceName, $upgrade = null)
    {
        if ($upgrade) {
            $action_description = "You have upgraded the plan to " . $serviceName;
            $dynamic_value = ["plan_name" => $serviceName];
        } else {
            $action_description = $serviceName . " service added for " . $learner->first_name . " " . mb_substr($learner->last_name, 0, 1);
            $dynamic_value = [
                "learner_name" => $learner->first_name . " " . mb_substr($learner->last_name, 0, 1),
                "service_name" => $serviceName
            ];
        }

        UpdateLatestActions::run($provider->id, $tab_id, $action_description, $provider->user->role_slug, $dynamic_value);
    }
}
