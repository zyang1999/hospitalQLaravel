<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AppointmentController;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $queueCount = Queue::whereDate('created_at', Carbon::today())->where('status', 'not like', 'CANCELLED')->count();
        $userCount = User::where('status', 'VERIFYING')->count();
        return view('dashboard', ['queueCount' => $queueCount, 'userCount' => $userCount]);
    })->name('dashboard');
    
    Route::get('/users', [UserController::class, 'getUsers'])->name('users');
    Route::get('/verification', [UserController::class, 'getUnverifiedPatients'])->name('verification');
    Route::get('/queue', [SpecialtyController::class, 'getSpecialtiesView'])->name('queue');
    Route::get('/user/{id}', [UserController::class, 'getUserDetails']);
    Route::get('/getUserWithIC/{ic}', [UserController::class, 'getUserWithIC']);
    Route::post('/createQueue', [QueueController::class, 'createQueue']);
    Route::post('/approveAccount', [UserController::class, 'approveAccount']);
    Route::post('/rejectAccount', [UserController::class, 'rejectAccount']);
    Route::post('/createUser', [UserController::class, 'createUser']);
    Route::post('/editUser', [UserController::class, 'editUser']);
    Route::post('/removeUser', [UserController::class, 'removeUser']);
    Route::get('/appointment', [AppointmentController::class, 'getAppointmentView'])->name('appointment');
    Route::get('/getAppointmentTable', [AppointmentController::class, 'getAppointmentTable']);
    Route::post('/createAppointment', [AppointmentController::class, 'createAppointmentWeb']);
    Route::post('/getDoctors', [UserController::class, 'getDoctorsWeb']);
    Route::get('/getDoctorSpecialty', [UserController::class, 'getDoctorSpecialtyWeb']);
    Route::get('/password', function (){
        return view('password');
    })
    ->name('password');
    Route::get('/changePassword', [UserController::class, 'changePasswordWeb']);
    Route::get('/verifyEmail/{token}',[UserController::class, 'verifyEmail']);
});

require __DIR__.'/auth.php';
