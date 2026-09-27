<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        Patient::create([
            'nik' => '6202010101010001',
            'name' => 'Andi Saputra',
            'gender' => 'L',
            'birth_date' => '1995-05-12',
            'phone' => '081234567801',
            'address' => 'Sampit',
        ]);

        Patient::create([
            'nik' => '6202010202020002',
            'name' => 'Siti Rahma',
            'gender' => 'P',
            'birth_date' => '2000-08-20',
            'phone' => '081234567802',
            'address' => 'Sampit',
        ]);

        Patient::create([
            'nik' => '6202010303030003',
            'name' => 'Budi Santoso',
            'gender' => 'L',
            'birth_date' => '1988-11-03',
            'phone' => '081234567803',
            'address' => 'Baamang',
        ]);
    }
}