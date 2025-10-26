<?php

namespace Database\Seeders\Catalogs;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Catalogs\MedicationType;
use App\Models\Admin\Catalogs\MedicationPresentation;
use App\Models\Admin\Catalogs\MedicationAdministration;
use App\Models\Admin\Catalogs\Unit;
use App\Models\Doctor\Catalogs\Medication;
use Illuminate\Support\Facades\DB;

class Medications extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // --- Creación de Unidades ---
            $units = [
                ['name' => 'Miligramos', 'abbreviation' => 'mg', 'type' => 'med'],
                ['name' => 'Gramos', 'abbreviation' => 'g', 'type' => 'stnd'],
                ['name' => 'Mililitros', 'abbreviation' => 'ml', 'type' => 'med'],
                ['name' => 'Gota', 'abbreviation' => 'gta', 'type' => 'med'],
                ['name' => 'Unidad', 'abbreviation' => 'u', 'type' => 'med'],
                ['name' => 'Pieza', 'abbreviation' => 'pz', 'type' => 'stnd'],
                ['name' => 'Taza', 'abbreviation' => 'pz', 'type' => 'food'],
                ['name' => 'Cucharada', 'abbreviation' => 'cda', 'type' => 'food'],
                ['name' => 'Cucharadita', 'abbreviation' => 'cdta', 'type' => 'food'],
            ];
            foreach ($units as $unit) {
                Unit::create($unit);
            }

            // --- Creación de Tipos de Medicamento ---
            $medicationTypes = [
                ['name' => 'Antibiotico'],
                ['name' => 'Analgésico'],
                ['name' => 'Antiinflamatorio'],
                ['name' => 'Antipirético'],
                ['name' => 'Antihistamínico'],
                ['name' => 'Descongestionante'],
                ['name' => 'Antitusivo'],
                ['name' => 'Expectorante'],
                ['name' => 'Antiemético'],
                ['name' => 'Antidiarreico'],
                ['name' => 'Laxante'],
                ['name' => 'Antihipertensivo'],
                ['name' => 'Diurético'],
                ['name' => 'Hipoglucemiante'],
                ['name' => 'Vitamínico'],
                ['name' => 'Mineral'],
                ['name' => 'Suplemento alimenticio'],
                ['name' => 'Vacuna'],
                ['name' => 'Antiglucemiante'],
                ['name' => 'Anticoagulante']
            ];
            foreach ($medicationTypes as $type) {
                MedicationType::create($type);
            }

            // --- Creación de Presentaciones de Medicamento ---
            $presentations = [
                ['name' => 'Tableta'],
                ['name' => 'Capsula'],
                ['name' => 'Jarabe'],
                ['name' => 'Suspensión'],
                ['name' => 'Inyección'],
                ['name' => 'Pomada'],
                ['name' => 'Crema'],
                ['name' => 'Gotas'],
                ['name' => 'Spray'],
                ['name' => 'Inhalador'],
                ['name' => 'Supositorio'],
                ['name' => 'Parche'],
                ['name' => 'Polvo'],
                ['name' => 'Solución']
            ];
            foreach ($presentations as $presentation) {
                MedicationPresentation::create($presentation);
            }

            // --- Creación de Vías de Administración ---
            $administrations = [
                ['name' => 'Oral'],
                ['name' => 'Intravenosa'],
                ['name' => 'Intramuscular'],
                ['name' => 'Subcutánea'],
                ['name' => 'Sublingual'],
                ['name' => 'Tópica'],
                ['name' => 'Inhalatoria'],
                ['name' => 'Rectal'],
                ['name' => 'Vaginal'],
                ['name' => 'Oftálmica'],
                ['name' => 'Otic'], // Correcto es "Ótica"
                ['name' => 'Nasal']
            ];
            foreach ($administrations as $administration) {
                MedicationAdministration::create($administration);
            }

            // --- Creación de Medicamentos Específicos ---
            Medication::create([
                'name' => 'Paracetamol',
                'concentration' => 500,
                'indications' => 'Alivio del dolor y reducción de la fiebre',
                'contraindications' => 'Hipersensibilidad al paracetamol, insuficiencia hepática grave',
                'medication_type_id' => 4, // Antipirético
                'medication_presentation_id' => 1, // Tableta
                'medication_administration_id' => 1, // Oral
                'unit_id' => 1, // mg
            ]);

            Medication::create([
                'name' => 'Ibuprofeno',
                'concentration' => 400,
                'indications' => 'Alivio del dolor y la inflamación',
                'contraindications' => 'Hipersensibilidad, úlcera péptica',
                'medication_type_id' => 3, // Antiinflamatorio
                'medication_presentation_id' => 1, // Tableta
                'medication_administration_id' => 1, // Oral
                'unit_id' => 1, // mg
            ]);

            // Agrega más medicamentos aquí si es necesario
        });
    }
}
