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
        $namaDepan = $this->faker->randomElement([
            'Budi', 'Siti', 'Ahmad', 'Rina', 'Deni', 'Yuli', 'Hendra',
            'Dewi', 'Agus', 'Fitri', 'Reza', 'Novia', 'Dian', 'Eko',
            'Lestari', 'Wahyu', 'Ayu', 'Fajar', 'Indah', 'Rizki',
        ]);
        $namaBelakang = $this->faker->randomElement([
            'Santoso', 'Wijaya', 'Kusuma', 'Rahayu', 'Pratama', 'Sari',
            'Nugroho', 'Wibowo', 'Hidayat', 'Permata', 'Susanto',
            'Saputra', 'Purnama', 'Laksana', 'Hartono', 'Setiawan',
        ]);
        $nama = "$namaDepan $namaBelakang";

        return [
            'name'              => $nama,
            'email'             => strtolower("$namaDepan.$namaBelakang") . rand(1, 99) . '@gmail.com',
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => 'masyarakat',
            'phone'             => '08' . $this->faker->numerify('#########'),
            'address'           => $this->faker->randomElement([
                'Jl. Merdeka No. ' . rand(1, 100) . ', Soreang',
                'Jl. Sudirman No. ' . rand(1, 50) . ', Banjaran',
                'Jl. Ahmad Yani No. ' . rand(1, 80) . ', Cimahi',
                'Perumahan Griya Asri Blok ' . chr(rand(65, 70)) . ' No. ' . rand(1, 20),
                'Jl. Raya Soreang KM ' . rand(1, 15) . ', Kab. Bandung',
            ]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function admin(): static
    {
        return $this->state(fn ($attr) => ['role' => 'admin']);
    }

    public function unverified(): static
    {
        return $this->state(fn ($attr) => ['email_verified_at' => null]);
    }
}
