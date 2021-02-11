<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AppointmentController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
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
});

require __DIR__.'/auth.php';
