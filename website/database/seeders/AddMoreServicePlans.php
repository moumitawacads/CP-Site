<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddMoreServicePlans extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::insert([
            [
                "name" => "Mini Artic Assessment",
                "description" => "By purchasing this plan your learner will improve their speech sounds. You will have access to all sounds.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Mini Literacy Diagnostic",
                "description" => "During this diagnostic will assess your child’s literacy skills using standardized tools.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Group Coaching",
                "description" => "Join a supportive community, share experiences and learn from one another. There are a maximum of (3) participants in this (1) one hour session.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
