<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // Set this in .env
        $signature = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $signature, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        Log::info('Event type => ', ['event_type' => $event['type']]);
        // Handle the event
        switch ($event['type']) {
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event['data']['object']);
                break;

            default:
                Log::info('Unhandled event type', ['event_type' => $event['type']]);
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }

    protected function handlePaymentFailed($invoice)
    {
        Log::info('Invoice payment succeeded.', ['invoice' => $invoice]);
        // Update your database or perform actions based on payment success
        // $subscriptionId = $subscription['id'];
        // $status = $subscription['status']; // e.g., 'active', 'past_due', 'canceled', 'unpaid'

        // $subscription = Subscription::where('stripe_id', $subscriptionId)->first();

        // if ($subscription) {
        //     $subscription->status = $status; // Map Stripe status to your own if necessary
        //     $subscription->save();
        // }
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        Log::info('Subscription deleted.', ['subscription' => $subscription]);
        // Update your database to mark the subscription as cancelled
        $subscriptionId = $subscription['id'];
        $status = $subscription['status']; // e.g., 'active', 'past_due', 'canceled', 'unpaid'

        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->stripe_status = $status; // Map Stripe status to your own if necessary
            $subscription->save();
        }
    }
}
