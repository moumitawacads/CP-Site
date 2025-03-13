<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpeechLanguageDiagnosisResource;
use App\Models\SpeechLanguageDiagnosis;
use Illuminate\Http\Request;

class SpeechLanguageDiagnosisController extends Controller
{
    public function index()
    {
        $speech_language_diagnosis_list = SpeechLanguageDiagnosis::all();

        return response()->json([
            'data' => SpeechLanguageDiagnosisResource::collection($speech_language_diagnosis_list)
        ]);
    }
}
