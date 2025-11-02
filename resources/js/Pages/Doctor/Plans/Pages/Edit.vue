<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiCalendarCheck" main />

        <PlanForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiDelete" color="danger" label="Eliminar" @click="destroyForm" :processing="processing" />
                <BaseButton :icon="mdiSend" color="info" label="Actualizar" @click="updateForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import PlanForm from '../Components/PlanForm.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { usePlan } from '../Composables/usePlan';
import { mdiCalendarCheck, mdiClose, mdiSend, mdiDelete } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Editar Plan'
    },
    plan: {
        type: Object,
        required: true
    },
    patients: {
        type: Array,
        required: true
    },
    planTypes: {
        type: Array,
        required: true
    },
    foods: {
        type: Array,
        default: () => []
    },
    exercises: {
        type: Array,
        default: () => []
    },
    routeName: {
        type: String,
        default: 'doctor.plans.'
    }
});

const { updateForm, destroyForm, processing } = usePlan(props);
</script>