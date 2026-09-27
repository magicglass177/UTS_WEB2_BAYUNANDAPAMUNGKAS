<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(): JsonResponse
    {
        $medicalRecords = MedicalRecord::query()
            ->with([
                'patient:id,nik,name,gender,birth_date',
                'user:id,name,email,role',
            ])
            ->orderByDesc('examination_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Data riwayat pemeriksaan berhasil diambil.',
            'data' => $medicalRecords,
        ]);
    }

    public function show(MedicalRecord $medicalRecord): JsonResponse
{
    $medicalRecord->load([
        'patient:id,nik,name,gender,birth_date,phone,address',
        'user:id,name,email,role',
    ]);

    return response()->json([
        'message' => 'Detail riwayat pemeriksaan berhasil diambil.',
        'data' => $medicalRecord,
    ]);
}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => [
                'required',
                'integer',
                'exists:patients,id',
            ],
            'examination_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'complaint' => [
                'required',
                'string',
                'max:1000',
            ],
            'diagnosis' => [
                'required',
                'string',
                'max:255',
            ],
            'treatment' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $validated['user_id'] = $request->user()->id;

        $medicalRecord = MedicalRecord::create($validated);

        $medicalRecord->load([
            'patient:id,nik,name,gender,birth_date',
            'user:id,name,email,role',
        ]);

        return response()->json([
            'message' => 'Riwayat pemeriksaan berhasil ditambahkan.',
            'data' => $medicalRecord,
        ], 201);
    }

    public function update(
        Request $request,
        MedicalRecord $medicalRecord
    ): JsonResponse {
        $user = $request->user();

        if (
            $user->role !== 'admin'
            && $medicalRecord->user_id !== $user->id
        ) {
            abort(
                403,
                'Anda tidak diperbolehkan mengedit riwayat pemeriksaan ini.'
            );
        }

        $validated = $request->validate([
            'patient_id' => [
                'required',
                'integer',
                'exists:patients,id',
            ],
            'examination_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'complaint' => [
                'required',
                'string',
                'max:1000',
            ],
            'diagnosis' => [
                'required',
                'string',
                'max:255',
            ],
            'treatment' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $medicalRecord->update($validated);

        $medicalRecord->load([
            'patient:id,nik,name,gender,birth_date',
            'user:id,name,email,role',
        ]);

        return response()->json([
            'message' => 'Riwayat pemeriksaan berhasil diperbarui.',
            'data' => $medicalRecord,
        ]);
    }

    public function destroy(
    Request $request,
    MedicalRecord $medicalRecord
): JsonResponse {
    $user = $request->user();

    if (
        $user->role !== 'admin'
        && $medicalRecord->user_id !== $user->id
    ) {
        abort(
            403,
            'Anda tidak diperbolehkan menghapus riwayat pemeriksaan ini.'
        );
    }

    $medicalRecord->delete();

    return response()->json([
        'message' => 'Riwayat pemeriksaan berhasil dihapus.',
    ]);
}
}