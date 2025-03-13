<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::all();

        return PlanResource::collection($plans->load('prices', 'features'));
    }

    public function planPrice(Request $request)
    {
        $planPrice = PlanPrice::where('stripe_price_id', $request->stripe_price_id)->first();

        return response()->json([$planPrice->load(['plan', 'plan.features'])]);
    }
}
