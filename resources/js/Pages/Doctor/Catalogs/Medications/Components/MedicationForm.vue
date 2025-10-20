<template>
    <CardBox class="mt-2" isForm>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="input" label="Nombre del medicamento" v-model="form.name" :error="form.errors.name"
                placeholder="Ejm: Paracetamol" required :maxLength="100" />

            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <BaseFormField type="number" label="Concentración" v-model="form.concentration"
                    :error="form.errors.concentration" placeholder="Ejm: 500" required />

                <BaseFormField type="select" label="Unidades" v-model="form.unit_id" :error="form.errors.unit_id"
                    :options="units" required placeholder="Selecciona una unidad" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <BaseFormField type="select" label="Tipo de medicamento" v-model="form.medication_type_id"
                :error="form.errors.medication_type_id" :options="types" required placeholder="Selecciona un tipo" />

            <BaseFormField type="select" label="Presentación" v-model="form.medication_presentation_id"
                :error="form.errors.medication_presentation_id" :options="presentations" required placeholder="Selecciona una presentación" />

            <BaseFormField type="select" label="Vía de administración" v-model="form.medication_administration_id"
                :error="form.errors.medication_administration_id" :options="administrations" required placeholder="Selecciona una vía de administración" />
        </div>

        <BaseFormField type="textarea" label="Indicaciones" v-model="form.indications" :error="form.errors.indications"
            placeholder="Ejm: Como y cuando usarlo..." required :maxLength="150" h="h-24" />
        <BaseFormField type="textarea" label="Contraindicaciones" v-model="form.contraindications"
            :error="form.errors.contraindications" placeholder="Ejm: Advertencias, interacciones, efectos secundarios" required :maxLength="150" h="h-24" />

        <Label class="block text-md font-medium text-gray-600 mb-2 mt-2">Imagenes del medicamento</Label>
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
const types = inject('types');
const presentations = inject('presentations');
const administrations = inject('administrations');
const units = inject('units');

</script>