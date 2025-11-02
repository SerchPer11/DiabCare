<template>
    <div class="space-y-6">
        <!-- Indicadores principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatCard
                v-if="dashboardData.indicators.nextAppointment"
                :value="`${dashboardData.indicators.nextAppointment.date} ${dashboardData.indicators.nextAppointment.time}`"
                label="Próxima cita"
                :subtitle="`Dr. ${dashboardData.indicators.nextAppointment.doctor}`"
                :color="getAppointmentColor(dashboardData.indicators.nextAppointment)"
            />
            <StatCard
                v-else
                value="Sin citas"
                label="Próxima cita"
                subtitle="No hay citas programadas"
                color="gray"
            />
            
            <StatCard
                :value="dashboardData.indicators.weeklyGoalCompletion"
                suffix="%"
                label="Metas cumplidas (semana)"
                :color="getGoalCompletionColor(dashboardData.indicators.weeklyGoalCompletion)"
            />
            
            <StatCard
                :value="dashboardData.indicators.activePlansCount"
                label="Planes activos"
                color="green"
            />
        </div>

        <!-- Gráfica de glucosa y tareas del día -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <ChartCard
                title="Historial de glucosa (promedio semanal)"
                :data="dashboardData.glucoseHistory"
                type="line"
                x-field="week"
                y-field="average"
                color="#EF4444"
                background-color="rgba(239, 68, 68, 0.1)"
            />
            
            <ListCard
                title="Tareas de hoy"
                :items="todayTasksFormatted"
                empty-message="No hay tareas para hoy"
                @action="handleTaskAction"
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
import ChartCard from './ChartCard.vue';
import ListCard from './ListCard.vue';
import QuickActionCard from './QuickActionCard.vue';

const props = defineProps({
    dashboardData: {
        type: Object,
        required: true
    }
});

const getAppointmentColor = (appointment) => {
    if (appointment.is_today) return 'red';
    if (appointment.is_tomorrow) return 'yellow';
    return 'blue';
};

const getGoalCompletionColor = (percentage) => {
    if (percentage >= 80) return 'green';
    if (percentage >= 60) return 'yellow';
    return 'red';
};

const todayTasksFormatted = computed(() => {
    return props.dashboardData.todayTasks.map(task => ({
        title: task.title,
        subtitle: task.description,
        meta: `Tipo: ${task.type}`,
        badge: {
            text: task.completed ? 'Completado' : 'Pendiente',
            color: task.completed ? 'green' : 'yellow'
        },
        action: {
            text: task.completed ? 'Ver' : 'Marcar como completado',
            route: 'patient.plans.show',
            params: { plan: task.id },
            external: false
        }
    }));
});

const handleTaskAction = (action) => {
    // Aquí podrías manejar acciones específicas como marcar tareas como completadas
    console.log('Task action:', action);
};
</script>