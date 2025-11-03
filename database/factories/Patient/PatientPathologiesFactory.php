<?php

namespace Database\Factories\Patient;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientPathologies>
 */
class PatientPathologiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasDiabetes = $this->faker->boolean(30);
        $hasHypertension = $this->faker->boolean(40);
        $hasObesity = $this->faker->boolean(25);
        $hasAllergies = $this->faker->boolean(20);

        return [
            'diabetes' => $hasDiabetes,
            'diabetes_type' => $hasDiabetes ? $this->faker->randomElement(['TP1', 'TP2', 'Gest']) : null,
            'diabetes_diagnosis_date' => $hasDiabetes ? $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,

            'hypertension' => $hasHypertension,
            'hypertension_diagnosis_date' => $hasHypertension ? $this->faker->dateTimeBetween('-8 years', 'now')->format('Y-m-d') : null,

            'obesity' => $hasObesity,
            'obesity_type' => $hasObesity ? $this->faker->randomElement(['N', 'I', 'II', 'III']) : null,

            'allergies' => $hasAllergies,
            'allergy_details' => $hasAllergies ? $this->faker->sentence(6) : null,
        ];
    }
}
