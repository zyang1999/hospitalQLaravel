<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SpecialtyController;

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

    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::post('/storeVerificationCredential', [UserController::class, 'storeVerificationCredential']);
    Route::post('/storeSelfie', [UserController::class, 'storeSelfie']);

    Route::post('/joinQueue', [QueueController::class, 'joinQueue']);
    Route::get('/getUserQueue', [QueueController::class, 'getUserQueue']);
    Route::get('/getAllQueue', [QueueController::class, 'getAllQueue']);
    Route::post('/updateQueue', [QueueController::class, 'updateQueue']);
    Route::get('/getCurrentPatient', [QueueController::class, 'getCurrentPatient']);
    Route::post('/cancelQueue', [QueueController::class, 'cancelQueue']);
    Route::get('/getQueueHistory', [QueueController::class, 'getQueueHistory']);
    
    Route::post('storeFeedback', [FeedbackController::class, 'storeFeedback']);
    Route::get('getSpecialties', [SpecialtyController::class, 'getSpecialties']);
    
});
Route::get('getDoctorList', [UserController::class, 'getDoctorList']);

