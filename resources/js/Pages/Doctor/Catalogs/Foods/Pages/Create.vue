<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiFoodApple" main />

        <FoodForm />

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
import FoodForm from '../Components/FoodForm.vue';
import { useFood } from '../Composables/useFood';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiFoodApple } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Medicamentos',
    },
    routeName: {
        type: String,
        default: 'doctor.medications.',
    },
    foodGroups: {
        type: Object,
        required: true,
    },
    units: {
        type: Object,
        required: true,
    },
});

const { saveForm, processing } = useFood(props);
</script>