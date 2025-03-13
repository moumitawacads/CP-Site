<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFound;
use App\Http\Resources\LearnerEncryptDataResource;
use App\Models\LearnerEncryptData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LearnerEncryptDataController extends Controller
{
    protected const LEARNER = 'learner';
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = LearnerEncryptData::query();
        if ($request->has('user_id') && $request->user_id != '') {
            $query = $query->where('user_id', $request->user_id);
        }
        $learnerEncryptData = $query->paginate($request->input('per_page', 100));

        if ($learnerEncryptData->total() == 0) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        return LearnerEncryptDataResource::collection($learnerEncryptData);
    }
}
