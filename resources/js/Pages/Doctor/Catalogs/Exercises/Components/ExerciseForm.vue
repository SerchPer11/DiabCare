<template>
    <CardBox class="mt-2" isForm>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="input" label="Nombre del ejercicio" v-model="form.name" :error="form.errors.name"
                placeholder="Ejemplo: Sentadilla" required :maxLength="100" />

            <BaseFormField type="select" label="Intensidad" v-model="form.intensity" :error="form.errors.intensity"
                :options="intensityOptions" required />

            <BaseFormField type="number" label="Series" v-model="form.sets" :error="form.errors.sets"
                placeholder="Ejemplo: 3" required min="1" />

            <BaseFormField type="number" label="Repeticiones" v-model="form.repetitions" :error="form.errors.repetitions"
                placeholder="Ejemplo: 12" required min="1" />

            <BaseFormField type="number" label="Descanso (segundos)" v-model="form.rest_seconds" :error="form.errors.rest_seconds"
                placeholder="Ejemplo: 30" required min="0" />

            <BaseFormField type="number" label="Duración (minutos)" v-model="form.duration_minutes" :error="form.errors.duration_minutes"
                placeholder="Ejemplo: 30" required min="0" />

            <BaseFormField type="input" label="Equipamiento" v-model="form.equipment" :error="form.errors.equipment"
                placeholder="Ejemplo: Mancuernas, Banda, Ninguno" :maxLength="100" />

            <BaseFormField type="textarea" label="Contraindicaciones" v-model="form.contraindications" :error="form.errors.contraindications"
                placeholder="Especifica si existen" />

            <BaseFormField type="textarea" label="Notas" v-model="form.notes" :error="form.errors.notes"
                placeholder="Notas adicionales" />

            <BaseFormField type="textarea" label="Descripción" v-model="form.description" :error="form.errors.description"
                placeholder="Describe el ejercicio" />

            <BaseFormField type="number" label="Calorías quemadas" v-model="form.calories_burned" :error="form.errors.calories_burned"
                placeholder="Ejemplo: 100" min="0" />

            <BaseFormField type="select" label="Tipo de ejercicio" v-model="form.exercise_type_id" :error="form.errors.exercise_type_id"
                :options="exerciseTypes" optionValue="id" optionLabel="name" required />
            <BaseFormField type="select" label="Estado" v-model="form.is_active" :error="form.errors.is_active"
                :options="isActiveOptions" />
        </div>
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject, computed } from 'vue';
import CardBox from '@/Components/CardBox.vue';

const form = inject('form');
const exerciseTypesRaw = inject('exerciseTypes');

const exerciseTypes = computed(() => {
    if (Array.isArray(exerciseTypesRaw)) return exerciseTypesRaw;
    if (exerciseTypesRaw?.data) return exerciseTypesRaw.data;
    return [];
});

const intensityOptions = [
    'Baja',
    'Media', 
    'Alta'
];

const isActiveOptions = [
    'Activo',
    'Inactivo'
];
</script>
