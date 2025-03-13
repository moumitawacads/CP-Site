<?php

namespace App\Http\Controllers;

use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Http\Request;

class OccupationController extends Controller
{
    public function index()
    {
        $occupations = Occupation::all();

        return response()->json([
            'data' => OccupationResource::collection($occupations)
        ]);
    }
}
