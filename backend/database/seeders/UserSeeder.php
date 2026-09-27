<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin THT',
            'email' => 'admin@tht.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas THT',
            'email' => 'petugas@tht.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);
    }
}