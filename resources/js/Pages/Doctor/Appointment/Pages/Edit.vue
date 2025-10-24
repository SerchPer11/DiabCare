<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiCalendar" />

        <AppointmentForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiDelete" color="danger" label="Eliminar" @click="destroyForm" :processing="processing" />
                <BaseButton :icon="mdiSend" color="info" label="Guardar" @click="updateForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import AppointmentForm from '../Components/AppointmentForm.vue';
import { useAppointment } from '../Composables/useAppointment';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiCalendar } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend, mdiDelete } from '@mdi/js';
import { provide } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Editar Cita',
    },
    routeName: {
        type: String,
        default: 'doctor.appointments.',
    },
    statusList: {
        type: Object,
        required: true,
    },
    doctors: {
        type: Object,
        required: true,
    },
    patients: {
        type: Object,
        required: true,
    },
    appointment: {
        type: Object,
        required: true,
    },
    modalityOptions: {
        type: Array,
        required: true,
    },
});

const { updateForm, destroyForm, processing } = useAppointment(props);
provide('statusList', props.statusList);
provide('doctors', props.doctors);
provide('patients', props.patients);
provide('modalityOptions', props.modalityOptions);
</script>
