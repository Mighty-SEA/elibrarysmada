<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user administrasi
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'administrasi',
        ]);

        // Buat user guru
        User::create([
            'name' => 'Guru Bahasa',
            'email' => 'guru@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Buat user murid
        User::create([
            'name' => 'Murid Teladan',
            'email' => 'murid@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);
    }
}
