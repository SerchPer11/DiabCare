<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiClipboardList" :routeName="routeName" main 
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
            v-model:search="filters.search"
            v-model:rows="filters.rows"
            :total="plans?.meta?.total || 0" />

        <div class="py-6">
            <!-- Lista de Planes -->
            <div v-if="plans?.data && plans.data.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <PlanCard
                    v-for="plan in plans.data"
                    :key="plan.id"
                    :plan="plan"
                    @view="viewPlan"
                />
            </div>

            <!-- Estado vacío -->
            <CardBox v-else class="text-center py-16">
                <div class="mx-auto w-24 h-24 mb-4 text-gray-400">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes planes asignados</h3>
                <p class="text-gray-500">{{ emptyStateMessage }}</p>
            </CardBox>

            <!-- Paginación -->
            <div v-if="hasPlans" class="mt-6">
                <Pagination 
                    v-if="plans?.meta" 
                    :links="plans.meta.links" 
                    :total="plans.meta.total" 
                    :to="plans.meta.to"
                    :from="plans.meta.from" 
                    typeRecords="planes"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Pagination from '@/Components/Pagination.vue';
import PlanCard from './Components/PlanCard.vue';
import { usePlanData } from './Composables/usePlanData.js';
import { useFilters } from '@/Hooks/useFilters';
import { mdiReload, mdiClipboardList } from '@mdi/js';

const props = defineProps({
    plans: Object,
    filters: Object,
});

// Título de la página
const title = 'Mis Planes';
const routeName = 'patient.plans';

// Configurar filtros para la búsqueda 
const initialFilters = {
    search: props.filters?.search || '',
    rows: props.filters?.rows || 12,
    status: props.filters?.status || '',
    plan_type_id: props.filters?.plan_type_id || '',
};

const { filters, applyFilters, clearFilters: clearAllFilters } = useFilters(initialFilters, 'patient.plans.');

// Usar el composable para manejar la lógica de planes
const {
    hasPlans,
    hasActiveFilters,
    emptyStateMessage,
    viewPlan
} = usePlanData(props.plans, props.filters);

// Función para limpiar filtros (necesaria para el estado vacío)
const clearFilters = () => {
    clearAllFilters();
};
</script>