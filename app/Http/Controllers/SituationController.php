<?php

namespace App\Http\Controllers;

use App\Http\Resources\SituationResource;
use App\Models\Situation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SituationController extends Controller
{
    public function index()
    {
        $situation = Situation::get();
        if ($situation->count() > 0) {
            return SituationResource::collection($situation);
        } else {
            return response()->json(['message' => 'No situation available'], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:2000',
            'description' => 'required',
            'image' => 'nullable|file|max:51200',
        ]);

        if($validator->fails())
         {
            return response()->json([
              'message'=>'All fields are mandatory',
              'error'=> $validator->messages(),
             
            ], 422);
         }

        $situation = Situation::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id()
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

    public function show(int $id) 
    {
        $situation = Situation::find($id);
       
        return new SituationResource($situation);
    }

    public function update(Request $request, int $id)
     {
        $situation = Situation::find($id);
        logger($situation);
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:2000',
            'description' => 'sometimes',
            'image' => 'nullable|file|max:51200',
        ]);

        if($validator->fails())
         {
            return response()->json([
              'message'=>'All fields are mandatory',
              'error'=> $validator->messages(),
             
            ], 422);
         }

        $situation->update([
            'title' => $request->title ?? $situation->title,
            'description' => $request->description ?? $situation->description,
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
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
    
}
