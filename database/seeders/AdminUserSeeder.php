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
            'name' => 'ELista Berlian Tina',
            'username' => 'elista',
            'password' => Hash::make('password'),
            'role' => 'administrasi',
        ]);

        // Buat user guru
        User::create([
            'name' => 'Guru Bahasa',
            'username' => 'guru@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);  

        // Buat user murid
        User::create([
            'name' => 'Murid Teladan',
            'username' => 'murid@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);
    }
}
