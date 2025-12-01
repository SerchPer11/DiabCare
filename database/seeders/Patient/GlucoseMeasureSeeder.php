<?php

namespace Database\Seeders\Patient;

use App\Models\Patient\MeasureType;
use App\Models\Patient\MeasureConfig;
use App\Models\Patient\Measure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GlucoseMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el tipo de medición de glucosa
        $glucoseType = MeasureType::where('name', 'LIKE', '%glucosa%')
            ->orWhere('name', 'LIKE', '%glucose%')
            ->first();

        if (!$glucoseType) {
            // Crear tipo de medición de glucosa si no existe
            $glucoseType = MeasureType::create([
                'name' => 'Glucosa en Sangre',
                'unit' => 'mg/dL'
            ]);
        }

        // Obtener todos los pacientes
        $patients = User::role('patient')->get();
        if ($patients->isEmpty()) {
            return;
        }

        foreach ($patients as $patient) {
            $config = MeasureConfig::firstOrCreate([
                'patient_id' => $patient->id,
                'measure_type_id' => $glucoseType->id,
            ], [
                'min_value' => 70,
                'max_value' => 130,
                'range' => 'outrange',
                'severity' => 'high',
                'frequency' => 'daily',
            ]);
            // Crear mediciones de glucosa para los últimos 30 días para cada paciente
            $this->createGlucoseMeasures($config->id, $patient->id);
        }
    }

    private function createGlucoseMeasures($configId, $patientId)
    {
        $measures = [];
        
        // Crear mediciones para los últimos 30 días
        for ($days = 29; $days >= 0; $days--) {
            $date = Carbon::now()->subDays($days);
            
            // Simular diferentes patrones según el paciente
            $baseGlucose = $this->getBaseGlucoseForPatient($patientId);
            $variation = rand(-20, 30); // Variación aleatoria
            
            // Medición en ayunas (mañana)
            if (rand(1, 10) <= 8) { // 80% de probabilidad de tener medición matutina
                $fastingGlucose = $baseGlucose + rand(-10, 15);
                $measures[] = [
                    'value' => max(60, min(200, $fastingGlucose)),
                    'measured_at' => $date->format('Y-m-d'),
                    'hour_measured' => $this->getRandomTime(7, 9), // Entre 7 y 9 AM
                    'notes' => $this->getFastingNote(),
                    'measure_config_id' => $configId,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }

            // Medición posprandial (tarde/noche)
            if (rand(1, 10) <= 6) { // 60% de probabilidad de tener medición posprandial
                $postprandialGlucose = $baseGlucose + rand(20, 60); // Más alta después de comer
                $measures[] = [
                    'value' => max(80, min(250, $postprandialGlucose)),
                    'measured_at' => $date->format('Y-m-d'),
                    'hour_measured' => $this->getRandomTime(14, 20), // Entre 2 y 8 PM
                    'notes' => $this->getPostprandialNote(),
                    'measure_config_id' => $configId,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        // Insertar todas las mediciones
        foreach ($measures as $measure) {
            Measure::create($measure);
        }
    }

    private function getBaseGlucoseForPatient($patientId)
    {
        // Simular patrón variado para María (paciente con diabetes tipo 2 en tratamiento)
        // Alternará entre días de buen control y días con picos
        $dayOfYear = Carbon::now()->dayOfYear;
        if ($dayOfYear % 3 == 0) {
            return 120; // Días con control regular
        } elseif ($dayOfYear % 5 == 0) {
            return 150; // Días con picos ocasionales
        } else {
            return 95;  // Días con buen control
        }
    }

    private function getRandomTime($startHour, $endHour)
    {
        $hour = rand($startHour, $endHour);
        $minute = rand(0, 59);
        return sprintf('%02d:%02d:00', $hour, $minute);
    }

    private function getFastingNote()
    {
        $notes = [
            'Medición en ayunas - 8 horas sin comer',
            'Glucosa en ayunas matutina',
            'Medición antes del desayuno - ayuno nocturno',
            'Control glucémico en ayunas',
            'Medición matutina en ayunas de 10 horas',
            'Ayunas - sin ingesta desde la cena',
        ];

        return $notes[array_rand($notes)];
    }

    private function getPostprandialNote()
    {
        $notes = [
            'Medición posprandial - 2 horas después del almuerzo',
            'Control glucémico después de la cena',
            'Medición postprandial vespertina',
            'Glucosa 1.5 horas después de comer',
            'Control posprandial - después del almuerzo',
            'Medición después de la comida principal',
            'Glucosa postprandial tardía',
            'Control vespertino - 2 horas post-comida',
        ];

        return $notes[array_rand($notes)];
    }
}