<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddMoreServicePricePlans extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $miniArticAssessment = Plan::where('name', 'Mini Artic Assessment')->first();
        $miniLiteracyDiagnostic = Plan::where('name', 'Mini Literacy Diagnostic')->first();
        $groupCoaching = Plan::where('name', 'Group Coaching')->first();

        PlanPrice::insert([
            [
                "plan_id" => $miniArticAssessment->id,
                "stripe_product_id" => "prod_RS6MvJluEiXMi1",
                "stripe_price_id" => "price_1QZC9PC1Ha0VowUISKlGNlPd",
                "billing_period" => "annual",
                "price" => "220"
            ],
            [
                "plan_id" => $miniLiteracyDiagnostic->id,
                "stripe_product_id" => "prod_RS6MoenSWf1VCJ",
                "stripe_price_id" => "price_1QZC9uC1Ha0VowUIt9aLDQVH",
                "billing_period" => "annual",
                "price" => "220"
            ],
            [
                "plan_id" => $groupCoaching->id,
                "stripe_product_id" => "prod_RS6NtYS6hiBBGB",
                "stripe_price_id" => "price_1QZCALC1Ha0VowUIxnPrcakv",
                "billing_period" => "annual",
                "price" => "250"
            ],
        ]);
    }
}
