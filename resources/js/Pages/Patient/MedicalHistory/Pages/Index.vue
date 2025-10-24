<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :icon="mdiClipboardTextClock" :title="title" main/>

        <HistoryForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiContentSave " color="info" label="Guardar" @click="saveForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import HistoryForm from '../Components/HistoryForm.vue';
import { useHistory } from '../Composables/useHistory';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiContentSave, mdiClipboardTextClock } from '@mdi/js';
import CrudBanner from '@/Components/CrudBanner.vue';


const props = defineProps({
    title: {
        type: String,
        default: 'Medicamentos',
    },
    routeName: {
        type: String,
        default: 'doctor.medications.',
    },
    user: {
        type: Object,
        required: true,
    },
    history: {
        type: Object,
        default: null,
    },
});

const { saveForm, processing } = useHistory(props);
</script>