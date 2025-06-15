<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin
        User::create([
            'id' => '2025001',
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'administrasi',
            'tahun_angkatan' => 2025,
        ]);
        
        // Buat guru
        User::create([
            'id' => '2025002',
            'name' => 'Guru Demo',
            'username' => 'guru',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'tahun_angkatan' => 2025,
        ]);
        
        // Buat murid
        User::create([
            'id' => '2025003',
            'name' => 'Murid Demo',
            'username' => 'murid',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'tahun_angkatan' => 2025,
            'jenis_kelamin' => 'Laki-laki',
            'jurusan' => 'IPA',
        ]);
    }
}
