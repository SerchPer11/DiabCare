<!-- resources/js/Pages/Patient/Survey/Pages/Index.vue -->
<template>
    <Head title="Encuestas Disponibles" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Encuestas de Seguimiento
                </h2>
                <div class="text-sm text-gray-600">
                    {{ availableSurveys.length }} encuesta{{ availableSurveys.length !== 1 ? 's' : '' }} disponible{{ availableSurveys.length !== 1 ? 's' : '' }}
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Resumen de progreso -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ completedCount }}</div>
                            <div class="text-sm text-gray-600">Completadas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ inProgressCount }}</div>
                            <div class="text-sm text-gray-600">En Progreso</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ pendingCount }}</div>
                            <div class="text-sm text-gray-600">Pendientes</div>
                        </div>
                    </div>
                </div>

                <!-- Barra de búsqueda y filtros -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div class="flex-1">
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
                                    <option value="available">Disponibles</option>
                                    <option value="completed">Completadas</option>
                                    <option value="in_progress">En progreso</option>
                                    <option value="expired">Expiradas</option>
                                </select>
                                <select
                                    v-model="sortBy"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="priority">Por prioridad</option>
                                    <option value="created_at">Más recientes</option>
                                    <option value="ends_at">Por vencimiento</option>
                                    <option value="title">Por título</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Encuestas urgentes -->
                <div v-if="urgentSurveys.length > 0" class="mb-8">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-red-800 font-medium">Encuestas próximas a vencer ({{ urgentSurveys.length }})</h3>
                        </div>
                        <p class="text-red-700 text-sm mt-1">Estas encuestas vencen en los próximos 3 días. Te recomendamos completarlas pronto.</p>
                    </div>
                    
                    <div class="space-y-4">
                        <SurveyCard
                            v-for="survey in urgentSurveys"
                            :key="`urgent-${survey.id}`"
                            :survey="survey"
                            :response-data="getResponseData(survey.id)"
                        />
                    </div>
                </div>

                <!-- Lista principal de encuestas -->
                <div v-if="filteredSurveys.length === 0 && !urgentSurveys.length" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay encuestas disponibles</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || statusFilter !== 'all' 
                            ? 'No se encontraron encuestas con los filtros aplicados.' 
                            : 'No tienes encuestas asignadas en este momento.' 
                        }}
                    </p>
                    <div v-if="searchQuery || statusFilter !== 'all'" class="mt-6">
                        <button
                            @click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <div v-else-if="filteredSurveys.length > 0">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ urgentSurveys.length > 0 ? 'Otras Encuestas' : 'Encuestas Disponibles' }}
                            <span class="text-sm text-gray-500 ml-2">({{ filteredSurveys.length }})</span>
                        </h3>
                    </div>
                    
                    <div class="space-y-6">
                        <SurveyCard
                            v-for="survey in filteredSurveys"
                            :key="survey.id"
                            :survey="survey"
                            :response-data="getResponseData(survey.id)"
                        />
                    </div>
                </div>

                <!-- Enlaces útiles -->
                <div class="mt-8 bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Enlaces Útiles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link
                            :href="route('patient.surveys.my-responses')"
                            class="flex items-center p-3 bg-white rounded border hover:shadow-sm transition-shadow"
                        >
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Mis Respuestas</div>
                                <div class="text-sm text-gray-600">Ver historial completo</div>
                            </div>
                        </Link>
                        
                        <a
                            href="#"
                            @click.prevent="showHelp = true"
                            class="flex items-center p-3 bg-white rounded border hover:shadow-sm transition-shadow"
                        >
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Ayuda</div>
                                <div class="text-sm text-gray-600">Cómo responder encuestas</div>
                            </div>
                        </a>

                        <a
                            href="#"
                            @click.prevent="showContact = true"
                            class="flex items-center p-3 bg-white rounded border hover:shadow-sm transition-shadow"
                        >
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Contactar Doctor</div>
                                <div class="text-sm text-gray-600">Dudas o consultas</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Modal de ayuda -->
                <div v-if="showHelp" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showHelp = false">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Cómo Responder Encuestas</h3>
                                <button @click="showHelp = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="text-sm text-gray-600 space-y-3">
                                <p><strong>1. Escala Likert:</strong> Cada pregunta usa una escala del 1 al 5:</p>
                                <ul class="ml-4 space-y-1">
                                    <li>• 1 = Totalmente en desacuerdo</li>
                                    <li>• 2 = En desacuerdo</li>
                                    <li>• 3 = Neutral</li>
                                    <li>• 4 = De acuerdo</li>
                                    <li>• 5 = Totalmente de acuerdo</li>
                                </ul>
                                <p><strong>2. Progreso:</strong> Puedes guardar y continuar más tarde.</p>
                                <p><strong>3. Comentarios:</strong> Opcional, pero ayuda a tu doctor a entender mejor tu situación.</p>
                                <p><strong>4. Tiempo:</strong> No hay límite de tiempo, responde con calma.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SurveyCard from '../Components/SurveyCard.vue'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    surveys: Array,
    myResponses: Array,
    responseProgress: Object
})

const { isAvailable } = useSurveyResponse()

// Estados reactivos
const searchQuery = ref('')
const statusFilter = ref('all')
const sortBy = ref('priority')
const showHelp = ref(false)
const showContact = ref(false)

// Computadas para estadísticas
const availableSurveys = computed(() => {
    return props.surveys?.filter(survey => isAvailable(survey)) || []
})

const completedCount = computed(() => {
    return props.surveys?.filter(survey => {
        const response = getResponseData(survey.id)
        return response?.progress === 100
    }).length || 0
})

const inProgressCount = computed(() => {
    return props.surveys?.filter(survey => {
        const response = getResponseData(survey.id)
        return response?.progress > 0 && response?.progress < 100
    }).length || 0
})

const pendingCount = computed(() => {
    return props.surveys?.filter(survey => {
        const response = getResponseData(survey.id)
        return isAvailable(survey) && (!response || response.progress === 0)
    }).length || 0
})

// Encuestas urgentes (próximas a vencer en 3 días)
const urgentSurveys = computed(() => {
    if (!props.surveys) return []
    
    const threeDaysFromNow = new Date()
    threeDaysFromNow.setDate(threeDaysFromNow.getDate() + 3)
    
    return props.surveys.filter(survey => {
        if (!survey.ends_at || !isAvailable(survey)) return false
        
        const endDate = new Date(survey.ends_at)
        const response = getResponseData(survey.id)
        
        // Solo mostrar si no está completada y vence pronto
        return endDate <= threeDaysFromNow && (!response || response.progress < 100)
    })
})

// Filtrado y ordenamiento
const filteredSurveys = computed(() => {
    let filtered = [...(props.surveys || [])]
    
    // Excluir encuestas urgentes de la lista principal
    const urgentIds = urgentSurveys.value.map(s => s.id)
    filtered = filtered.filter(survey => !urgentIds.includes(survey.id))
    
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
            const response = getResponseData(survey.id)
            
            switch (statusFilter.value) {
                case 'available':
                    return isAvailable(survey) && (!response || response.progress < 100)
                case 'completed':
                    return response?.progress === 100
                case 'in_progress':
                    return response?.progress > 0 && response?.progress < 100
                case 'expired':
                    return !isAvailable(survey)
                default:
                    return true
            }
        })
    }
    
    // Ordenamiento
    filtered.sort((a, b) => {
        const aResponse = getResponseData(a.id)
        const bResponse = getResponseData(b.id)
        
        switch (sortBy.value) {
            case 'priority':
                // Prioridad: En progreso > Disponibles > Completadas > Expiradas
                const aPriority = getPriority(a, aResponse)
                const bPriority = getPriority(b, bResponse)
                return bPriority - aPriority
                
            case 'ends_at':
                const aEnd = a.ends_at ? new Date(a.ends_at) : new Date('2099-12-31')
                const bEnd = b.ends_at ? new Date(b.ends_at) : new Date('2099-12-31')
                return aEnd - bEnd
                
            case 'created_at':
                return new Date(b.created_at) - new Date(a.created_at)
                
            case 'title':
                return a.title.localeCompare(b.title)
                
            default:
                return 0
        }
    })
    
    return filtered
})

// Métodos auxiliares
const getResponseData = (surveyId) => {
    return props.responseProgress?.[surveyId] || null
}

const getPriority = (survey, response) => {
    if (response?.progress > 0 && response?.progress < 100) return 4 // En progreso
    if (isAvailable(survey) && (!response || response.progress === 0)) return 3 // Disponible
    if (response?.progress === 100) return 2 // Completada
    return 1 // Expirada o no disponible
}

const clearFilters = () => {
    searchQuery.value = ''
    statusFilter.value = 'all'
    sortBy.value = 'priority'
}

// Watchers
watch([searchQuery, statusFilter], () => {
    // Los filtros se aplican automáticamente a través de las computadas
})
</script>