<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(): JsonResponse
    {
        $patients = Patient::query()
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Data pasien berhasil diambil.',
            'data' => $patients,
        ]);
    }

    public function show(Patient $patient): JsonResponse
{
    $patient->load([
        'medicalRecords' => function ($query) {
            $query->with([
                'user:id,name,email,role',
            ])
            ->orderByDesc('examination_date')
            ->orderByDesc('id');
        },
    ]);

    return response()->json([
        'message' => 'Detail pasien berhasil diambil.',
        'data' => $patient,
    ]);
}

    public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'nik' => ['required', 'string', 'size:16', 'unique:patients,nik'],
        'name' => ['required', 'string', 'max:150'],
        'gender' => ['required', 'in:L,P'],
        'birth_date' => ['required', 'date', 'before_or_equal:today'],
        'phone' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string'],
    ]);

    $patient = Patient::create($validated);

    return response()->json([
        'message' => 'Data pasien berhasil ditambahkan.',
        'data' => $patient,
    ], 201);
}

public function update(Request $request, Patient $patient): JsonResponse
{
    $validated = $request->validate([
        'nik' => [
            'required',
            'string',
            'size:16',
            'unique:patients,nik,' . $patient->id,
        ],
        'name' => ['required', 'string', 'max:150'],
        'gender' => ['required', 'in:L,P'],
        'birth_date' => ['required', 'date', 'before_or_equal:today'],
        'phone' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string'],
    ]);

    $patient->update($validated);

    return response()->json([
        'message' => 'Data pasien berhasil diperbarui.',
        'data' => $patient->fresh(),
    ]);
}

public function destroy(Patient $patient): JsonResponse
{
    if ($patient->medicalRecords()->exists()) {
        return response()->json([
            'message' => 'Pasien tidak dapat dihapus karena masih memiliki riwayat pemeriksaan.',
        ], 409);
    }

    $patient->delete();

    return response()->json([
        'message' => 'Data pasien berhasil dihapus.',
    ]);
}

}