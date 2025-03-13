<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
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
                "name" => "Mini Assessment",
                "description" => "By purchasing this plan your learner will improve their speech sounds. You will have access to all sounds.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Assessment",
                "description" => "A full one (1) hour assessment of your child’s articulation and literary skills is completed using standardized tools.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Existing Assessment",
                "description" => "Bring an assessment from another provider and have our SLPs review and provide feedback.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Consulting",
                "description" => "This is a 45 minute assessment.",
                "type" => "services",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Basic",
                "description" => "By purchasing this plan your learner will improve their speech sounds. You will have access to all sounds.",
                "type" => "individual",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Professional",
                "description" => "By purchasing this professional plan you will be able to have full access to all sounds, phonological processes and vowels.",
                "type" => "individual",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Professional Plus",
                "description" => "By purchasing this professional plus plan you will have full access to all sounds, phonological processes, vowels and homework helper.",
                "type" => "individual",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Homework Helper",
                "description" => "Accelerate your learning opportunities with direct homework assigned from your Chattiepants Clinician.<b><i> To take advantage of this premium learning opportunity, an assessment must conducted by a ChattiePants Speech and Language Pathologist.</i></b>",
                "type" => "individual",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
