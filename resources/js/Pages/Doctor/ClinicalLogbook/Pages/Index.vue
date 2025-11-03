<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner 
            :title="title" 
            :icon="mdiBookOpenPageVariant" 
            :routeName="routeName" 
            main 
            @apply-filters="applyFilters"
            @clear-filters="clearFilters" 
            v-model:search="filters.search" 
            v-model:rows="filters.rows"
            :total="entries?.meta?.total || 0" 
        />

        <ClinicalLogRecords 
            :entries="entries" 
            :routeName="routeName" 
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ClinicalLogRecords from '../Components/ClinicalLogRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiBookOpenPageVariant } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Bitácora Clínica'
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
        default: 'doctor.clinical-logbook.'
    },
    entries: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>