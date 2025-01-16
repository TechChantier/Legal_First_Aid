<?php

namespace App\Http\Controllers;

use App\Http\Resources\SituationResource;
use App\Models\Situation;
use Illuminate\Http\Request;
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

    public function show(Situation $situation) 
    {
        return new SituationResource($situation);
    }

    public function update(Request $request, Situation $situation)
     {
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

    public function destroy(Situation $situation)
     {
        $situation->delete();
        return response()->json([
            'message' => 'Situation deleted successfully',
        ], 200);
     }
}
