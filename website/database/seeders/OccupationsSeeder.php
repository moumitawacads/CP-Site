<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Occupation;

class OccupationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Occupation::insert([
            [
                "name" => "SLP",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "CDA",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Teacher",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Service Provider",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Educational Assistant",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Other Professional(s) (DELETED Support Teacher)",
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }
}
