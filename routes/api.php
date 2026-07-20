<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/specialties', [SpecialtyController::class, 'index']);
Route::get('/doctors', [DoctorController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/appointments', [AppointmentController::class, 'doctorTodayAppointments']);
        Route::get('/doctor/appointments/all', [AppointmentController::class, 'doctorAllAppointments']);
    });

    Route::get('/doctor/{id}', [DoctorController::class, 'show']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->middleware('role:patient');

    Route::get('/appointments/patient/{id}', [AppointmentController::class, 'patientAppointments'])
        ->middleware('role:patient');
    Route::get('/my-appointments', [AppointmentController::class, 'patientAppointments'])
        ->middleware('role:patient');

    Route::put('/appointments/{id}/cancel', [AppointmentController::class, 'cancelAppointment'])
        ->middleware('role:patient');

    Route::middleware('role:doctor')->group(function () {
        Route::get('/appointments/today/{doctor_id}', [AppointmentController::class, 'doctorTodayAppointments']);
        Route::get('/appointments/doctor/{doctor_id}', [AppointmentController::class, 'doctorAllAppointments']);
        Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);

        Route::put('/appointments/{id}/notes', [AppointmentController::class, 'updateNotes']);
    });
});
