<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $urut;
        $tahun = 2025;
        if ($urut === null) {
            $last = \App\Models\User::where('id', 'like', $tahun . '%')->orderBy('id', 'desc')->first();
            if ($last) {
                $urut = intval(substr($last->id, 4)) + 1;
            } else {
                $urut = 1;
            }
        }
        $id = $tahun . str_pad($urut++, 3, '0', STR_PAD_LEFT);

        // Buat tanggal acak antara 1 Januari 2025 sampai 30 Juni 2025
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 6, 30);
        $randomDate = Carbon::createFromTimestamp(
            rand($startDate->timestamp, $endDate->timestamp)
        );
        
        // Daftar nama depan Indonesia
        $namaDepanPria = ['Budi', 'Agus', 'Dedi', 'Eko', 'Bambang', 'Joko', 'Hadi', 'Slamet', 'Dimas', 'Andi', 
                          'Rizki', 'Fajar', 'Bayu', 'Hendra', 'Irwan', 'Dodi', 'Arif', 'Wahyu', 'Yusuf', 'Aditya'];
        $namaDepanWanita = ['Siti', 'Dewi', 'Rina', 'Ani', 'Yuni', 'Lina', 'Wati', 'Lia', 'Fitri', 'Ratna', 
                           'Indah', 'Dian', 'Ayu', 'Ika', 'Nur', 'Sri', 'Erni', 'Endah', 'Tika', 'Maya'];
        
        // Daftar nama belakang Indonesia
        $namaBelakang = ['Wijaya', 'Susanto', 'Saputra', 'Hidayat', 'Nugraha', 'Setiawan', 'Kusuma', 'Wibowo', 'Santoso', 'Pratama', 
                         'Suryadi', 'Putra', 'Utama', 'Permana', 'Purnama', 'Firmansyah', 'Kurniawan', 'Hartono', 'Sugiarto', 'Budiman'];
        
        // Tentukan jenis kelamin acak
        $jenisKelamin = fake()->randomElement(['Laki-laki', 'Perempuan']);
        
        // Pilih nama depan berdasarkan jenis kelamin
        $namaDepan = ($jenisKelamin === 'Laki-laki') 
            ? $namaDepanPria[array_rand($namaDepanPria)] 
            : $namaDepanWanita[array_rand($namaDepanWanita)];
        
        // Pilih nama belakang
        $belakang = $namaBelakang[array_rand($namaBelakang)];
        
        // Gabungkan nama depan dan belakang
        $namaLengkap = $namaDepan . ' ' . $belakang;
        
        // Buat nomor telepon Indonesia (format: 08xxxxxxxxxx)
        $nomorTelepon = '08' . rand(1, 9) . rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        
        return [
            'id' => $id,
            'name' => $namaLengkap,
            'username' => strtolower($namaDepan) . '.' . strtolower($belakang) . rand(1, 99),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'murid',
            'jenis_kelamin' => $jenisKelamin,
            'jurusan' => fake()->randomElement(['IPA', 'IPS']),
            'tahun_angkatan' => $tahun,
            'nomor_telepon' => $nomorTelepon,
            'created_at' => $randomDate,
            'updated_at' => $randomDate,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
