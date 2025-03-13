<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'fullname' => 'Liz Cripps',
                'email' => 'lcripps@wacadsgroup.com',
                'phone_number' => '9988776655',
                'role_slug' => 'administrator',
                'password' => Hash::make('Format@1'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
