<script setup>
import CardBox from '@/Components/CardBox.vue';

// 1. DEFINICIÓN DE PROPS
// Recibimos el array 'stats' que nuestro
// DiabetesTypeReportService preparó en el backend.
defineProps({
    stats: {
        type: Array,
        required: true,
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <CardBox v-for="(stat, index) in stats" :key="index" class="p-6">
            <div class="text-center">
                <div class_alias="text-4xl font-bold text-medic-600">
                    {{ stat.value }}
                </div>
                <div class="mt-2 text-sm font-medium text-gray-500 uppercase tracking-wider">
                    {{ stat.label }}
                </div>
            </div>
        </CardBox>
    </div>
</template>