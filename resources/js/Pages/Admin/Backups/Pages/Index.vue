<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <!-- Stats Cards -->
        <BackupStats :stats="stats" :routeName="routeName" />

        <!-- Filter Banner with Date Range -->
        <CardBox class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Search -->
                <BaseFormField 
                    type="input"
                    label="Buscar"
                    v-model="localFilters.search"
                    placeholder="Buscar por nombre, descripción o usuario..."
                />

                <!-- Start Date -->
                <BaseFormField 
                    type="date"
                    label="Fecha Inicio"
                    v-model="localFilters.start_date"
                />

                <!-- End Date -->
                <BaseFormField 
                    type="date"
                    label="Fecha Fin"
                    v-model="localFilters.end_date"
                />

                <!-- Actions -->
                <div class="flex gap-2">
                    <BaseButton
                        color="info"
                        :icon="mdiMagnify"
                        label="Filtrar"
                        @click="applyFilters"
                    />
                    <BaseButton
                        color="light"
                        :icon="mdiRefresh"
                        label="Limpiar"
                        @click="clearFilters"
                    />
                </div>
            </div>

            <!-- Results Info -->
            <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                <div>
                    Total de registros: {{ backups?.meta?.total || 0 }}
                </div>
                <div class="flex items-center gap-2">
                    Mostrar:
                    <select 
                        v-model="localFilters.rows" 
                        class="border rounded px-2 py-1"
                        @change="applyFilters"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    por página
                </div>
            </div>
        </CardBox>

        <!-- Backup Records -->
        <BackupRecords :backups="backups" :routeName="routeName" />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BackupRecords from '../Components/BackupRecords.vue';
import BackupStats from '../Components/BackupStats.vue';
import CardBox from '@/Components/CardBox.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import {
    mdiMagnify,
    mdiCalendar,
    mdiRefresh
} from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Respaldos'
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            rows: 10,
            start_date: null,
            end_date: null,
            order: 'created_at',
            direction: 'desc'
        })
    },
    routeName: {
        type: String,
        default: 'backups.'
    },
    backups: {
        type: Object,
        required: true
    },
    stats: {
        type: Object,
        required: true
    }
});

// Local filters state
const localFilters = reactive({
    search: props.filters.search || '',
    rows: props.filters.rows || 10,
    start_date: props.filters.start_date || null,
    end_date: props.filters.end_date || null,
    order: props.filters.order || 'created_at',
    direction: props.filters.direction || 'desc'
});

const applyFilters = () => {
    router.get(route(`${props.routeName}index`), localFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    localFilters.search = '';
    localFilters.start_date = null;
    localFilters.end_date = null;
    localFilters.rows = 10;
    applyFilters();
};

// Auto-apply filters on search with debounce
let searchTimeout;
const watchSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

onMounted(() => {
    // Watch search input
    const searchInput = document.querySelector('input[placeholder*="Buscar"]');
    if (searchInput) {
        searchInput.addEventListener('input', watchSearch);
    }
});
</script>