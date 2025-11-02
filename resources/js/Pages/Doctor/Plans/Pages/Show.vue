<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <PlanDetails 
            :plan="plan"
            :routeName="routeName"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlanDetails from '../Components/PlanDetails.vue';
import CrudHead from '@/Components/CrudHead.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Detalle del Plan'
    },
    plan: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'doctor.plans.'
    }
});
</script>