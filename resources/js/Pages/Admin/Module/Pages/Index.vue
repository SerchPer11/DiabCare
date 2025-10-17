<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiViewModule" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
            :total="modules?.meta?.total || 0" />

        <ModuleRecords :modules="modules" :routeName="routeName" />
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ModuleRecords from '../Components/ModuleRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiViewModule } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Módulos'
    },
    filters: {
        type: Object,
        default: () => ({ search: '', rows: 10, withTrashed: false })
    },
    routeName: {
        type: String,
        default: 'modules.'
    },
    modules: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>