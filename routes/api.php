<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\SituationController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout')->middleware('auth:sanctum');
});

// Route::apiResource('situations', SituationController::class)->middleware('auth:sanctum');

Route::controller(SituationController::class)->group(function() {
    Route::get('situations', 'index');
    Route::post('situations', 'store')->middleware('auth:sanctum');
    // Add the filter route for situations
    Route::get('situations', 'search');
    Route::get('situations/{situationId}', 'show');
    Route::match(['put', 'patch'], 'situations/{situationId}', 'update')->middleware('auth:sanctum'); 
    Route::delete('situations/{situationId}', 'destroy')->middleware('auth:sanctum');

});




Route::controller(SuggestionController::class)->group(function() {
    // Get all suggestions under a particular situation
    Route::get('/situations/{situationId}/suggestions', 'index');

    // Create a new suggestion
    Route::post('/situations/{situationId}/suggestions', 'store')->middleware('auth:sanctum');

    // Get a specific suggestion by ID
    Route::get('/suggestions/{id}', 'show');

    // Update an existing suggestion
    Route::put('/suggestions/{id}', 'update')->middleware('auth:sanctum');

    // Delete a suggestion
    Route::delete('/suggestions/{id}', 'destroy')->middleware('auth:sanctum');

    Route::delete('/search-situation/{title}', 'destroy')->middleware('auth:sanctum');
});
