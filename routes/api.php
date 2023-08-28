<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BloodController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\UserController;

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
        Route::post('alumni-list', [MemberController::class, 'alumniList']);
        Route::get('same-Batch-alumni-list', [MemberController::class, 'sameBatchAlumniList']);
        Route::get('alumni-details/{id}', [MemberController::class, 'alumniDetails']);
        Route::get('get-profile', [AuthController::class, 'getProfile']);
        Route::post('update-user', [AuthController::class, 'updateUser']);
        Route::post('blood-request-save-or-update', [BloodController::class, 'bloodRequest']);
        Route::get('blood-request-list', [BloodController::class, 'bloodRequestList']);
        Route::post('education-save-or-update', [MemberController::class, 'educationSaveOrUpdate']);
        Route::post('service-save-or-update', [MemberController::class, 'serviceSaveOrUpdate']);
        Route::get('service-list', [MemberController::class, 'serviceList']);
        Route::get('education-list', [MemberController::class, 'educationList']);
        Route::get('service-list-by-id/{id}', [MemberController::class, 'serviceListById']);
        Route::get('education-list-by-id/{id}', [MemberController::class, 'educationListById']);
        Route::post('alumni-List-by-blood-group', [BloodController::class, 'alumniListByBloodGroup']);
        Route::get('blood-request-details/{id}', [BloodController::class, 'bloodRequestDetails']);
        Route::get('job-list', [JobController::class, 'jobList']);
        Route::get('job-details/{id}', [JobController::class, 'jobDetails']);
    });
    //Website Routes
    Route::group(['prefix' => 'website'], function () {
    });
    //Admin Routes
    Route::group(['prefix' => 'admin'], function () {
        Route::get('user-list', [UserController::class, 'userList']);
        Route::post('reset-password', [UserController::class, 'resetPassword']);
        Route::post('user-save-or-update', [UserController::class, 'userSaveOrCreate']);
        Route::post('notice-save-or-update', [NoticeController::class, 'noticeSaveOrUpdate']);
        Route::get('notice-list', [NoticeController::class, 'noticeList']);
        Route::get('job-list', [JobController::class, 'jobList']);
        Route::post('job-save-or-update', [JobController::class, 'saveOrUpdateJob']);
        Route::get('notice-details/{id}', [NoticeController::class, 'noticeDetails']);
        Route::Post('poll-save-or-update', [PollController::class, 'pollSaveOrUpdate']);
        Route::get('poll-list', [PollController::class, 'pollList']);
        Route::post('poll-option-update', [PollController::class, 'pollOptionUpdate']);
        Route::delete('poll-option-delete/{id}', [PollController::class, 'pollOptionDelete']);
        Route::get('event-list', [EventController::class, 'eventList']);
        Route::post('event-save-or-update', [EventController::class, 'eventSaveOrUpdate']);
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
