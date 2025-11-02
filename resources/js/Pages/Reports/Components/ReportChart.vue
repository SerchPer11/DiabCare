<script setup>
import { ref } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';

// 1. REGISTRO DE COMPONENTES DE CHART.JS
// Es obligatorio registrar los "pedazos" de Chart.js que vamos a usar.
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

// 2. DEFINICIÓN DE PROPS
// Recibimos el objeto 'chartData' que nuestro
// DiabetesTypeReportService preparó en el backend.
const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    }
});

// 3. REFERENCIA AL GRÁFICO
// Esta 'ref' nos dará acceso a la instancia interna de la gráfica
// para poder llamar a sus métodos (como 'toBase64Image').
const chartRef = ref(null);

// 4. MÉTODO DE EXPORTACIÓN (LA MAGIA DEL PDF)
// Esta función será llamada por el componente padre (Show.vue).
const getChartAsBase64 = () => {
    // Verificamos que la referencia (chartRef.value) y la
    // instancia interna (.chart) existan.
    if (chartRef.value && chartRef.value.chart) {
        // Esta es la función nativa de Chart.js para convertir
        // el canvas en una imagen Base64 (un string larguísimo).
        return chartRef.value.chart.toBase64Image();
    }

    console.error("La instancia del gráfico no está lista para exportar.");
    return null;
};

// 5. EXPOSICIÓN DEL MÉTODO
// 'defineExpose' hace que la función 'getChartAsBase64'
// sea pública y accesible desde el componente padre.
defineExpose({
    getChartAsBase64
});

</script>

<template>
    <CardBox>
        <div class="relative h-96">
            <Bar v-if="props.chartData.data" ref="chartRef" :data="props.chartData.data"
                :options="props.chartData.options" />
            <div v-else class="text-center p-8 text-gray-500">
                No hay datos suficientes para mostrar la gráfica.
            </div>
        </div>
    </CardBox>
</template>