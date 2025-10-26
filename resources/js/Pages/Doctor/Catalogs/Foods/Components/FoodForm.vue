<template>
    <CardBox class="mt-2" isForm>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="input" label="Nombre del medicamento" v-model="form.name" :error="form.errors.name"
                placeholder="Ejm: Paracetamol" required :maxLength="100" />

            <BaseFormField type="select" label="Grupo alimenticio" v-model="form.food_group_id"
                :error="form.errors.food_group_id" :options="foodGroups" required placeholder="Selecciona un grupo" />

            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <BaseFormField type="number" label="Porción estándar" v-model="form.portion_size"
                    :error="form.errors.portion_size" placeholder="Ejm: 100" required :maxLength="10" />

                <BaseFormField type="select" label="Unidades" v-model="form.unit_id" :error="form.errors.unit_id"
                    :options="units" required placeholder="Selecciona una unidad" />
            </div>

            <BaseFormField type="input" label="Kilocalorías" v-model="form.calories" :error="form.errors.calories"
                placeholder="Ejm: 100" required :maxLength="10" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="input" label="Carbohidratos (g)" v-model="form.carbohydrates"
                :error="form.errors.carbohydrates" placeholder="Ejm: 100" required :maxLength="10" />

            <BaseFormField type="input" label="Fibra (g)" v-model="form.fiber" :error="form.errors.fiber"
                placeholder="Ejm: 100" required :maxLength="10" />

            <BaseFormField type="input" label="Proteínas (g)" v-model="form.protein" :error="form.errors.protein"
                placeholder="Ejm: 100" required :maxLength="10" />

            <BaseFormField type="input" label="Grasas (g)" v-model="form.fats" :error="form.errors.fats"
                placeholder="Ejm: 100" required :maxLength="10" />
        </div>
        <BaseFormField type="textarea" label="Descripción" v-model="form.description"
            :error="form.errors.description" placeholder="Descripción del alimento o recomendaciones especificas" :maxLength="1000" h="h-48" />

        <Label class="block text-md font-medium text-gray-600 mt-2">Imagenes del alimento</Label>
        <ImageUploadCarousel :form="form" />

    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import ImageUploadCarousel from '@/Components/ImageUploadCarousel.vue';
import Label from '@/Components/ui/label/Label.vue';


const form = inject('form');
const foodGroups = inject('foodGroups');
const units = inject('units');

</script>