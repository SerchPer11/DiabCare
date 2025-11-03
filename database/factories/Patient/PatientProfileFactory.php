<?php

namespace Database\Factories\Patient;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient\PatientPathologies;
use App\Models\Patient\PatientProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientProfile>
 */
class PatientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return [
            'weight' => $this->faker->randomFloat(2, 50, 110), 
            'height' => $this->faker->randomFloat(2, 1.50, 2.00), 
            'blood_type' => $this->faker->randomElement($bloodTypes),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PatientProfile $patient) {
            PatientPathologies::factory()->create([
                'patient_profile_id' => $patient->id,
            ]);
        });
    }
}
