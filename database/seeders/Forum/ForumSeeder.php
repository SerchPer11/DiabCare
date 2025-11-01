<?php

namespace Database\Seeders\Forum;

use App\Models\Forum\ForumStatus;
use App\Models\Forum\Forum;
use App\Models\Forum\ForumAnswer;
use App\Models\Forum\ForumCategory;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ForumStatus::insert([
            ['name' => 'Abierto', 'description' => 'El foro está abierto para discusión.'],
            ['name' => 'Cerrado', 'description' => 'El foro está cerrado para nuevas publicaciones.'],
        ]);

        ForumCategory::insert([
            ['name' => 'Nutrición'],
            ['name' => 'Ejercicio'],
            ['name' => 'Medicación'],
            ['name' => 'Soporte emocional'],
            ['name' => 'Otros'],
        ]);

        Forum::create([
            'title' => '¿Cómo manejar la diabetes tipo 2?',
            'content' => 'Estoy buscando consejos sobre cómo manejar la diabetes tipo 2 a través de la dieta y el ejercicio.',
            'user_id' => 1,
            'forum_status_id' => 2,
            'category_id' => 4,
        ]);

        Forum::create([
            'title' => 'Ideas de recetas saludables para diabéticos',
            'content' => 'Estoy buscando recetas que sean saludables y adecuadas para diabéticos.',
            'user_id' => 2,
            'forum_status_id' => 1,
            'category_id' => 1,
        ]);

        ForumAnswer::create([
            'answer' => 'Es importante mantener una dieta equilibrada y hacer ejercicio regularmente. Consulta a tu médico para un plan personalizado.',
            'forum_id' => 1,
            'user_id' => 2,
        ]);
        ForumAnswer::create([
            'answer' => 'También puedes considerar unirte a un grupo de apoyo para compartir experiencias y consejos con otros.',
            'forum_id' => 1,
            'user_id' => 3,
        ]);
        ForumAnswer::create([
            'answer' => 'Deberias consultar a un profesional de la salud.',
            'forum_id' => 1,
            'user_id' => 2,
        ]);

        ForumAnswer::create([
            'answer' => 'Es importante mantener una dieta equilibrada y hacer ejercicio regularmente. Consulta a tu médico para un plan personalizado.',
            'forum_id' => 2,
            'user_id' => 3,
        ]);
        ForumAnswer::create([
            'answer' => 'También puedes considerar unirte a un grupo de apoyo para compartir experiencias y consejos con otros.',
            'forum_id' => 2,
            'user_id' => 1,
        ]);
    }
}
