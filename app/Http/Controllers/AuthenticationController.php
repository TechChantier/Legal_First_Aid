<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Dotenv\Validator;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 401);
        }

        $credentials = request(['email', 'password']);
        if (! auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized, check your login credentials',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,

        ]);
    }

    public function register(Request $request)
    {

        DB::beginTransaction();

        try {
            if ($request->role == 'lawyer') {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|between:2,100',
                    'email' => 'required|email|max:100|unique:users',
                    'password' => 'required|string|confirmed|min:8',
                    'image' => 'nullable|file|max:51200',
                    'matricule' => 'required|string|max:1000',
                    'role' => 'required|in:lawyer',

                ]);
            } elseif ($request->role == 'normal_user' && ! $request->matricule) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|between:2,100',
                    'email' => 'required|email|max:100|unique:users',
                    'password' => 'required|string|confirmed|min:8',
                    'image' => 'nullable|file|max:51200',
                    'role' => 'required|in:normal_user',

                ]);
            } else {
                return response()->json(['message' => 'invalid input'], 500);
            }

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                ], 401);
            }
            logger($request->matricule);
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));

            if ($request->role == 'lawyer') {
                $user->role = 'lawyer';
                $user->save();
            } else {
                $user->role = 'normal_user';
                $user->save();
            }

            // Image validation
            if ($request->hasFile('image')) {
                try {
                    $path = Storage::url($request->file('image')->store('images', 'public'));
                    $user->image = $path;
                    $user->save();
                } catch (\Exception $e) {
                    logger()->error('File upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            DB::commit();

            // $tokenResult = $user->createToken('Personal Access Token');
            // $token = $tokenResult->plainTextToken;

            return response()->json([
                'message' => 'User Successfully Registered',
                // 'token' => $token,
                'user' => $user,

            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (Exception $e) {
            logger()->error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
