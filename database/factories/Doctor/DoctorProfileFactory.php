<?php

namespace Database\Factories\Doctor;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorProfile>
 */
class DoctorProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'specialty_id' => $this->faker->numberBetween(1, 16),
            'license_number' => $this->faker->unique()->numerify('LIC-#######'),
            'titulation_date' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
        ];
    }
}
