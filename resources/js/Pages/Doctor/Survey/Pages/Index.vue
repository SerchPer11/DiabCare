<!-- resources/js/Pages/Doctor/Survey/Pages/Index.vue -->
<template>
    <Head title="Mis Encuestas" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ showingResults ? 'Resultados de Encuestas' : 'Mis Encuestas' }}
                </h2>
                <Link
                    v-if="!showingResults"
                    :href="route('doctor.surveys.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                    + Nueva Encuesta
                </Link>
                <Link
                    v-else
                    :href="route('doctor.surveys.index')"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                    ← Volver a Encuestas
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas generales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ showingResults ? totalResponses : totalSurveys }}</div>
                            <div class="text-sm text-gray-600">{{ showingResults ? 'Total Respuestas' : 'Total Encuestas' }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ showingResults ? completedResponses : activeSurveys }}</div>
                            <div class="text-sm text-gray-600">{{ showingResults ? 'Completadas' : 'Activas' }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ showingResults ? pendingResponses : totalResponses }}</div>
                            <div class="text-sm text-gray-600">{{ showingResults ? 'Pendientes' : 'Total Respuestas' }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-amber-600">{{ averageResponseRate }}%</div>
                            <div class="text-sm text-gray-600">Tasa Respuesta</div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y búsqueda -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1 max-w-lg">
                                <div class="relative">
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Buscar encuestas..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <select
                                    v-model="statusFilter"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="all">Todos los estados</option>
                                    <option value="active">Activas</option>
                                    <option value="inactive">Inactivas</option>
                                    <option value="scheduled">Programadas</option>
                                    <option value="expired">Expiradas</option>
                                </select>
                                <select
                                    v-model="sortBy"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="created_at">Fecha de creación</option>
                                    <option value="title">Título</option>
                                    <option value="responses_count">Más respuestas</option>
                                    <option value="updated_at">Actualización</option>
                                </select>
                                <select
                                    v-model="sortOrder"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="desc">Descendente</option>
                                    <option value="asc">Ascendente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de encuestas -->
                <div v-if="filteredSurveys.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay encuestas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || statusFilter !== 'all' ? 'No se encontraron encuestas con los filtros aplicados.' : 'Comienza creando tu primera encuesta.' }}
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('doctor.surveys.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            + Nueva Encuesta
                        </Link>
                    </div>
                </div>

                <div v-else class="space-y-6">
                    <SurveyCard
                        v-for="survey in paginatedSurveys"
                        :key="survey.id"
                        :survey="survey"
                        @toggle-status="handleToggleStatus"
                    />
                </div>

                <!-- Paginación -->
                <div v-if="totalPages > 1" class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <button
                            @click="currentPage--"
                            :disabled="currentPage === 1"
                            class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                        >
                            Anterior
                        </button>
                        
                        <template v-for="page in visiblePages" :key="page">
                            <button
                                v-if="page !== '...'"
                                @click="currentPage = page"
                                :class="[
                                    'px-3 py-2 border rounded-md',
                                    currentPage === page
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'border-gray-300 hover:bg-gray-50'
                                ]"
                            >
                                {{ page }}
                            </button>
                            <span v-else class="px-3 py-2 text-gray-500">...</span>
                        </template>

                        <button
                            @click="currentPage++"
                            :disabled="currentPage === totalPages"
                            class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                        >
                            Siguiente
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Botón flotante para agregar encuesta (siempre visible) -->
        <div class="fixed bottom-6 right-6 z-50">
            <Link
                :href="route('doctor.surveys.create')"
                class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300"
                title="Nueva Encuesta"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </Link>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SurveyCard from '../Components/SurveyCard.vue'
import { useSurveys } from '../Composables/useSurveys'

const props = defineProps({
    surveys: Array,
    stats: Object,
    showingResults: {
        type: Boolean,
        default: false
    }
})

const { getSurveyStatus } = useSurveys()

// Estados reactivos
const searchQuery = ref('')
const statusFilter = ref('all')
const sortBy = ref('created_at')
const sortOrder = ref('desc')
const currentPage = ref(1)
const itemsPerPage = 12

// Estadísticas computadas
const totalSurveys = computed(() => props.stats?.total || props.surveys?.length || 0)
const activeSurveys = computed(() => props.stats?.active || props.surveys?.filter(s => s.is_active).length || 0)
const totalResponses = computed(() => props.stats?.total_responses || 0)
const averageResponseRate = computed(() => Math.round(props.stats?.average_response_rate || 0))

// Estadísticas para resultados
const completedResponses = computed(() => props.stats?.completed || 0)
const pendingResponses = computed(() => props.stats?.pending || 0)

// Filtrado y ordenamiento
const filteredSurveys = computed(() => {
    let filtered = [...(props.surveys || [])]

    // Filtro de búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(survey => 
            survey.title.toLowerCase().includes(query) ||
            (survey.description || '').toLowerCase().includes(query)
        )
    }

    // Filtro de estado
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(survey => {
            const status = getSurveyStatus(survey).status
            return status === statusFilter.value
        })
    }

    // Ordenamiento
    filtered.sort((a, b) => {
        let aValue = a[sortBy.value]
        let bValue = b[sortBy.value]

        // Manejo especial para fechas
        if (sortBy.value.includes('_at')) {
            aValue = new Date(aValue || 0)
            bValue = new Date(bValue || 0)
        }

        // Manejo especial para strings
        if (typeof aValue === 'string') {
            return sortOrder.value === 'asc' 
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue)
        }

        // Manejo para números y fechas
        if (sortOrder.value === 'asc') {
            return aValue - bValue
        } else {
            return bValue - aValue
        }
    })

    return filtered
})

// Paginación
const totalPages = computed(() => Math.ceil(filteredSurveys.value.length / itemsPerPage))

const paginatedSurveys = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return filteredSurveys.value.slice(start, end)
})

const visiblePages = computed(() => {
    const pages = []
    const total = totalPages.value
    const current = currentPage.value

    if (total <= 7) {
        for (let i = 1; i <= total; i++) {
            pages.push(i)
        }
    } else {
        pages.push(1)
        
        if (current > 4) {
            pages.push('...')
        }
        
        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
            pages.push(i)
        }
        
        if (current < total - 3) {
            pages.push('...')
        }
        
        if (total > 1) {
            pages.push(total)
        }
    }
    
    return pages
})

// Watchers para resetear la página cuando cambian los filtros
watch([searchQuery, statusFilter, sortBy, sortOrder], () => {
    currentPage.value = 1
})

// Métodos de acción
const handleToggleStatus = (surveyId, newStatus) => {
    router.patch(route('doctor.surveys.toggle-status', surveyId), {
        is_active: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Mostrar notificación de éxito si es necesario
        }
    })
}
</script>