<?php

namespace Database\Seeders\Catalogs;

use App\Models\Admin\Catalogs\FoodGroup;
use App\Models\Doctor\Catalogs\Food;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodGroups = [
            ['name' => 'Frutas'],
            ['name' => 'Verduras'],
            ['name' => 'Cereales'],
            ['name' => 'Lácteos'],
            ['name' => 'Carnes'],
            ['name' => 'Legumbres'],
            ['name' => 'Grasas saludables'],
            ['name' => 'Bebidas'],
        ];

        foreach ($foodGroups as $group) {
            FoodGroup::create($group);
        }

        Food::create([
            'name' => 'Manzana',
            'food_group_id' => 1,
            'calories' => 52,
            'protein' => 0.3,
            'carbohydrates' => 14,
            'fats' => 0.2,
            'fiber' => 2.4,
            'description' => 'Fruta dulce y crujiente, rica en fibra y antioxidantes.',
            'portion_size' => 150,
            'unit_id' => 1,
            'is_active' => true,
        ]);
    }
}
