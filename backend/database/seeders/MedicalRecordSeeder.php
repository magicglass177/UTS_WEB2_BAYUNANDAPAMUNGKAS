<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tht.test')->firstOrFail();
        $petugas = User::where('email', 'petugas@tht.test')->firstOrFail();

        $andi = Patient::where('nik', '6202010101010001')->firstOrFail();
        $siti = Patient::where('nik', '6202010202020002')->firstOrFail();

        MedicalRecord::create([
            'patient_id' => $andi->id,
            'user_id' => $admin->id,
            'examination_date' => '2026-09-20',
            'complaint' => 'Telinga terasa sakit sejak dua hari.',
            'diagnosis' => 'Otitis eksterna',
            'treatment' => 'Pemberian obat sesuai pemeriksaan dokter.',
            'notes' => 'Kontrol kembali jika keluhan berlanjut.',
        ]);

        MedicalRecord::create([
            'patient_id' => $andi->id,
            'user_id' => $petugas->id,
            'examination_date' => '2026-09-24',
            'complaint' => 'Telinga masih terasa tidak nyaman.',
            'diagnosis' => 'Evaluasi lanjutan',
            'treatment' => 'Melanjutkan penanganan sesuai arahan dokter.',
            'notes' => 'Dijadwalkan kontrol berikutnya.',
        ]);

        MedicalRecord::create([
            'patient_id' => $siti->id,
            'user_id' => $admin->id,
            'examination_date' => '2026-09-25',
            'complaint' => 'Hidung tersumbat dan bersin.',
            'diagnosis' => 'Rhinitis',
            'treatment' => 'Pemberian terapi sesuai pemeriksaan dokter.',
            'notes' => null,
        ]);
    }
}