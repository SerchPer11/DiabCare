<script setup>
import { ref, computed } from 'vue';
import CardBox from '@/Components/CardBox.vue';

import { Bar, Pie } from 'vue-chartjs';

import { Chart as ChartJS, registerables } from 'chart.js';
ChartJS.register(...registerables);


const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    }
});

const chartRef = ref(null);

const chartComponent = computed(() => {
    if (props.chartData.type === 'pie') {
        return Pie;
    }
    if (props.chartData.type === 'line') {
    }
    return Bar;
});


const getChartAsBase64 = () => {
    if (chartRef.value && chartRef.value.chart) {
        return chartRef.value.chart.toBase64Image();
    }
    console.error("La instancia del gráfico no está lista para exportar.");
    return null;
};

defineExpose({
    getChartAsBase64
});

</script>

<template>
    <CardBox>
        <div class="relative h-96">
            
            <component 
                v-if="props.chartData.data" 
                :is="chartComponent" 
                ref="chartRef" 
                :data="props.chartData.data"
                :options="props.chartData.options" 
            />
            
            <div v-else class="text-center p-8 text-gray-500">
                No hay datos suficientes para mostrar la gráfica.
            </div>
        </div>
    </CardBox>
</template>