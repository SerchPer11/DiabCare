<template>
    <CardBox class="mt-2" isForm>
        <div class="flex items-center space-x-4 mb-4">
            <BaseIcon :path="mdiInformationSlabCircle" size="30" h="h-auto" w="w-auto"
                class="p-1 rounded-lg text-medic-500 bg-medic-50" />
            <Label class="text-md text-gray-700">Información general</Label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="select" label="Paciente" v-model="form.patient_id" :error="form.errors.patient_id"
                placeholder="Seleccione un paciente" required :options="patients" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <BaseFormField type="date" label="Fecha" v-model="form.measured_at" :error="form.errors.measured_at"
                    required />

                <BaseFormField type="time" label="Hora" v-model="form.hour_measured" :error="form.errors.hour_measured"
                    required />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <BaseFormField type="number" label="Valor ingresado" v-model="form.value" :error="form.errors.value"
                placeholder="Ingrese el valor" required />

            <BaseFormField type="select" label="Unidad de medida" v-model="form.measure_type_id"
                :error="form.errors.measure_type_id" placeholder="Seleccione una unidad de medida" required
                :options="types" valueOption="unit" />

            <BaseFormField type="select" label="Tipo de monitoreo" v-model="form.measure_type_id"
                :error="form.errors.measure_type_id" placeholder="Selecciona un tipo de monitoreo" required
                :options="types" />
        </div>

        <BaseFormField type="textarea" label="Observaciones" v-model="form.notes" :error="form.errors.notes"
            placeholder="Ejm: Sintomas, contexto del registro, etc." required :maxLength="1000" h="h-24" />

        <CardBox class="mt-4">
            <div class="flex items-center space-x-4 mb-1">
                <BaseIcon :path="mdiAlert" size="30" h="h-auto" w="w-auto"
                    class="p-1 rounded-lg text-medic-500 bg-medic-50" />
                <Label class="text-md text-gray-700">Umbrales</Label>
            </div>
            <Label class="text-sm text-gray-700">Establece límites para las mediciones de este tipo.</Label><br>
            <Label class="text-xs text-gray-500">(La configuración se guardara para este tipo de medición.)</Label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 mb-4">
                <BaseFormField type="number" label="Rango objetivo (minimo)" v-model="form.min_value"
                    :error="form.errors.min_value" placeholder="Ingrese el valor" required />

                <BaseFormField type="number" label="Rango objetivo (maximo)" v-model="form.max_value"
                    :error="form.errors.max_value" placeholder="Ingrese el valor" required />

                <BaseFormField type="select" label="Marcar como anormal si" v-model="form.range"
                    :error="form.errors.range" placeholder="Seleccione una opción " required :options="ranges"
                    valueOption="label" valueSelect="value" />

                <BaseFormField type="select" label="Severidad" v-model="form.severity" :error="form.errors.severity"
                    placeholder="Seleccione una opción " required :options="severities" valueOption="label"
                    valueSelect="value" />
            </div>
            <BaseFormField type="select" label="Frecuencia" v-model="form.frequency" :error="form.errors.frequency"
                placeholder="Seleccione la frecuencia" required :options="frequencies" valueOption="label"
                valueSelect="value" />
        </CardBox>

    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import { mdiAlert, mdiInformationSlabCircle } from '@mdi/js';
import Label from '@/Components/ui/label/Label.vue';


const form = inject('form');
const patients = inject('patients');
const types = inject('types');
const frequencies = inject('frequencies');
const severities = inject('severities');
const ranges = inject('ranges');

</script>