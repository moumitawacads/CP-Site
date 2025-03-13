<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            [
                "name" => "Administrator",
                "description" => "Can see everything",
                "slug" => "administrator",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Clinician",
                "description" => "Can see only clinicians related data",
                "slug" => "clinician",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Learner",
                "description" => "Can see only learners related data",
                "slug" => "learner",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Parents",
                "description" => "Can see only parents related data",
                "slug" => "parents",
                "created_at" => now(),
                "updated_at" => now(),
            ]
        ]);
    }
}
