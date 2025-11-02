<template>
    <div class="space-y-6">
        <!-- Indicadores principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatCard
                :value="dashboardData.indicators.todayAppointments"
                label="Citas de hoy"
                color="blue"
            />
            <StatCard
                :value="dashboardData.indicators.activePatientsCount"
                label="Pacientes con planes activos"
                color="green"
            />
            <StatCard
                :value="dashboardData.indicators.plansExpiringCount"
                label="Planes por vencer (7 días)"
                color="yellow"
            />
        </div>

        <!-- Lista de próximas citas -->
        <div class="grid grid-cols-1 gap-6">
            <ListCard
                title="Próximas 5 citas"
                :items="upcomingAppointmentsFormatted"
                empty-message="No hay citas programadas próximamente"
            />
        </div>

        <!-- Accesos directos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <QuickActionCard
                v-for="action in dashboardData.quickActions"
                :key="action.route"
                :title="action.title"
                :description="action.description"
                :route="action.route"
                :route-params="action.params || {}"
                :button-text="action.title"
                :color="action.color"
                :icon="action.icon"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import StatCard from './StatCard.vue';
import ListCard from './ListCard.vue';
import QuickActionCard from './QuickActionCard.vue';

const props = defineProps({
    dashboardData: {
        type: Object,
        required: true
    }
});

const upcomingAppointmentsFormatted = computed(() => {
    return props.dashboardData.upcomingAppointments.map(appointment => ({
        title: appointment.patient_name,
        subtitle: `${appointment.date} a las ${appointment.time}`,
        meta: `Modalidad: ${appointment.modality} - ${appointment.reason}`,
        badge: {
            text: appointment.is_today ? 'Hoy' : appointment.is_tomorrow ? 'Mañana' : 'Próxima',
            color: appointment.is_today ? 'red' : appointment.is_tomorrow ? 'yellow' : 'blue'
        }
    }));
});
</script>