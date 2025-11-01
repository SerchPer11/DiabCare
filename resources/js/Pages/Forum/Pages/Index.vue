<template>
    <ForumLayout>
        <CrudHead :title="title" />

        <WelcomeBanner :title="title" :icon="mdiForum" :routeName="routeName" main @apply-filters="applyFilters"
            @clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
            :total="questions?.meta?.total || 0" />

        <ForumRecords :questions="questions" :routeName="routeName" />
    </ForumLayout>

</template>

<script setup>
import ForumRecords from '../Components/ForumRecords.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiForum } from '@mdi/js';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import WelcomeBanner from '@/Components/WelcomeBanner.vue';
import { useForum } from '@/Pages/Forum/Composables/useForum.js';

const props = defineProps({
    title: {
        type: String,
        default: 'Foro'
    },
    filters: {
        type: Object,
        default: () => ({ search: '', rows: 10, withTrashed: false })
    },
    routeName: {
        type: String,
        default: 'doctor.questions.'
    },
    questions: {
        type: Object,
        required: true
    },
    categories: {
        type: Object,
        required: true
    }
});

const { form } = useForum(props);
const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>