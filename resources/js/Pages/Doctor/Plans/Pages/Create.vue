<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiCalendarCheck" main />

        <PlanForm 
            :patients="patients" 
            :planTypes="planTypes" 
            :foods="foods" 
            :exercises="exercises"
        />

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
import PlanForm from '../Components/PlanForm.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { usePlan } from '../Composables/usePlan';
import { mdiCalendarCheck, mdiClose, mdiSend } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Crear Plan'
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

const { saveForm, processing } = usePlan(props);
</script>