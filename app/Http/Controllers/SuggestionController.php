<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuggestionResource;
use App\Models\Situation;
use App\Models\Suggestion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group lawyer suggestions
 *
 * Endpoints related to lawyer suggestions management.
 */
class SuggestionController extends Controller
{
    /**
     * Display all suggestions under a specific situation.
     */

    /**
     * Get lawyer suggestions details.
     *
     * Retrieve details of a lawyer suggestions by their ID.
     *
     * @urlParam id int required The ID of the lawyer suggestions. Example: 1
     *
     * @response 200 {
     * "success": true,
     * "message": "Suggestions successfully retrieved",
     * "data": [
     *     {
     *         "id": 20,
     *         "answer": "This is not correct",
     *         "image": null,
     *         "created_at": "2025-01-22T15:05:49.000000Z",
     *         "lawyer": {
     *             "id": 3,
     *             "name": "Sandra",
     *             "email": "sandra@gmail.com",
     *             "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *             "matricule": "bla bla bla"
     *         },
     *         "situation": {
     *             "id": 2,
     *             "title": "Gate man was arrest",
     *             "description": "He's am going through a lot",
     *             "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *             "created_at": "2025-01-21T11:09:12.000000Z"
     *         }
     *     },
     *     {
     *         "id": 21,
     *         "answer": "This is not correct",
     *         "image": null,
     *         "created_at": "2025-01-22T15:06:49.000000Z",
     *         "lawyer": {
     *             "id": 3,
     *             "name": "Sandra",
     *             "email": "sandra@gmail.com",
     *             "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *             "matricule": "bla bla bla"
     *         },
     *         "situation": {
     *             "id": 2,
     *             "title": "Gate man was arrest",
     *             "description": "He's am going through a lot",
     *             "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *             "created_at": "2025-01-21T11:09:12.000000Z"
     *         }
     *     },
     *     {
     *         "id": 23,
     *         "answer": "Very wrong",
     *         "image": null,
     *         "created_at": "2025-01-22T15:25:42.000000Z",
     *         "lawyer": {
     *             "id": 3,
     *             "name": "Sandra",
     *             "email": "sandra@gmail.com",
     *             "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *             "matricule": "bla bla bla"
     *         },
     *         "situation": {
     *             "id": 2,
     *             "title": "Gate man was arrest",
     *             "description": "He's am going through a lot",
     *             "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *             "created_at": "2025-01-21T11:09:12.000000Z"
     *         }
     *     },
     *     {
     *         "id": 25,
     *         "answer": "No No No",
     *         "image": null,
     *         "created_at": "2025-01-24T09:22:02.000000Z",
     *         "lawyer": {
     *             "id": 3,
     *             "name": "Sandra",
     *             "email": "sandra@gmail.com",
     *             "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *             "matricule": "bla bla bla"
     *         },
     *         "situation": {
     *             "id": 2,
     *             "title": "Gate man was arrest",
     *             "description": "He's am going through a lot",
     *             "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *             "created_at": "2025-01-21T11:09:12.000000Z"
     *         }
     *     }
     * ]
     *}
     * @response 404 {
     *"success": false,
     *"message": "Situation not found"
     *}
     */
    public function index(int $situationId)
    {
        // Check if the situation exists
        $situation = Situation::find($situationId);

        if (! $situation) {
            return response()->json([
                'success' => false,
                'message' => 'Situation not found',
            ], 404);
        }

        // Get all suggestions for the given situation
        $suggestions = Suggestion::where('situation_id', $situationId)
            ->with(['user', 'situation'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Suggestions successfully retrieved',
            'data' => SuggestionResource::collection($suggestions),
        ], 200);
    }

    /**
     * Store a new suggestion in the database.
     */

    /**
     * Get lawyer suggestions details.
     *
     * Retrieve details of a lawyer suggestions by their ID.
     *
     * @urlParam id int required The ID of the lawyer suggestions. Example: 1
     *
     * @response 201 {
     *  "success": true,
     * "data": {
     *     "id": 25,
     * "legal_system": "common_law",
     *    "answer": "No No No",
     *     "image": null,
     *     "created_at": "2025-01-24T09:22:02.000000Z",
     *    "lawyer": {
     *       "id": 3,
     *       "name": "Sandra",
     *       "email": "sandra@gmail.com",
     *      "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *     "matricule": "bla bla bla"
     * },
     * "situation": {
     *    "id": 2,
     *    "title": "Gate man was arrest",
     *    "description": "He's am going through a lot",
     *   "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *   "created_at": "2025-01-21T11:09:12.000000Z"
     *  }
     * }
     *}
     * @response 401 {
     *"message": "Unauthenticated."
     *}
     */
    public function store(Request $request, int $situationId)
    {

        DB::beginTransaction();

        try {

            $situation = Situation::find($situationId);

            if (! $situation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Situation not found',
                ], 404);
            }

            // Validate the request
            $validatedData = $request->validate([
                'legal_system'=> 'required|string|max:100',
                'answer' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:51200',
            ]);

            // Create the suggestion
            $suggestion = Suggestion::create([
                'legal_system'=>$validatedData['legal_system'],
                'answer' => $validatedData['answer'],
                'situation_id' => $situation->id,
                'user_id' => Auth::id(),
            ]);

            // Handle the image upload if provided
            if ($request->hasFile('image')) {
                $imagePath = Storage::url($request->file('image')->store('suggestion_images', 'public'));
                $suggestion->image = $imagePath;
                $suggestion->save();
            }

            DB::commit();
            $suggestion->load([
                'user',
                'situation',
            ]);

            return response()->json([
                'success' => true,
                'data' => new SuggestionResource($suggestion),
            ], 201);

        } catch (Exception $e) {
            DB::rollback();
            logger()->error('Failed to create suggestion', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error occured while creating suggestion',
            ], 500);

        }
    }

    /**
     * Display a specific suggestion.
     */

    /**
     * Get lawyer suggestions details.
     *
     * Retrieve details of a lawyer suggestions by their ID.
     *
     * @urlParam id int required The ID of the lawyer suggestions. Example: 1
     *
     * @response 200 {
     *"success": true,
     *"data": {
     *    "id": 25,
     *    "answer": "No No No",
     *    "image": null,
     *    "created_at": "2025-01-24T09:22:02.000000Z",
     *    "updated_at": "2025-01-24T09:22:02.000000Z",
     *    "situation_id": 2,
     *    "user_id": 3,
     *    "situation": {
     *        "id": 2,
     *        "title": "Gate man was arrest",
     *        "description": "He's am going through a lot",
     *        "image": "/storage/images/sHNcE5cszEFpEYu770KvndsYVtYqSV8biQDgvB0Y.png",
     *        "created_at": "2025-01-21T11:09:12.000000Z",
     *        "updated_at": "2025-01-21T11:09:12.000000Z",
     *        "user_id": 2
     *    },
     *    "user": {
     *        "id": 3,
     *        "name": "Sandra",
     *        "email": "sandra@gmail.com",
     *        "email_verified_at": null,
     *        "matricule": "bla bla bla",
     *        "role": "lawyer",
     *        "image": "/storage/images/LPu4h7nOiKV7qvnlWsB1z0HJa4B7ofgHwymtAiHY.jpg",
     *        "created_at": "2025-01-22T13:30:56.000000Z",
     *        "updated_at": "2025-01-22T13:30:56.000000Z"
     *    }
     *}
     *}
     * @response 404 {
     *"success": false,
     *"message": "Suggestion not found"
     *}
     */
    public function show(int $id)
    {
        // Find the suggestion by ID
        $suggestion = Suggestion::with(['situation', 'user'])->find($id);

        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $suggestion,
        ]);
    }

    /**
     * Update a suggestion in the database.
     */

    /**
     * Get lawyer suggestions details.
     *
     * Retrieve details of a lawyer suggestions by their ID.
     *
     * @urlParam id int required The ID of the lawyer suggestions. Example: 1
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
        $suggestion = Suggestion::find($id);
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:2000',
            'description' => 'sometimes|string',
            'image' => 'nullable|file|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),

            ], 422);
        }

        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }

        $suggestion->update([
            'answer' => $request->answer ?? $suggestion->answer,
            'legal_system'=>$request->legal_system ?? $suggestion->legal_system,
        ]);

        if ($request->hasFile('image')) {
            try {
                $path = Storage::url($request->file('image')->store('images', 'public'));
                $suggestion->image = $path;
                $suggestion->save();
            } catch (\Exception $e) {
                logger()->error('File upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $suggestion->load([
            'user',
            'situation',
        ]);

        return response()->json([
            'message' => 'suggestion updated successfully',
            'data' => new SuggestionResource($suggestion),
        ], 200);

    }

    /**
     * Remove a suggestion from the database.
     */

    /**
     * Get lawyer suggestions details.
     *
     * Retrieve details of a lawyer suggestions by their ID.
     *
     * @urlParam id int required The ID of the lawyer suggestions. Example: 1
     *
     * @response 200 {
     *"message": "Suggestion deleted successfully"
     *}
     * @response 404 {
     *"success": false,
     *"message": "Suggestion not found"
     *}
     */
    public function destroy(int $id)
    {
        $suggestion = Suggestion::find($id);
        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }
        logger($suggestion);
        $suggestion->delete();

        return response()->json([
            'message' => 'Suggestion deleted successfully',
        ], 200);
    }
}
