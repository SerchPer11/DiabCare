<template>
    <CardBox class="mt-2" isForm>
        <BaseFormField type="input" label="Titulo de la recomendación" v-model="form.title" :error="form.errors.title"
                placeholder="Ejm: Ingerir frutas y verduras"  :maxLength="100" disabled />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="select" label="Tipo de recomendación" v-model="form.recomendation_type_id" :error="form.errors.recomendation_type_id"
                placeholder="Ejm: Ingerir frutas y verduras"  :options="types"  disabled/>

            <BaseFormField type="select" label="Prioridad" v-model="form.priority" :error="form.errors.priority"
                placeholder="Ejm: Ingerir frutas y verduras"  :options="priorities" valueSelect="value" valueOption="label" disabled />
        </div>

        <BaseFormField type="textarea" label="Contenido de la recomendación" v-model="form.content" :error="form.errors.content"
                placeholder="Ejm: Se recomienda ingerir frutas y verduras diariamente para mantener una buena salud."  :maxLength="1000" h="h-48" disabled/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="date" label="Fecha de inicio" v-model="form.start_date" :error="form.errors.start_date"
                placeholder="Seleccione la fecha de inicio"  disabled/>

            <BaseFormField type="date" label="Fecha de fin" v-model="form.end_date" :error="form.errors.end_date"
                placeholder="Seleccione la fecha de fin" disabled/>

            <BaseFormField type="select" label="Doctor" v-model="form.doctor_id" :error="form.errors.doctor_id"
                placeholder="Seleccione un doctor"  :options="doctors"  disabled/>

            <BaseFormField type="select" label="Paciente" v-model="form.patient_id" :error="form.errors.patient_id"
                placeholder="Seleccione un paciente"  :options="patients"  disabled/>
        </div>
        <Label class="block text-lg text-gray-700 mt-2">Archivos adjuntos</Label>
        <Label class="block text-md text-gray-600 mt-2">Imagenes relacionadas</Label>
        <ImageUploadCarousel :form="form" disabled/>

        <FileUpload 
            :form="form" 
            title="Archivos relacionados" 
            description="Puedes subir archivos en formato PDF o DOCX."
            icon-classes="bg-forest-400 text-mono-100 rounded-lg"
        disabled/>

    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import ImageUploadCarousel from '@/Components/ImageUploadCarousel.vue';
import Label from '@/Components/ui/label/Label.vue';
import FileUpload from '@/Components/FileUpload.vue';


const form = inject('form');
const types = inject('types');
const priorities = inject('priorities');
const patients = inject('patients');
const doctors = inject('doctors');

</script>