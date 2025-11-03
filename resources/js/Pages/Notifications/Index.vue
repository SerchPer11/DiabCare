<template>
    <AuthenticatedLayout>

        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiBell" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
            :total="notifications?.meta?.total || 0" />

        <NotificationsRecords :notifications="notifications" :routeName="routeName" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NotificationsRecords from './Components/NotificationsRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiBell } from '@mdi/js';

const props = defineProps({
    notifications:{
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: 'Notificaciones'
    },
    filters: {
        type: Object,
        default: () => ({ search: '', rows: 10, withTrashed: false })
    },
    routeName: {
        type: String,
        default: 'notifications.'
    },
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>