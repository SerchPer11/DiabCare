<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiHandHeart" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
                        :total="recomendations?.meta?.total || 0" />

        <RecomendationRecords :recomendations="recomendations" :routeName="routeName" />
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecomendationRecords from '../Components/RecomendationsRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiHandHeart } from '@mdi/js';


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
        default: 'doctor.recomendations.'
    },
    recomendations: {
        type: Object,
        required: true
    }
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>