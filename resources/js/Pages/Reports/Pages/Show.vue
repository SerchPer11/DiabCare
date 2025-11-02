<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudHeadWhioutFilters :title="title" main :icon="mdiFileChart" />
        <CardBox class="mt-4">
            <div class="flex gap-2 items-center text-left m-2">
                <div>
                    <h1 class="text-medic-400 font-semibold">Reporte: {{ reportTitle }}</h1>
                    <p class="text-gray-600">Seleccione los criterios de filtrado.</p>
                </div>
                <div class="ml-auto">
                    <BaseButton :icon="mdiFilePdfBox" color="info" label="Exportar a PDF"  @click="exportToPDF" class="mr-24"/>
                </div>
            </div>
            <ReportFilters :filters="filters" @filter="handleFilter" class="mt-4"/>
        </CardBox>

        <ReportStats :stats="stats" class="mt-4" />
        <ReportChart :chartData="chartData" ref="chartRef" class="mt-4" />
        <ReportTable :tableData="tableData" class="mt-4" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import CrudHeadWhioutFilters from '@/Components/CrudHeadWhioutFilters.vue';
import { ref } from 'vue';
import { mdiFileChart, mdiFilePdfBox } from '@mdi/js';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import ReportFilters from '../Components/ReportFilters.vue';
import ReportChart from '../Components/ReportChart.vue';
import ReportStats from '../Components/ReportStats.vue';
import ReportTable from '../Components/ReportTable.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Reportes'
    },
    chartData: {
        type: Object,
        required: true
    },
    tableData: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    reportType: {
        type: String,
        required: true
    },
    reportTitle: {
        type: String,
        required: true
    },
    stats: {
        type: Array,
        default: () => []
    }
});

const chartRef = ref(null);

const currentFilters = ref(
    props.filters.reduce((acc, filter) => {
        acc[filter.name] = filter.value;
        return acc;
    }, {})
);

const handleFilter = (filters) => {
    currentFilters.value = filters;

    router.get(route('reports.show', { reportType: props.reportType }), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const exportToPDF = () => {
    if (!chartRef.value) {
        console.error("No se encontró la referencia de la gráfica.");
        return;
    }

    const chartImage = chartRef.value.getChartAsBase64();

    if (!chartImage) {
        console.error("No se pudo generar la imagen de la gráfica.");
        return;
    }
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');

    if (!csrfTokenElement) {
        console.error("¡Error Crítico! No se encontró la meta etiqueta 'csrf-token'.");
        return;
    }
    const csrfToken = csrfTokenElement.content;


    const currentFilters = props.filters.reduce((acc, filter) => {
        acc[filter.name] = filter.value;
        return acc;
    }, {});

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route('reports.export', { reportType: props.reportType });

    const inputs = {
        _token: csrfToken,
        chartImage: chartImage,
        ...currentFilters,
    };

    for (const name in inputs) {
        if (inputs[name] !== null && inputs[name] !== undefined) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = inputs[name];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};
</script>