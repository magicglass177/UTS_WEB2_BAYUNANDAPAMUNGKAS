<?php

use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
    );

    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::post('/patients', [PatientController::class, 'store']);
        Route::put(
            '/patients/{patient}',
            [PatientController::class, 'update']
        );

        Route::delete(
        '/patients/{patient}',
        [PatientController::class, 'destroy']
        );
    });

    Route::get(
        '/medical-records',
        [MedicalRecordController::class, 'index']
    );

    Route::get(
    '/medical-records/{medicalRecord}',
    [MedicalRecordController::class, 'show']
    );

    Route::post(
        '/medical-records',
        [MedicalRecordController::class, 'store']
    );

    Route::put(
        '/medical-records/{medicalRecord}',
        [MedicalRecordController::class, 'update']
    );

    Route::delete(
    '/medical-records/{medicalRecord}',
    [MedicalRecordController::class, 'destroy']
);
});