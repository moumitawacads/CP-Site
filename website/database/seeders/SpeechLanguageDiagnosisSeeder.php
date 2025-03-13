<?php

namespace Database\Seeders;

use App\Models\SpeechLanguageDiagnosis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpeechLanguageDiagnosisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SpeechLanguageDiagnosis::insert([
            [
                "name" => "Developmental Language Disorder (DLD)",
                "slug" => "developmental-language-disorder",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Neurodivergent",
                "slug" => "neurodivergent",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Articulation Delay/Disorder",
                "slug" => "articulation-delay-or-disorder",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Autism Spectrum Disorder",
                "slug" => "autism-spectrum-disorder",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Apraxia of speech",
                "slug" => "apraxia-of-speech",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Stuttering",
                "slug" => "stuttering",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Developmental Disability",
                "slug" => "developmental-disability",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Intellectual Disability",
                "slug" => "intellectual-disability",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Genetic Disorder",
                "slug" => "genetic-disorder",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "ADHD",
                "slug" => "adhd",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "ABI(Accquired Brain Injury)",
                "slug" => "abi",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Physical Challenges",
                "slug" => "physical-challenges",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Other",
                "slug" => "other",
                "created_at" => now(),
                "updated_at" => now(),
            ]
        ]);
    }
}
