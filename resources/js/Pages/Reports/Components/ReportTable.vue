<script setup>
import CardBox from '@/Components/CardBox.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import { mdiTable } from '@mdi/js';

// 1. DEFINICIÓN DE PROPS
// Recibimos el objeto 'tableData' que nuestro
// DiabetesTypeReportService preparó en el backend.
defineProps({
    tableData: {
        type: Object,
        required: true,
        default: () => ({ headers: [], rows: [] })
    }
});
</script>

<template>
    <CardBox has-table class="overflow-hidden">
        <div class="overflow-x-auto p-1 max-h-96">
            <table class="w-full min-w-[600px] mt-2 border border-medic-100">
                <thead class="bg-medic-100 rounded-t-lg">
                    <tr>
                        <th v-for="header in tableData.headers" :key="header.key"
                            class="p-3 text-left text-sm font-semibold text-gray-500 uppercase">
                            {{ header.label }}
                        </th>
                    </tr>
                </thead>
                <tbody v-if="tableData.rows && tableData.rows.length > 0">
                    <tr v-for="(row, rowIndex) in tableData.rows" :key="rowIndex"
                        class="">
                        <td v-for="header in tableData.headers" :key="header.key" :data-label="header.label"
                            class="p-3 text-sm text-gray-700 border-t border-medic-100">
                            {{ row[header.key] }}
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td :colspan="tableData.headers.length" class="p-8 text-center text-gray-500">
                            No se encontraron registros con los filtros seleccionados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </CardBox>
</template>
