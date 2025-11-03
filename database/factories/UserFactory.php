<?php

namespace Database\Factories;

use App\Models\Doctor\DoctorProfile;
use App\Models\Patient\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

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
        $gender = $this->faker->randomElement(['male', 'female', 'other']);

        return [
            'name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName(),
            'second_last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('12345678'),
            'phone' => $this->faker->numerify('55########'),
            'gender' => $gender,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    public function doctor(): Factory
    {
        return $this->state(function (array $attributes) {
            
            $doctorProfile = DoctorProfile::factory()->create();
            return [
                'profileable_id' => $doctorProfile->id,
                'profileable_type' => DoctorProfile::class,
            ];
        })->afterCreating(function (User $user) {
            $user->assignRole('doctor');
        });
    }

    public function patient(): Factory
    {
        return $this->state(function (array $attributes) {

            $patientProfile = PatientProfile::factory()->create();
            return [
                'profileable_id' => $patientProfile->id,
                'profileable_type' => PatientProfile::class,
            ];
        })->afterCreating(function (User $user) {
            $user->assignRole('patient');
        });
    }
}
