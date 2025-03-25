<?php

namespace App\Http\Controllers;

use App\Http\Resources\SituationResource;
use App\Models\Situation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group user suggestions
 *
 * Endpoints related to user situations management.
 */
class SituationController extends Controller
{
    /**
     * Display all situations under a specific situation.
     */

    /**
     * Get user situations details.
     *
     * Retrieve details of a user situations by their ID.
     *
     * @urlParam id int required The ID of the user situations. Example: 1
     *
     * @response 200 {
     *"success": true,
     *"data": [
     *    {
     *        "id": 2,
     *        "title": "Gate man was arrest",
     *        "description": "He's am going through a lot",
     *        "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *        "created_at": "2025-01-21T11:09:12.000000Z"
     *    },
     *    {
     *        "id": 3,
     *        "title": "Hello mimi lena",
     *        "description": "Call me when you can",
     *        "image": "/storage/images/YC2W1ITiioam6AfvVp0JScuZBXsunRJnLzeQTp4X.png",
     *        "created_at": "2025-01-21T11:25:36.000000Z"
     *    },
     *    {
     *        "id": 4,
     *        "title": "Does not go to school",
     *        "description": "yes i don't",
     *        "image": null,
     *        "created_at": "2025-01-21T11:26:29.000000Z"
     *    }
     *]
     *}
     * @response 404 {
     *"success": false,
     *"message": "No Situation available"
     *}
     */
    public function index()
    {
        $situation = Situation::with(['user'])->get();
        if ($situation->count() > 0) {
            return SituationResource::collection($situation);
        } else {
            return response()->json(['message' => 'No situation available'], 200);
        }
    }

    /**
     * Store a new situation in the database.
     */

    /**
     * Post user situation details.
     *
     * Retrieve details of a user suggestions by their ID.
     *
     * @urlParam id int required The ID of the user suggestions. Example: 1
     *
     * @response 200 {
     *"message": "Situation created successfully",
     *"data": {
     *    "id": 5,
     *    "title": "Had an arguement with the police",
     *    "description": "yes i dis",
     *    "image": null,
     *    "created_at": "2025-01-24T13:31:21.000000Z"
     *}
     *}
     * @response 401 {
     *"message": "Unauthenticated."
     *}
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:2000',
            'description' => 'required',
            'image' => 'nullable|file|max:51200',
            'is_sensitive' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),

            ], 422);
        }

        $situation = Situation::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'is_sensitive' => $request->is_sensitive ?? false,
        ]);

        if ($request->hasFile('image')) {
            try {
                $path = Storage::url($request->file('image')->store('images', 'public'));
                $situation->image = $path;
                $situation->save();
            } catch (\Exception $e) {
                logger()->error('File upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Situation created successfully',
            'data' => new SituationResource($situation),
        ], 200);

    }

    /**
     * Display a specific situation.
     */

    /**
     * Get user situation details.
     *
     * Retrieve details of a situation suggestions by their ID.
     *
     * @urlParam id int required The ID of the situation suggestions. Example: 1
     *
     * @response 200 {
     *"success": true,
     *"data": {
     *"data": {
     *    "id": 5,
     *    "title": "Had an arguement with the police",
     *    "description": "yes i dis",
     *    "image": null,
     *    "created_at": "2025-01-24T13:31:21.000000Z"
     *}
     *}
     * @response 404 {
     *"success": false,
     *"message": "Situation not found"
     *}
     */
    public function show(int $id)
    {
        $situation = Situation::find($id);

        if (! $situation) {
            return response()->json([
                'success' => false,
                'message' => 'Situation not found',
            ], 404);
        }

        return new SituationResource($situation);
    }

    /**
     * Update a situation in the database.
     */

    /**
     * Put user situation details.
     *
     * Retrieve details of a user situation by their ID.
     *
     * @urlParam id int required The ID of the user situation. Example: 1
     *
     * @response 200 {
     *"message": "suggestion updated successfully",
     *"data": {
     *    "id": 25,
     *    "answer": "You have to go to call your lawyer",
     *    "image": null,
     *    "created_at": "2025-01-24T09:22:02.000000Z",
     *    "lawyer": {
     *        "id": 3,
     *        "name": "Sandra",
     *        "email": "sandra@gmail.com",
     *        "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *        "matricule": "bla bla bla"
     *    },
     *    "situation": {
     *        "id": 2,
     *        "title": "Gate man was arrest",
     *        "description": "He's am going through a lot",
     *        "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *        "created_at": "2025-01-21T11:09:12.000000Z"
     *    }
     *}
     *}
     * @response 404 {
     *"success": false,
     *"message": "Suggestion not found"
     *}
     */
    public function update(Request $request, int $id)
    {
        $situation = Situation::find($id);
        logger($situation);
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:2000',
            'description' => 'sometimes',
            'image' => 'nullable|file|max:51200',
            'is_sensitive' => 'boolean',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),

            ], 422);
        }

        $situation->update([
            'title' => $request->title ?? $situation->title,
            'description' => $request->description ?? $situation->description,
            'is_sensitive' => $request->is_sensitive ?? $situation->is_sensitive,
        ]);

        
        // if ($request->hasFile('image')) {
        //     try {
        //         $path = Storage::url($request->file('image')->store('images', 'public'));
        //         $situation->image = $path;
        //         $situation->save();
        //     } catch (\Exception $e) {
        //         logger()->error('File upload failed', [
        //             'error' => $e->getMessage(),
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }
        // }

$imagePath = null;
if($request->hasFile('image')) {
    $image= $request->file('image');
    $relativePath = $image->store('images', 'public');
    $imagePath = url('storage/' . $relativePath);
}

            if ($request->hasFile('image')) {
                try {
                    $path = asset($imagePath) ;
                    $situation->image = $path;
                    $situation->save();
                } catch (\Exception $e) {
                    logger()->error('File upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }


        return response()->json([
            'message' => 'Situation updated successfully',
            'data' => new SituationResource($situation),
        ], 200);

    }

    public function destroy(int $id)
    {
        $situation = Situation::find($id);
        logger($situation);
        $situation->delete();

        return response()->json([
            'message' => 'Situation deleted successfully',
        ], 200);
    }

    public function search()
    {
        try {

            $query = Situation::query();

            if (request()->has('search')) {
                $search = request()->query('search');

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
            }

            $situations = $query->get();

            return response()->json([
                'success' => true,
                'data' => SituationResource::collection($situations),

            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
}
