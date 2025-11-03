<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner 
            :title="title" 
            :icon="mdiCalendarMonth" 
            :routeName="routeName" 
            main 
            :hideCreate="true"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
            v-model:search="filters.search"
            v-model:rows="filters.rows"
            :total="appointments?.meta?.total || 0" 
        />

        <div class="py-6">
            <!-- Lista de Citas -->
            <div v-if="appointments?.data && appointments.data.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <AppointmentCard
                    v-for="appointment in appointments.data"
                    :key="appointment.id"
                    :appointment="appointment"
                    @view="viewAppointment"
                />
            </div>

            <!-- Estado vacío -->
            <CardBox v-else class="text-center py-16">
                <div class="mx-auto w-24 h-24 mb-4 text-gray-400">
                    <Icon :path="mdiCalendarBlank" class="w-full h-full" />
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    {{ hasActiveFilters ? 'No se encontraron citas' : 'No tienes citas programadas' }}
                </h3>
                <p class="text-gray-500 mb-4">
                    {{ hasActiveFilters 
                        ? 'Intenta ajustar los filtros de búsqueda.'
                        : 'Cuando tengas citas médicas programadas aparecerán aquí.' 
                    }}
                </p>
                <BaseButton 
                    v-if="hasActiveFilters"
                    color="info"
                    outline
                    @click="clearFilters"
                >
                    <Icon :path="mdiReload" class="w-4 h-4 mr-2" />
                    Limpiar filtros
                </BaseButton>
            </CardBox>

            <!-- Paginación -->
            <div v-if="hasAppointments" class="mt-6">
                <Pagination 
                    v-if="appointments?.meta" 
                    :links="appointments.meta.links" 
                    :total="appointments.meta.total" 
                    :to="appointments.meta.to"
                    :from="appointments.meta.from" 
                    typeRecords="citas"
                />
            </div>
        </div>

        <!-- Modal de detalle de cita -->
        <AppointmentDetailModal
            v-model="showDetailModal"
            :appointment="selectedAppointment"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Icon from '@/Components/Icon.vue';
import AppointmentCard from '../Components/AppointmentCard.vue';
import AppointmentDetailModal from '../Components/AppointmentDetailModal.vue';
import { useFilters } from '@/Hooks/useFilters';
import { computed, ref } from 'vue';
import { 
    mdiReload, 
    mdiCalendarMonth, 
    mdiCalendarBlank 
} from '@mdi/js';

const props = defineProps({
    appointments: Object,
    filters: Object,
});

// Título de la página
const title = 'Mis Citas';
const routeName = 'patient.appointments';

// Estado para el modal de detalle
const showDetailModal = ref(false);
const selectedAppointment = ref(null);

// Configurar filtros para la búsqueda 
const initialFilters = {
    search: props.filters?.search || '',
    rows: props.filters?.rows || 12,
};

const { filters, applyFilters, clearFilters: clearAllFilters } = useFilters(initialFilters, 'patient.appointments.');

// Computadas para manejar el estado
const hasAppointments = computed(() => {
    return props.appointments?.data && props.appointments.data.length > 0;
});

const hasActiveFilters = computed(() => {
    return props.filters?.search && props.filters.search.length > 0;
});

// Función para ver detalles de una cita
const viewAppointment = (appointment) => {
    selectedAppointment.value = appointment;
    showDetailModal.value = true;
};

// Función para limpiar filtros
const clearFilters = () => {
    clearAllFilters();
};
</script>