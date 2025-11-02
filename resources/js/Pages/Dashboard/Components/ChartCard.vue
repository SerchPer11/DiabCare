<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h4 class="font-medium text-gray-900 mb-4">{{ title }}</h4>
            <div class="h-64">
                <canvas ref="chartCanvas"></canvas>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    type: {
        type: String,
        default: 'line',
        validator: value => ['line', 'bar', 'pie', 'doughnut'].includes(value)
    },
    xField: {
        type: String,
        default: 'label'
    },
    yField: {
        type: String,
        default: 'value'
    },
    color: {
        type: String,
        default: '#3B82F6'
    },
    backgroundColor: {
        type: String,
        default: 'rgba(59, 130, 246, 0.1)'
    }
});

const chartCanvas = ref(null);
let chartInstance = null;

const createChart = async () => {
    if (!chartCanvas.value || !props.data?.length) return;

    await nextTick();

    // Destruir gráfico anterior si existe
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');
    
    const labels = props.data.map(item => item[props.xField]);
    const values = props.data.map(item => item[props.yField]);

    const chartConfig = {
        type: props.type,
        data: {
            labels,
            datasets: [{
                label: props.title,
                data: values,
                borderColor: props.color,
                backgroundColor: props.backgroundColor,
                borderWidth: 2,
                fill: props.type === 'line',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: props.type === 'line' || props.type === 'bar' ? {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            } : {}
        }
    };

    chartInstance = new Chart(ctx, chartConfig);
};

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { deep: true });
</script>