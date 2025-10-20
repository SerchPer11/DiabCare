<template>
	<AuthenticatedLayout>
		<CrudHead :title="title" />

		<IndexBanner :title="title" :icon="mdiDumbbell" :routeName="routeName" main @apply-filters="applyFilters"
			@clear-filters="clearFilters" v-model:search="filters.search" v-model:rows="filters.rows"
			:total="exercises?.meta?.total || 0" />

		<ExerciseRecords :exercises="exercises" :routeName="routeName" />
	</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ExerciseRecords from '../Components/ExerciseRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { useFilters } from '@/Hooks/useFilters';
import { mdiDumbbell } from '@mdi/js';

const props = defineProps({
	title: {
		type: String,
		default: 'Ejercicios'
	},
	filters: {
		type: Object,
		default: () => ({ search: '', rows: 10, withTrashed: false })
	},
	routeName: {
		type: String,
		default: 'doctor.catalogs.exercises.'
	},
	exercises: {
		type: Object,
		required: true
	}
});

const { filters, clearFilters, applyFilters, isLoading } = useFilters(props.filters, props.routeName);
</script>
