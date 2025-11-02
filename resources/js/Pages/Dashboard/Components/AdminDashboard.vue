<template>
    <div class="space-y-6">
        <!-- Indicadores principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatCard
                :value="dashboardData.indicators.newUsersThisWeek"
                label="Nuevos usuarios esta semana"
                color="blue"
                icon="users"
            />
            <StatCard
                :value="dashboardData.indicators.totalUsers"
                label="Total de usuarios en el sistema"
                color="green"
                icon="users"
            />
            <StatCard
                :value="dashboardData.indicators.lastBackup.formatted"
                label="Último respaldo"
                :subtitle="dashboardData.indicators.lastBackup.human"
                color="purple"
            />
        </div>

        <!-- Gráfica y lista -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <ChartCard
                title="Altas de usuarios por semana"
                :data="dashboardData.weeklyRegistrations"
                type="bar"
                x-field="week"
                y-field="count"
                color="#3B82F6"
            />
            
            <ListCard
                title="Usuarios más recientes"
                :items="recentUsersFormatted"
                empty-message="No hay usuarios recientes"
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

const recentUsersFormatted = computed(() => {
    return props.dashboardData.recentUsers.map(user => ({
        title: user.name,
        subtitle: user.email,
        meta: user.created_at_human,
        badge: {
            text: user.role,
            color: 'blue'
        }
    }));
});
</script>