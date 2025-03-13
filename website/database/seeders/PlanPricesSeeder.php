<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $miniAssessment = Plan::where('name', 'Mini Assessment')->first();
        $assessment = Plan::where('name', 'Assessment')->first();
        $existingAssessment = Plan::where('name', 'Existing Assessment')->first();
        $consulting = Plan::where('name', 'Consulting')->first();
        $basic = Plan::where('name', 'Basic')->first();
        $professional = Plan::where('name', 'Professional')->first();
        $professionalPlus = Plan::where('name', 'Professional Plus')->first();
        $homeworkHelper = Plan::where('name', 'Homework Helper')->first();

        PlanPrice::insert([
            [
                "plan_id" => $miniAssessment->id,
                "stripe_product_id" => "prod_RQDEWJ88Whij9h",
                "stripe_price_id" => "price_1QXMo0C1Ha0VowUIQNqsSEd1",
                "billing_period" => "monthly",
                "price" => "139"
            ],
            [
                "plan_id" => $assessment->id,
                "stripe_product_id" => "prod_RQDF0rV9uamGOx",
                "stripe_price_id" => "price_1QXMomC1Ha0VowUINKTBA3ev",
                "billing_period" => "annual",
                "price" => "220"
            ],
            [
                "plan_id" => $existingAssessment->id,
                "stripe_product_id" => "prod_RQDGlpmcl0b4GI",
                "stripe_price_id" => "price_1QXMpqC1Ha0VowUIo25pKsjc",
                "billing_period" => "annual",
                "price" => "220"
            ],
            [
                "plan_id" => $consulting->id,
                "stripe_product_id" => "prod_RQDH4Hdlwu49Gw",
                "stripe_price_id" => "price_1QXMqUC1Ha0VowUIi3LApoCe",
                "billing_period" => "annual",
                "price" => "250"
            ],
            [
                "plan_id" => $basic->id,
                "stripe_product_id" => "prod_RQDK9PXmGpS3Ij",
                "stripe_price_id" => "price_1QXMtsC1Ha0VowUIGuPaiV8Q",
                "billing_period" => "monthly",
                "price" => "19.99"
            ],
            [
                "plan_id" => $basic->id,
                "stripe_product_id" => "prod_RQDLGNccQp0hct",
                "stripe_price_id" => "price_1QXMuVC1Ha0VowUIaljnUXN8",
                "billing_period" => "annual",
                "price" => "150"
            ],
            [
                "plan_id" => $professional->id,
                "stripe_product_id" => "prod_RQDLzR7WCxH2vx",
                "stripe_price_id" => "price_1QXMvFC1Ha0VowUIyF1D9B96",
                "billing_period" => "monthly",
                "price" => "24"
            ],
            [
                "plan_id" => $professional->id,
                "stripe_product_id" => "prod_RQDMFQ1M3k9VHf",
                "stripe_price_id" => "price_1QXMvoC1Ha0VowUIVmYOkjwF",
                "billing_period" => "annual",
                "price" => "240"
            ],
            [
                "plan_id" => $professionalPlus->id,
                "stripe_product_id" => "prod_RQDMLHVM4F2SpL",
                "stripe_price_id" => "price_1QXMwGC1Ha0VowUI0N96B2QV",
                "billing_period" => "monthly",
                "price" => "29"
            ],
            [
                "plan_id" => $professionalPlus->id,
                "stripe_product_id" => "prod_RQDNYsZDEmOuiM",
                "stripe_price_id" => "price_1QXMwkC1Ha0VowUIaSyGXhsP",
                "billing_period" => "annual",
                "price" => "275"
            ],
            [
                "plan_id" => $homeworkHelper->id,
                "stripe_product_id" => "prod_RQDOuuhyxL30si",
                "stripe_price_id" => "price_1QXMxQC1Ha0VowUIJu8Ln4M7",
                "billing_period" => "monthly",
                "price" => "25"
            ],
            [
                "plan_id" => $homeworkHelper->id,
                "stripe_product_id" => "prod_RQDOkEzxGvXjSS",
                "stripe_price_id" => "price_1QXMxpC1Ha0VowUIkqyIBB3O",
                "billing_period" => "annual",
                "price" => "200"
            ]
        ]);
    }
}
