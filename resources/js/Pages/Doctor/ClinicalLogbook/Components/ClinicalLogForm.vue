<template>
    <CardBox>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Información Básica -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
            </div>
            <BaseFormField 
                type="select"
                label="Paciente" 
                v-model="form.patient_id"
                :error="form.errors.patient_id"
                :options="patients"
                valueSelect="value"
                valueOption="label"
                placeholder="Seleccionar paciente..."
                required
            />

            <BaseFormField 
                type="select"
                label="Tipo de Evento" 
                v-model="form.event_type"
                :error="form.errors.event_type"
                :options="eventTypes"
                valueSelect="value"
                valueOption="label"
                placeholder="Seleccionar tipo..."
                required
            />

            <BaseFormField 
                type="input"
                label="Título" 
                v-model="form.title"
                :error="form.errors.title"
                placeholder="Título descriptivo de la entrada..."
                class="lg:col-span-2"
                required
            />

            <!-- Descripción -->
            <BaseFormField 
                type="textarea"
                label="Descripción" 
                v-model="form.description"
                :error="form.errors.description"
                placeholder="Descripción detallada del evento..."
                rows="4"
                class="lg:col-span-2"
                required
            />

            <!-- Notas -->
            <BaseFormField 
                type="textarea"
                label="Notas Adicionales" 
                v-model="form.notes"
                :error="form.errors.notes"
                placeholder="Notas adicionales, observaciones, recomendaciones..."
                rows="3"
                class="lg:col-span-2"
            />

            <!-- Relación con otros elementos -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Relación con Otros Elementos</h3>
            </div>

            <BaseFormField 
                type="select"
                label="Tipo de Relación" 
                v-model="form.related_type"
                :error="form.errors.related_type"
                :options="relatedTypeOptions"
                valueSelect="value"
                valueOption="label"
                placeholder="Sin relación..."
                @change="onRelatedTypeChange"
            />

            <BaseFormField 
                v-if="form.related_type"
                type="select"
                label="Elemento Relacionado" 
                v-model="form.related_id"
                :error="form.errors.related_id"
                :options="getRelatedOptions()"
                valueSelect="value"
                valueOption="label"
                placeholder="Seleccionar elemento..."
            />

            <!-- Archivos adjuntos -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivos Adjuntos</h3>
            </div>

            <div class="lg:col-span-2">
                <Label class="block text-md text-gray-600 mb-2">Imágenes relacionadas</Label>
                <ImageUploadCarousel :form="form" />
            </div>

            <div class="lg:col-span-2">
                <FileUpload 
                    :form="form" 
                    title="Archivos relacionados" 
                    description="Puedes subir archivos en formato PDF o DOCX."
                    icon-classes="bg-forest-400 text-mono-100 rounded-lg"
                />
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import ImageUploadCarousel from '@/Components/ImageUploadCarousel.vue';
import FileUpload from '@/Components/FileUpload.vue';
import Label from '@/Components/ui/label/Label.vue';

// Inject dependencies from composable
const form = inject('form');
const patients = inject('patients');
const eventTypes = inject('eventTypes');
const appointments = inject('appointments');
const plans = inject('plans');
const medications = inject('medications');

// Related type options
const relatedTypeOptions = [
    { value: 'App\\Models\\Appointment', label: 'Cita Médica' },
    { value: 'App\\Models\\Plan', label: 'Plan de Tratamiento' },
    { value: 'App\\Models\\Doctor\\Catalogs\\Medication', label: 'Medicamento' },
];

// Methods
const onRelatedTypeChange = () => {
    form.related_id = null;
};

const getRelatedOptions = () => {
    switch (form.related_type) {
        case 'App\\Models\\Appointment':
            return appointments || [];
        case 'App\\Models\\Plan':
            return plans || [];
        case 'App\\Models\\Doctor\\Catalogs\\Medication':
            return medications || [];
        default:
            return [];
    }
};
</script>