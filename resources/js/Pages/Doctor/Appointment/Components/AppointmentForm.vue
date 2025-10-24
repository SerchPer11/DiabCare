
<template>
    <CardBox class="mt-2" isForm>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseFormField type="select" label="Paciente" v-model="form.patient_id" :error="form.errors.patient_id"
                :options="patients" valueSelect="id" valueOption="full_name" placeholder="Seleccione paciente" required />

            <BaseFormField type="select" label="Médico" v-model="form.doctor_id" :error="form.errors.doctor_id"
                :options="doctors" valueSelect="id" valueOption="full_name" placeholder="Seleccione médico" required />

            <BaseFormField type="date" label="Fecha de la cita" v-model="form.date" :error="form.errors.date" required />

            <BaseFormField type="time" label="Hora de la cita" v-model="form.time" :error="form.errors.time" required />

            <BaseFormField type="select" label="Modalidad" v-model="form.modality" :error="form.errors.modality"
                placeholder="Seleccione modalidad" required :options="modalityOptions" valueSelect="value" valueOption="label" />

            <BaseFormField type="textarea" label="Motivo de la cita" v-model="form.reason" :error="form.errors.reason" required 
                placeholder="Redacte el motivo de la cita" />

            <BaseFormField type="textarea" label="Notas adicionales" v-model="form.additional_notes" :error="form.errors.additional_notes" 
                placeholder="Redacte notas adicionales" />

            <BaseFormField type="input" label="Link videollamada" v-model="form.video_call_link" :error="form.errors.video_call_link"
                placeholder="https://..." />

            <BaseFormField type="select" label="Estado" v-model="form.appointment_status_id" :error="form.errors.appointment_status_id"
                placeholder="Seleccione estado" :options="statusList" valueSelect="id" valueOption="name" required />
        </div>
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject, computed } from 'vue';
import CardBox from '@/Components/CardBox.vue';

const form = inject('form');
const statusListRaw = inject('statusList');
const doctorsRaw = inject('doctors');
const patientsRaw = inject('patients');
const modalityOptionsRaw = inject('modalityOptions');

const statusList = computed(() => {
    if (Array.isArray(statusListRaw)) return statusListRaw;
    if (statusListRaw?.data) return statusListRaw.data;
    return [];
});

const doctors = computed(() => {
    if (Array.isArray(doctorsRaw)) return doctorsRaw;
    if (doctorsRaw?.data) return doctorsRaw.data;
    return [];
});

const patients = computed(() => {
    if (Array.isArray(patientsRaw)) return patientsRaw;
    if (patientsRaw?.data) return patientsRaw.data;
    return [];
});

const modalityOptions = computed(() => {
    if (Array.isArray(modalityOptionsRaw)) return modalityOptionsRaw;
    return [
        { value: 'Presencial', label: 'Presencial' },
        { value: 'Virtual', label: 'Virtual' }
    ];
});
</script>
