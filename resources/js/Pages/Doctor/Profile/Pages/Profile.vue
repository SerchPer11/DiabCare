<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <ProfileHead />

        <ProfileForm />

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
import ProfileForm from '../Components/ProfileForm.vue';
import { useProfile } from '../Composables/useProfile';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiContentSave } from '@mdi/js';
import ProfileHead from '../Components/ProfileHead.vue';


const props = defineProps({
    title: {
        type: String,
        default: 'Medicamentos',
    },
    routeName: {
        type: String,
        default: 'doctor.medications.',
    },
    specialties: {
        type: Object,
        required: true,
    },
    profile: {
        type: Object,
        required: true,
    },
});

const { saveForm, processing } = useProfile(props);
</script>