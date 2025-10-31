<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar un doctor para asignar como creador
        $doctor = User::role('doctor')->first();

        // Encuesta de Calidad de Vida en Diabetes
        $survey1 = Survey::create([
            'title' => 'Encuesta de Calidad de Vida - Diabetes',
            'description' => 'Esta encuesta evalúa cómo la diabetes afecta diferentes aspectos de tu vida diaria.',
            'instructions' => 'Por favor responde todas las preguntas basándote en tu experiencia en las últimas 2 semanas. Usa la escala donde 1 = Totalmente en desacuerdo y 5 = Totalmente de acuerdo.',
            'is_active' => true,
            'created_by' => $doctor?->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        $questions1 = [
            'Me siento satisfecho/a con mi manejo actual de la diabetes',
            'La diabetes interfiere con mi vida familiar',
            'Me siento cómodo/a hablando de mi diabetes con otros',
            'Tengo miedo de las complicaciones de la diabetes',
            'Mi tratamiento para la diabetes es demasiado restrictivo',
            'Me preocupa tener hipoglucemia (azúcar bajo)',
            'Mi diabetes me causa estrés emocional',
            'Siento que tengo suficiente apoyo de mi familia',
            'Me siento confiado/a manejando mi diabetes',
            'La diabetes limita mis actividades físicas'
        ];

        foreach ($questions1 as $index => $question) {
            SurveyQuestion::create([
                'survey_id' => $survey1->id,
                'question' => $question,
                'order' => $index + 1,
                'is_required' => true,
            ]);
        }

        // Encuesta de Adherencia al Tratamiento
        $survey2 = Survey::create([
            'title' => 'Evaluación de Adherencia al Tratamiento',
            'description' => 'Esta encuesta nos ayuda a entender qué tan bien sigues tu plan de tratamiento.',
            'instructions' => 'Responde honestamente sobre tu comportamiento en el último mes. 1 = Nunca, 5 = Siempre.',
            'is_active' => true,
            'created_by' => $doctor?->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(3),
        ]);

        $questions2 = [
            'Tomo mis medicamentos a la hora indicada',
            'Sigo mi dieta recomendada',
            'Hago ejercicio según las indicaciones médicas',
            'Monitoreo mi glucosa según el plan',
            'Asisto a mis citas médicas programadas',
            'Leo las etiquetas de los alimentos antes de consumirlos',
            'Llevo un registro de mis niveles de glucosa',
            'Tomo decisiones alimentarias saludables cuando como fuera'
        ];

        foreach ($questions2 as $index => $question) {
            SurveyQuestion::create([
                'survey_id' => $survey2->id,
                'question' => $question,
                'order' => $index + 1,
                'is_required' => true,
            ]);
        }
    }
}
