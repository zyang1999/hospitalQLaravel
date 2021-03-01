<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentFeedbackController;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Specialty;
use App\Models\Queue;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/logout', [UserController::class, 'logout']);

    //Verification
    Route::post('/storeVerificationCredential', [UserController::class, 'storeVerificationCredential']);
    Route::post('/storeSelfie', [UserController::class, 'storeSelfie']);

    //Profile
    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::post('changePassword', [UserController::class, 'changePassword']);
    Route::post('changeProfileImage', [UserController::class, 'changeProfileImage']);
    Route::post('changePhoneNumber', [UserController::class, 'changePhoneNumber']);
    Route::post('changeHomeAddress', [UserController::class, 'changeHomeAddress']);
    
    //History
    Route::get('/getHistory', [UserController::class, 'getHistory']);
    Route::post('getAppointmentDetails', [AppointmentController::class, 'getAppointmentDetails']);
    Route::post('getQueueDetails', [QueueController::class, 'getQueueDetails']);

    //Queue
    Route::post('/joinQueue', [QueueController::class, 'joinQueue']);
    Route::get('/getUserQueue', [QueueController::class, 'getUserQueue']);
    Route::get('/getAllQueue', [QueueController::class, 'getAllQueue']);
    Route::post('/updateQueue', [QueueController::class, 'updateQueue']);
    Route::get('/getCurrentPatient', [QueueController::class, 'getCurrentPatient']);
    Route::post('/cancelQueue', [QueueController::class, 'cancelQueue']);
    Route::post('/stopQueue', [QueueController::class, 'stopQueue']);
    Route::post('storeFeedback', [FeedbackController::class, 'storeFeedback']);
    

    //Appointment
    Route::post('getSpecialties', [SpecialtyController::class, 'getSpecialties']);
    Route::post('getDoctorList', [UserController::class, 'getDoctorList']);
    Route::post('getAvailableDate', [AppointmentController::class, 'getAvailableDate']);
    Route::post('getSchedule', [AppointmentController::class, 'getSchedule']);
    Route::post('bookAppointment',[AppointmentController::class, 'bookAppointment']);
    Route::get('getAppointment', [AppointmentController::class, 'getAppointment']);
    Route::get('getDoctorAppointments', [AppointmentController::class, 'getDoctorAppointments']);
    Route::post('createAppointment', [AppointmentController::class, 'createAppointment']);
    Route::post('deleteAppointment', [AppointmentController::class, 'deleteAppointment']);
    Route::get('getDoctorAppointmentsToday', [AppointmentController::class, 'getDoctorAppointmentsToday']);
    Route::post('completeAppointment', [AppointmentController::class, 'completeAppointment']);
    Route::post('storeAppointmentFeedback', [AppointmentFeedbackController::class, 'storeAppointmentFeedback']);

    Route::post('saveFcmToken', [UserController::class, 'saveFcmToken']);
});

Route::get('/getQueueCount', function () {
    return Specialty::where("specialty", "Pharmacist")->pluck('location')->unique()->sortBy(function ($location){
        return Queue::where("specialty", "Pharmacist")->where("location", $location)->count();
    })->values()->first();
});