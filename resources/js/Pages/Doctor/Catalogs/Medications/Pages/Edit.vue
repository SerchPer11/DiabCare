<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiMedication" main />

        <MedicationForm />

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
import MedicationForm from '../Components/MedicationForm.vue';
import { useMedication } from '../Composables/useMedication';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiMedication } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend, mdiDelete } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Medicamentos',
    },
    routeName: {
        type: String,
        default: 'doctor.medications.',
    },
    types: {
        type: Object,
        required: true,
    },
    presentations: {
        type: Object,
        required: true,
    },
    administrations: {
        type: Object,
        required: true,
    },
    units: {
        type: Object,
        required: true,
    },
    medication: {
        type: Object,
        required: true,
    },
});

const { updateForm, destroyForm, processing } = useMedication(props);
</script>