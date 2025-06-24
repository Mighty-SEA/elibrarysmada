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

        return [
            'id' => $id,
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'murid',
            'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'jurusan' => fake()->randomElement(['IPA', 'IPS', 'Bahasa']),
            'tahun_angkatan' => $tahun,
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
