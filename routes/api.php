<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocationController;

// Auth Routes
Route::post('/auth/register', [AuthController::class, 'registerUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);



// Mobile Open Routes
Route::group(['prefix' => 'mobile'], function () {
});

// Website Open Routes
Route::group(['prefix' => 'website'], function () {
});

Route::middleware('auth:sanctum')->group(function () {
    //Check Auth Routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


//Mobile Routes
    Route::group(['prefix' => 'mobile'], function () {
        Route::get('event-list', [EventController::class, 'eventList']);
    });
    //Website Routes
    Route::group(['prefix' => 'website'], function () {
    });
//Admin Routes
    Route::group(['prefix' => 'admin'], function () {
    });

});


// open routes
Route::group(['prefix' => 'open'], function () {
    Route::get('division-list', [LocationController::class, 'divisionList']);
    Route::get('district-list/{division_id}', [LocationController::class, 'districtListByID']);
    Route::get('upazila-list/{district_id}', [LocationController::class, 'upazilaListByID']);
    Route::get('area-list/{upazilla_id}', [LocationController::class, 'unionListByID']);
});



Route::any('{url}', function () {
    return response()->json([
        'status' => false,
        'message' => 'Route Not Found!',
        'data' => []
    ], 404);
})->where('url', '.*');
