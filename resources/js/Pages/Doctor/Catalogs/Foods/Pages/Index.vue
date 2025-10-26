<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiFoodApple" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
            :total="foods?.meta?.total || 0" />

        <FoodRecords :foods="foods" :routeName="routeName" />
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FoodRecords from '../Components/FoodRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiFoodApple } from '@mdi/js';


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
        default: 'doctor.foods.'
    },
    foods: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>