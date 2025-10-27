<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiClipboardPulse " main />

        <MeasureForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiSend" color="info" label="Guardar" @click="saveForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import MeasureForm from '../Components/MeasureForm.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiClipboardPulse  } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend } from '@mdi/js';
import { useMeasure } from '../Composables/useMeasure';


const props = defineProps({
    title: {
        type: String,
        default: 'Mediciones',
    },
    routeName: {
        type: String,
        default: 'measures.',
    },
    patients: {
        type: Object,
        required: true,
    },
    types: {
        type: Object,
        required: true,
    },
    config: {
        type: Object,
        required: false,
    },
    frequencies: {
        type: Array,
        required: true,
    },
    severities: {
        type: Array,
        required: true,
    },
    ranges: {
        type: Array,
        required: true,
    },
});

const { saveForm, processing } = useMeasure(props);
</script>