<template>
    <CardBox class="mt-2" isForm>
        <div class="flex items-center space-x-4 mb-4">
            <BaseIcon :path="mdiInformationSlabCircle" size="30" h="h-auto" w="w-auto"
                class="p-1 rounded-lg text-medic-500 bg-medic-50" />
            <Label class="text-md text-gray-700">Selecciona las opciones que apliquen:</Label>
        </div>

        <div class="grid grid-cols-1 md:grid-col-1 lg:grid-cols-3 gap-4 mr-2">
            <BaseFormField type="switch" label="¿Tiene diabetes?" v-model="form.diabetes"
                :error="form.errors.diabetes" />

            <BaseFormField type="select" label="Tipo de diabetes" v-model="form.diabetes_type"
                :error="form.errors.diabetes_type" placeholder="Seleccione su tipo de diabetes" :options="diabetesTypes"
                valueSelect="value" />

            <BaseFormField type="date" label="Fecha de diagnóstico" v-model="form.diabetes_diagnosis_date"
                :error="form.errors.diabetes_diagnosis_date" placeholder="Ingrese su fecha de diagnóstico" />

            <BaseFormField type="switch" label="¿Tiene hipertensión?" v-model="form.hypertension"
                :error="form.errors.hypertension" />

            <BaseFormField type="select" label="Tipo de hipertensión" v-model="form.hypertension_type"
                :error="form.errors.hypertension_type" placeholder="Seleccione su tipo de hipertensión" :options="types"
                valueSelect="value" />

            <BaseFormField type="date" label="Fecha de diagnóstico " v-model="form.hypertension_diagnosis_date"
                :error="form.errors.hypertension_diagnosis_date" placeholder="Ingrese su fecha de diagnóstico" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 mt-4 mr-2">
            <BaseFormField type="switch" label="¿Tiene Obesidad?" v-model="form.obesity" :error="form.errors.obesity"
                disabled />

            <BaseFormField type="select" label="Tipo de Obesidad" v-model="form.obesity_type"
                :error="form.errors.obesity_type" placeholder="Usted no tiene obesidad" :options="obesityTypes"
                disabled valueSelect="value" />

            <BaseFormField type="switch" label="¿Tiene Alergias?" v-model="form.allergies"
                :error="form.errors.allergies" />

            <BaseFormField type="textarea" label="Tipo de Alergias" v-model="form.allergies_details"
                :error="form.errors.allergies_details" placeholder="Descripción de las alergias" h="h-24"
                :maxLength="200" />
        </div>
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import Label from '@/Components/ui/label/Label.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiInformationSlabCircle } from '@mdi/js';


const form = inject('form');

const types = [
    { id: '1', name: 'Tipo I', value: 'I' },
    { id: '2', name: 'Tipo II', value: 'II' },
    { id: '3', name: 'Tipo III', value: 'III' },
];

const diabetesTypes = [
    { id: '1', name: 'Tipo 1', value: 'TP1' },
    { id: '2', name: 'Tipo 2', value: 'TP2' },
    { id: '3', name: 'Gestacional', value: 'Gest' },
];

const obesityTypes = [
    { id: '1', name: 'Sobrepeso', value: 'N' },
    { id: '2', name: 'Leve', value: 'I' },
    { id: '3', name: 'Moderada', value: 'II' },
    { id: '4', name: 'Grave', value: 'III' },
    { id: '5', name: 'Extrema', value: 'IV' },
];
</script>