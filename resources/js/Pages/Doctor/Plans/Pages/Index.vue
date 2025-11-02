<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner 
            :title="title" 
            :icon="mdiCalendarCheck" 
            :routeName="routeName" 
            main 
            @apply-filters="applyFilters"
            @clear-filters="clearFilters" 
            v-model:search="filters.search" 
            v-model:rows="filters.rows"
            :total="plans?.meta?.total || 0" 
        />

        <PlanRecords 
            :plans="plans" 
            :routeName="routeName" 
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlanRecords from '../Components/PlanRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiCalendarCheck } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Planes de Pacientes'
    },
    filters: {
        type: Object,
        default: () => ({ 
            search: '', 
            rows: 10
        })
    },
    routeName: {
        type: String,
        default: 'doctor.plans.'
    },
    plans: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>