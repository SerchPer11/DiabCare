<template>
    <CardBox
        class="hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
        @click="$emit('view', plan.id)"
    >
        <!-- Header con tipo y estado -->
        <div class="flex items-center justify-between mb-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                  :class="getPlanTypeClasses(plan.plan_type?.name)">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path v-if="plan.plan_type?.name === 'alimentacion'" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    <path v-else d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                </svg>
                {{ getPlanTypeName(plan.plan_type?.name) }}
            </span>
            
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClasses(plan.status)">
                <span class="w-2 h-2 rounded-full mr-1"
                      :class="getStatusDotClasses(plan.status)"></span>
                {{ getStatusLabel(plan.status) }}
            </span>
        </div>

        <!-- Título del plan -->
        <h3 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ plan.title }}
        </h3>

        <!-- Descripción -->
        <p v-if="plan.description" class="text-gray-600 mb-4 line-clamp-3">
            {{ plan.description }}
        </p>

        <!-- Información de fechas -->
        <div class="flex items-center text-sm text-gray-500 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ formatDateRange(plan.start_date, plan.end_date) }}</span>
        </div>

        <!-- Progreso del plan -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progreso</span>
                <span class="text-sm font-medium text-gray-900">{{ planProgress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="progressBarColor"
                     :style="{ width: planProgress + '%' }"></div>
            </div>
        </div>

        <!-- Elementos del plan -->
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>{{ elementsCount }} elemento(s)</span>
            </div>
            
            <span class="text-blue-600 hover:text-blue-800 font-medium">
                Ver detalles →
            </span>
        </div>

        <!-- Footer con indicador de tiempo -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ getTimeStatus() }}</span>
                <span>{{ daysRemaining }}</span>
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import { computed } from 'vue';

const props = defineProps({
    plan: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['view']);

// Métodos de utilidad
const getPlanTypeName = (typeName) => {
    if (!typeName) return 'Tipo no definido';
    return typeName === 'alimentacion' ? 'Alimentación' : 'Actividad Física';
};

const getPlanTypeClasses = (typeName) => {
    if (!typeName) return 'bg-gray-100 text-gray-800';
    return typeName === 'alimentacion' 
        ? 'bg-green-100 text-green-800'
        : 'bg-blue-100 text-blue-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'activo': 'Activo',
        'completado': 'Completado',
        'cancelado': 'Cancelado'
    };
    return labels[status] || status;
};

const getStatusClasses = (status) => {
    const classes = {
        'activo': 'bg-green-100 text-green-700',
        'completado': 'bg-blue-100 text-blue-700',
        'cancelado': 'bg-red-100 text-red-700'
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const getStatusDotClasses = (status) => {
    const classes = {
        'activo': 'bg-green-400',
        'completado': 'bg-blue-400',
        'cancelado': 'bg-red-400'
    };
    return classes[status] || 'bg-gray-400';
};

const formatDateRange = (startDate, endDate) => {
    const start = new Date(startDate).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short'
    });
    const end = new Date(endDate).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short'
    });
    return `${start} - ${end}`;
};

// Computadas
const elementsCount = computed(() => {
    return props.plan.elements?.length || 0;
});

const planProgress = computed(() => {
    const now = new Date();
    const start = new Date(props.plan.start_date);
    const end = new Date(props.plan.end_date);
    
    if (now < start) return 0;
    if (now > end || props.plan.status === 'completado') return 100;
    
    const total = end - start;
    const elapsed = now - start;
    return Math.round((elapsed / total) * 100);
});

const progressBarColor = computed(() => {
    const progress = planProgress.value;
    if (props.plan.status === 'completado') return 'bg-blue-500';
    if (props.plan.status === 'cancelado') return 'bg-red-500';
    if (progress >= 75) return 'bg-green-500';
    if (progress >= 50) return 'bg-yellow-500';
    if (progress >= 25) return 'bg-orange-500';
    return 'bg-blue-500';
});

const daysRemaining = computed(() => {
    const now = new Date();
    const end = new Date(props.plan.end_date);
    
    if (props.plan.status === 'completado') return 'Completado';
    if (props.plan.status === 'cancelado') return 'Cancelado';
    
    if (now > end) return 'Finalizado';
    
    const diffTime = end - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Último día';
    if (diffDays === 1) return '1 día restante';
    return `${diffDays} días restantes`;
});

const getTimeStatus = () => {
    const now = new Date();
    const start = new Date(props.plan.start_date);
    const end = new Date(props.plan.end_date);
    
    if (now < start) {
        const diffTime = start - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays === 1 ? 'Comienza mañana' : `Comienza en ${diffDays} días`;
    }
    
    if (now > end) return 'Plan finalizado';
    
    return 'En progreso';
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>