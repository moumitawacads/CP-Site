<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFound;
use App\Http\Resources\WhatsNewResource;
use App\Models\WhatsNew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WhatsNewController extends Controller
{
    protected $resourceNotFound = "Resource not found";

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Request $request)
    {
        $paginate = $request->input('per_page', 20);
        $query = WhatsNew::with(['author', 'category'])->orderBy('created_at', 'desc');
        if ($request->has('status') && $request->status != 'all') {
            $query = $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category != 'all') {
            $query = $query->where('category_id', $request->category);
        }

        if ($request->has('category_name') && $request->category_name != '') {
            $query = $query->whereHas('category', function ($catQuery) use ($request) {
                $catQuery->where('name', 'like', $request->category_name);
            });
            $paginate = $request->input('per_page', 200);
        }

        $whatsNew = $query->paginate($paginate);

        return WhatsNewResource::collection($whatsNew);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required',
            'file' => 'required|file',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($request->hasFile('file')) {
            // Store file in the `public/whats-new` directory
            $filePath = $request->file('file')->store('whats-new', 'public');
        }

        $url = Storage::url($filePath);

        $whatsNew = new WhatsNew();
        $whatsNew->title = $request->title;
        $whatsNew->content = json_encode($request->content);
        $whatsNew->file_url = App::environment('local') ? url($url) : url('public/' . $url);
        $whatsNew->status = $request->status;
        $whatsNew->category_id = $request->category_id;
        $whatsNew->author_id = Auth::user()->id;
        $whatsNew->published_at = $request->status == 'published' ? now() : null;
        $whatsNew->save();

        return response()->json([
            'message' => 'Added successfully',
            'data' => new WhatsNewResource($whatsNew)
        ], 201);
    }

    public function show($id)
    {
        $whatsNew = WhatsNew::with(['author', 'category'])->find($id);
        if (!$whatsNew) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        return new WhatsNewResource($whatsNew);
    }

    public function update(Request $request, $id)
    {
        $whatsNew = WhatsNew::find($id);
        if (!$whatsNew) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        if ($request->hasFile('file')) {
            // Store file in the `public/whats-new` directory
            $filePath = $request->file('file')->store('whats-new', 'public');
            $url = Storage::url($filePath);
            $whatsNew->file_url = App::environment('local') ? url($url) : url('public/' . $url);
        }

        $whatsNew->title = $request->title;
        $whatsNew->content = json_encode($request->content);
        $whatsNew->status = $request->status;
        $whatsNew->category_id = $request->category_id;
        $whatsNew->published_at = $request->status == 'published' ? now() : null;
        $whatsNew->save();

        return response()->json([
            'message' => 'Updated successfully',
            'data' => new WhatsNewResource($whatsNew)
        ], 200);
    }

    public function delete($id)
    {
        $whatsNew = WhatsNew::find($id);
        if (!$whatsNew) {
            throw new ResourceNotFound($this->resourceNotFound, 404);
        }

        $whatsNew->delete();

        return response()->json([
            'message' => 'Deleted successfully',
        ], 200);
    }

    public function fileUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($request->hasFile('file')) {
            // Store file in the `public/whats-new` directory
            $filePath = $request->file('file')->store('whats-new', 'public');
        }
        $url = Storage::url($filePath);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file_path' => App::environment('local') ? url($url) : url('public/' . $url) //App::environment('local') ? url($filePath) : url('public/storage/' . $filePath)
        ], 200);
    }
}
