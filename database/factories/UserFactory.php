<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        return [
            'id' => $id,
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'murid',
            'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'jurusan' => fake()->randomElement(['IPA', 'IPS', 'Bahasa']),
            'tahun_angkatan' => $tahun,
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
