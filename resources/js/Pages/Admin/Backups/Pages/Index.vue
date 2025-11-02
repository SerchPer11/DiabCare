<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <!-- Stats Cards -->
        <BackupStats :stats="stats" :routeName="routeName" />

        <!-- Filter Banner with Date Range -->
        <CardBox class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-10 gap-4 items-end">
                <!-- Search Field -->
                <div class="md:col-span-2 lg:col-span-4">
                    <BaseFormField 
                        type="input"
                        label="Buscar"
                        v-model="localFilters.search"
                        placeholder="Buscar por nombre, descripción o usuario..."
                        @input="handleSearchInput"
                    />
                </div>

                <!-- Start Date -->
                <div class="lg:col-span-2">
                    <BaseFormField 
                        type="date"
                        label="Fecha Inicio"
                        v-model="localFilters.start_date"
                    />
                </div>

                <!-- Records per page -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Por página</label>
                    <select 
                        v-model="localFilters.rows" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        @change="applyFilters"
                    >
                        <option value="10">10 registros</option>
                        <option value="25">25 registros</option>
                        <option value="50">50 registros</option>
                        <option value="100">100 registros</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-2 lg:col-span-2 flex gap-2 justify-end">
                    <BaseButton
                        color="info"
                        :icon="mdiMagnify"
                        label="Filtrar"
                        @click="applyFilters"
                        small
                    />
                    <BaseButton
                        color="light"
                        :icon="mdiRefresh"
                        label="Limpiar"
                        @click="clearFilters"
                        small
                    />
                </div>
            </div>

            <!-- Results Summary -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ backups?.meta?.total || 0 }}</span> 
                        respaldos encontrados
                        <span v-if="backups?.meta?.from && backups?.meta?.to" class="ml-2">
                            (mostrando {{ backups.meta.from }} - {{ backups.meta.to }})
                        </span>
                    </div>
                    
                    <!-- Active Filters Indicator -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
                        <span class="text-xs text-gray-500">Filtros activos:</span>
                        <span 
                            v-if="localFilters.search" 
                            class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full"
                        >
                            Búsqueda: "{{ localFilters.search }}"
                        </span>
                        <span 
                            v-if="localFilters.start_date" 
                            class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full"
                        >
                            Desde: {{ localFilters.start_date }}
                        </span>
                    </div>
                </div>
            </div>
        </CardBox>

        <!-- Backup Records -->
        <BackupRecords :backups="backups" :routeName="routeName" />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount } from 'vue';
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
    order: props.filters.order || 'created_at',
    direction: props.filters.direction || 'desc'
});

// Check if there are active filters
const hasActiveFilters = computed(() => {
    return !!(localFilters.search || localFilters.start_date);
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
    localFilters.rows = 10;
    applyFilters();
};

// Auto-apply filters on search with debounce
let searchTimeout = null;

// Watch for changes in search field
const debouncedSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 800); // Increased timeout for better UX
};

// Manual search trigger
const handleSearchInput = () => {
    debouncedSearch();
};

// Watch search field changes reactively
watch(() => localFilters.search, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        debouncedSearch();
    }
});

// Cleanup on component unmount
onBeforeUnmount(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});
</script>