<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiBookOpenPageVariant" main />

        <ClinicalLogForm 
            :patients="patients" 
            :eventTypes="eventTypes" 
            :appointments="appointments"
            :plans="plans"
            :medications="medications"
        />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiSend" color="info" label="Actualizar" @click="updateForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import ClinicalLogForm from '../Components/ClinicalLogForm.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { useClinicalLogBook } from '../Composables/useClinicalLogBook';
import { mdiBookOpenPageVariant, mdiClose, mdiSend } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Editar Entrada de Bitácora'
    },
    entry: {
        type: Object,
        required: true
    },
    patients: {
        type: Array,
        required: true
    },
    eventTypes: {
        type: Array,
        required: true
    },
    appointments: {
        type: Array,
        default: () => []
    },
    plans: {
        type: Array,
        default: () => []
    },
    medications: {
        type: Array,
        default: () => []
    },
    routeName: {
        type: String,
        default: 'doctor.clinical-logbook.'
    }
});

const { updateForm, processing } = useClinicalLogBook(props);
</script>