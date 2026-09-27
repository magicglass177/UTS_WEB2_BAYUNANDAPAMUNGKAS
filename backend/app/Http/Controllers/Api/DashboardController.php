<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalPatients = Patient::count();

        $totalMedicalRecords = MedicalRecord::count();

        $todayMedicalRecords = MedicalRecord::query()
            ->whereDate('examination_date', today())
            ->count();

        return response()->json([
            'message' => 'Data dashboard berhasil diambil.',
            'data' => [
                'total_patients' => $totalPatients,
                'total_medical_records' => $totalMedicalRecords,
                'today_medical_records' => $todayMedicalRecords,
            ],
        ]);
    }
}