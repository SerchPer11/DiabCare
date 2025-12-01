<?php

namespace Database\Seeders\Patient;

use App\Models\Patient\MeasureType;
use Illuminate\Database\Seeder;
use App\Models\Patient\MeasureConfig;
use App\Models\Patient\Measure;

class MeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measureTypes = [
            ['name' => 'Peso', 'unit' => 'kg'],
            ['name' => 'Altura', 'unit' => 'cm'],
            ['name' => 'Presión Arterial Sistólica', 'unit' => 'mmHg'],
            ['name' => 'Presión Arterial Diastólica', 'unit' => 'mmHg'],
            ['name' => 'Frecuencia Cardíaca', 'unit' => 'bpm'],
            ['name' => 'Glucosa en Sangre', 'unit' => 'mg/dL'],
            ['name' => 'Temperatura Corporal', 'unit' => '°C'],
            ['name' => 'Frecuencia Respiratoria', 'unit' => 'rpm'],
            ['name' => 'Saturación de Oxígeno', 'unit' => '%'],
            ['name' => 'Hemoglobina Glicosilada (HbA1c)', 'unit' => '%'],
            ['name' => 'Colesterol Total', 'unit' => 'mg/dL'],
            ['name' => 'Colesterol LDL', 'unit' => 'mg/dL'],
            ['name' => 'Colesterol HDL', 'unit' => 'mg/dL'],
            ['name' => 'Triglicéridos', 'unit' => 'mg/dL'],
            ['name' => 'Circunferencia de Cintura', 'unit' => 'cm'],
        ];

        foreach ($measureTypes as $type) {
            MeasureType::create($type);
        }

        // Obtener pacientes
        $patients = \App\Models\User::role('patient')->get();
        $patientCount = $patients->count();
        $measureConfigs = [
            ['min_value' => 50, 'max_value' => 150, 'range' => 'outrange', 'measure_type_id' => 1, 'severity' => 'medium', 'frequency' => 'monthly'], // Peso
            ['min_value' => 100, 'max_value' => 250, 'range' => 'outrange', 'measure_type_id' => 2, 'severity' => 'medium', 'frequency' => 'once'], // Altura
            ['min_value' => 60, 'max_value' => 100, 'range' => 'outrange', 'measure_type_id' => 4, 'severity' => 'medium', 'frequency' => 'daily'], // Frecuencia Cardíaca
            ['min_value' => 70, 'max_value' => 130, 'range' => 'outrange', 'measure_type_id' => 5, 'severity' => 'medium', 'frequency' => 'daily'], // Glucosa en Sangre
            ['min_value' => 36.0, 'max_value' => 37.5, 'range' => 'outrange', 'measure_type_id' => 6, 'severity' => 'medium', 'frequency' => 'once'], // Temperatura Corporal
            ['min_value' => 12, 'max_value' => 20, 'range' => 'outrange', 'measure_type_id' => 7, 'severity' => 'medium', 'frequency' => 'daily'], // Frecuencia Respiratoria
            ['min_value' => 95, 'max_value' => 100, 'range' => 'outrange', 'measure_type_id' => 8, 'severity' => 'medium', 'frequency' => 'daily'], // Saturación de Oxígeno
        ];

        $createdConfigs = [];
        foreach ($measureConfigs as $idx => $config) {
            $patient = $patients[$idx % $patientCount] ?? $patients->first();
            $config['patient_id'] = $patient->id;
            $createdConfigs[] = MeasureConfig::create($config);
        }

        /*$measures = [
            ['value' => 80, 'measured_at' => '2024-01-15 08:00:00', 'notes' => 'Peso normal', 'measure_config_id' => 1],
            ['value' => 170, 'measured_at' => '2024-01-15 08:05:00', 'notes' => 'Altura registrada', 'measure_config_id' => 2],
            ['value' => '110/70', 'measured_at' => '2024-01-15 08:10:00', 'notes' => 'Presión arterial normal', 'measure_config_id' => 3],
            ['value' => 75, 'measured_at' => '2024-01-15 08:15:00', 'notes' => 'Frecuencia cardíaca normal', 'measure_config_id' => 4],
            ['value' => 90, 'measured_at' => '2024-01-15 08:20:00', 'notes' => 'Glucosa en sangre normal', 'measure_config_id' => 5],
            ['value' => 36.5, 'measured_at' => '2024-01-15 08:25:00', 'notes' => 'Temperatura corporal normal', 'measure_config_id' => 6],
            ['value' => 16, 'measured_at' => '2024-01-15 08:30:00', 'notes' => 'Frecuencia respiratoria normal', 'measure_config_id' => 7],
            ['value' => 98, 'measured_at' => '2024-01-15 08:35:00', 'notes' => 'Saturación de oxígeno normal', 'measure_config_id' => 8],
        ];*/
        $measures = [
            ['value' => 80, 'measured_at' => '2024-01-15 08:00:00', 'hour_measured' => '08:00:00', 'notes' => 'Peso normal'],
            ['value' => 170, 'measured_at' => '2024-01-15 08:05:00', 'hour_measured' => '08:05:00', 'notes' => 'Altura registrada'],
            ['value' => 90, 'measured_at' => '2024-01-15 08:20:00', 'hour_measured' => '08:20:00', 'notes' => 'Glucosa en sangre normal'],
            ['value' => 36.5, 'measured_at' => '2024-01-15 08:25:00', 'hour_measured' => '08:25:00', 'notes' => 'Temperatura corporal normal'],
            ['value' => 16, 'measured_at' => '2024-01-15 08:30:00', 'hour_measured' => '08:30:00', 'notes' => 'Frecuencia respiratoria normal'],
            ['value' => 98, 'measured_at' => '2024-01-15 08:35:00', 'hour_measured' => '08:35:00', 'notes' => 'Saturación de oxígeno normal'],
        ];
        foreach ($measures as $idx => $measure) {
            $config = $createdConfigs[$idx % count($createdConfigs)];
            $measure['measure_config_id'] = $config->id;
            Measure::create($measure);
        }
    }
}
