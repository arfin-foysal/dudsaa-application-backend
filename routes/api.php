<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BloodController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PollController;

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
        Route::get('event-details/{id}', [EventController::class, 'eventDetails']);
        Route::get('notice-list', [NoticeController::class, 'noticeList']);
        Route::get('notice-details/{id}', [NoticeController::class, 'noticeDetails']);
        Route::get('voting-list', [PollController::class, 'votingList']);
        Route::post('voting', [PollController::class, 'voting']);
        Route::Post('poll-save-or-update', [PollController::class, 'pollSaveOrUpdate']);
        Route::delete('poll-delete', [PollController::class, 'pollDelete']);
        Route::Post('poll-option-update', [PollController::class, 'pollOptionUpdate']);
        Route::delete('poll-option-delete', [PollController::class, 'pollOptionDelete']);
        Route::get('poll-list', [PollController::class, 'pollList']);
        Route::get('poll-details/{id}', [PollController::class, 'pollDetails']);
        Route::get('alumni-list', [MemberController::class, 'alumniList']);
        Route::get('same-Batch-alumni-list', [MemberController::class, 'sameBatchAlumniList']);
        Route::get('alumni-details/{id}', [MemberController::class, 'alumniDetails']);
        Route::get('get-profile', [AuthController::class, 'getProfile']);
        Route::post('update-user', [AuthController::class, 'updateUser']);
        Route::post('blood-request-save-or-update', [BloodController::class, 'bloodRequest']);
        Route::get('blood-request-list', [BloodController::class, 'bloodRequestList']);
        Route::post('education-save-or-update', [MemberController::class, 'educationSaveOrUpdate']);
        Route::post('service-save-or-update', [MemberController::class, 'serviceSaveOrUpdate']);
        Route::get('service-list', [MemberController::class, 'serviceList']);
        Route::get('service-list-by-id/{id}', [MemberController::class, 'serviceListById']);
        Route::get('alumni-List-by-blood-group', [BloodController::class, 'alumniListByBloodGroup']);
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
    Route::get('country-list', [LocationController::class, 'countryList']);
    Route::get('state-list/{country_id}', [LocationController::class, 'stateListByID']);
    Route::get('city-list/{state_id}', [LocationController::class, 'cityListByID']);
});



Route::any('{url}', function () {
    return response()->json([
        'status' => false,
        'message' => 'Route Not Found!',
        'data' => []
    ], 404);
})->where('url', '.*');
