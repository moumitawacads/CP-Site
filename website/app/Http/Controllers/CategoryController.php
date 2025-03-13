<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Category::query();
        $category = $query->get();

        return CategoryResource::collection($category);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'message' => 'Added successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $category->name = $request->name;
        $category->save();

        return response()->json([
            'message' => 'Updated successfully',
            'data' => new CategoryResource($category)
        ], 200);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Deleted successfully',
        ], 200);
    }
}
