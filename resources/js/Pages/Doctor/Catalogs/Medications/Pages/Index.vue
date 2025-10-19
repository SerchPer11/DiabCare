<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiAccountGroup" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
            :total="users?.meta?.total || 0" />

        <!--<UserRecords :users="users" :routeName="routeName" />-->
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
//import UserRecords from '../Components/UserRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiAccountGroup } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Usuarios'
    },
    filters: {
        type: Object,
        default: () => ({ search: '', rows: 10, withTrashed: false })
    },
    routeName: {
        type: String,
        default: 'users.'
    },
    users: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>