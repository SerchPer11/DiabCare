<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiHandHeart" main/>

        <RecomendationForm />

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
import RecomendationForm from '../Components/RecomendationForm.vue';
import { useRecomendation } from '../Composables/useRecomendation';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiHandHeart } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend, mdiDelete } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Alimentos',
    },
    routeName: {
        type: String,
        default: 'doctor.recomendations.',
    },
    priorities: {
        type: Object,
        required: true,
    },
    types: {
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
    recomendation: {
        type: Object,
        required: true,
    },
});

const { updateForm, destroyForm, processing } = useRecomendation(props);
</script>