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
        $tahunSekarang = date('Y');
        
        // Buat user administrasi
        User::create([
            'id' => $tahunSekarang . '001',
            'name' => 'ELista Berlian Tina',
            'username' => 'elista',
            'password' => Hash::make('password'),
            'role' => 'administrasi',
            'tahun_angkatan' => $tahunSekarang,
        ]);

        // Buat user guru
        User::create([
            'id' => $tahunSekarang . '002',
            'name' => 'Guru Bahasa',
            'username' => 'guru@perpus.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'tahun_angkatan' => $tahunSekarang,
        ]);  

        // Buat user murid
        User::create([
            'id' => $tahunSekarang . '003',
            'name' => 'Murid Teladan',
            'username' => 'murid',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'tahun_angkatan' => $tahunSekarang,
        ]);
    }
}
