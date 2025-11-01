<!-- resources/js/Pages/Patient/Survey/Pages/MyResponses.vue -->
<template>
    <Head title="Mis Respuestas" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mis Respuestas de Encuestas
                </h2>
                <Link
                    :href="route('patient.surveys.index')"
                    class="text-blue-600 hover:text-blue-800 transition-colors"
                >
                    ← Volver a encuestas
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas generales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ totalResponses }}</div>
                            <div class="text-sm text-gray-600">Total Respuestas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ completedResponses }}</div>
                            <div class="text-sm text-gray-600">Completadas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ averageScore }}</div>
                            <div class="text-sm text-gray-600">Puntuación Promedio</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-amber-600">{{ responseThisMonth }}</div>
                            <div class="text-sm text-gray-600">Este Mes</div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y búsqueda -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Buscar por título de encuesta..."
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
                                    <option value="completed">Completadas</option>
                                    <option value="incomplete">Incompletas</option>
                                </select>
                                <select
                                    v-model="periodFilter"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="all">Todas las fechas</option>
                                    <option value="this_week">Esta semana</option>
                                    <option value="this_month">Este mes</option>
                                    <option value="last_month">Mes pasado</option>
                                    <option value="last_3_months">Últimos 3 meses</option>
                                </select>
                                <select
                                    v-model="sortBy"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="created_at_desc">Más recientes</option>
                                    <option value="created_at_asc">Más antiguos</option>
                                    <option value="score_desc">Puntuación alta</option>
                                    <option value="score_asc">Puntuación baja</option>
                                    <option value="survey_title">Por encuesta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de progreso temporal -->
                <div v-if="temporalData && temporalData.length > 1" class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mi Progreso en el Tiempo</h3>
                        
                        <div class="space-y-3">
                            <div
                                v-for="(period, index) in (temporalData || []).slice(-6)" 
                                :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded"
                            >
                                <span class="text-sm font-medium text-gray-700">{{ period.period }}</span>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="text-blue-600">{{ period.responses }} respuestas</span>
                                    <span class="text-purple-600">Promedio: {{ period.avgScore.toFixed(1) }}/5</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full"
                                            :style="{ width: `${(period.avgScore / 5) * 100}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de respuestas -->
                <div v-if="filteredResponses.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay respuestas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || statusFilter !== 'all' || periodFilter !== 'all'
                            ? 'No se encontraron respuestas con los filtros aplicados.'
                            : 'No has completado ninguna encuesta aún.'
                        }}
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('patient.surveys.index')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Ver encuestas disponibles
                        </Link>
                    </div>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="response in paginatedResponses"
                        :key="response.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <!-- Encabezado de la respuesta -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        {{ response.survey.title }}
                                    </h3>
                                    <p v-if="response.survey.description" class="text-sm text-gray-600 mb-2">
                                        {{ response.survey.description }}
                                    </p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>{{ formatDate(response.created_at) }}</span>
                                        <span>•</span>
                                        <span>{{ response.answers.length }}/{{ getQuestionsArray(response.survey).length }} preguntas</span>
                                        <span>•</span>
                                        <span>Puntuación: {{ calculateResponseScore(response) }}/5</span>
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getStatusClass(response)"
                                    >
                                        {{ getStatusText(response) }}
                                    </span>
                                    <div class="relative">
                                        <button
                                            @click="toggleResponseMenu(response.id)"
                                            class="p-1 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <div
                                            v-if="openMenus[response.id]"
                                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
                                        >
                                            <div class="py-1">
                                                <button
                                                    @click="viewResponseDetail(response)"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                >
                                                    Ver detalles
                                                </button>
                                                <button
                                                    v-if="!isCompleted(response) && isSurveyStillActive(response.survey)"
                                                    @click="continueResponse(response)"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                >
                                                    Continuar respuesta
                                                </button>
                                                <button
                                                    @click="shareWithDoctor(response)"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                >
                                                    Compartir con doctor
                                                </button>
                                                <hr class="my-1">
                                                <button
                                                    @click="downloadResponse(response)"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                >
                                                    Descargar PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progreso visual -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progreso de respuesta</span>
                                    <span class="text-sm text-gray-600">{{ getCompletionPercentage(response) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full transition-all duration-300"
                                        :class="isCompleted(response) ? 'bg-green-500' : 'bg-blue-500'"
                                        :style="{ width: `${getCompletionPercentage(response)}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Resumen de puntuación -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded">
                                    <div class="text-lg font-bold text-blue-600">{{ calculateResponseScore(response) }}</div>
                                    <div class="text-xs text-blue-800">Puntuación media</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded">
                                    <div class="text-lg font-bold text-green-600">{{ response.answers.length }}</div>
                                    <div class="text-xs text-green-800">Preguntas respondidas</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded">
                                    <div class="text-lg font-bold text-purple-600">{{ getPositiveResponses(response) }}%</div>
                                    <div class="text-xs text-purple-800">Respuestas positivas</div>
                                </div>
                                <div class="text-center p-3 bg-amber-50 rounded">
                                    <div class="text-lg font-bold text-amber-600">{{ getCommentsCount(response) }}</div>
                                    <div class="text-xs text-amber-800">Comentarios</div>
                                </div>
                            </div>

                            <!-- Vista previa de últimas respuestas -->
                            <div v-if="response.answers.length > 0" class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Últimas respuestas:</h4>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="answer in response.answers.slice(-5)"
                                        :key="answer.id"
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="getLikertColor(answer.likert_value)"
                                    >
                                        {{ answer.likert_value }}
                                    </span>
                                    <span v-if="response.answers.length > 5" class="text-xs text-gray-500 self-center">
                                        +{{ response.answers.length - 5 }} más
                                    </span>
                                </div>
                            </div>

                            <!-- Acciones rápidas -->
                            <div class="flex justify-end mt-4 space-x-3">
                                <button
                                    v-if="!isCompleted(response) && isSurveyStillActive(response.survey)"
                                    @click="continueResponse(response)"
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                >
                                    Continuar
                                </button>
                                <button
                                    @click="viewResponseDetail(response)"
                                    class="px-3 py-1 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors"
                                >
                                    Ver detalles
                                </button>
                            </div>
                        </div>
                    </div>
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

        <!-- Modal para mostrar detalles de respuesta -->
        <ResponseModal 
            :show="showModal" 
            :response="selectedResponse"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useSurveyResponse } from '../Composables/useSurveyResponse'
import ResponseModal from '../Components/ResponseModal.vue'
import TestModal from '../Components/TestModal.vue'

const props = defineProps({
    responses: [Array, Object], // Accept both Array and paginated Object
    temporalData: {
        type: Array,
        default: () => []
    }
})

const { formatDate, likertScale } = useSurveyResponse()

// Extract responses data from paginated object or use directly if array
const responsesData = computed(() => {
    if (!props.responses) return []
    
    // If it's a paginated object with data property
    if (props.responses.data && Array.isArray(props.responses.data)) {
        return props.responses.data
    }
    
    // If it's already an array
    if (Array.isArray(props.responses)) {
        return props.responses
    }
    
    return []
})

// Estados reactivos
const searchQuery = ref('')
const statusFilter = ref('all')
const periodFilter = ref('all')
const sortBy = ref('created_at_desc')
const currentPage = ref(1)
const responsesPerPage = 10
const openMenus = ref({})

// Estados para el modal
const showModal = ref(false)
const selectedResponse = ref(null)

// Computadas para estadísticas
const totalResponses = computed(() => responsesData.value.length || 0)

const completedResponses = computed(() => {
    return responsesData.value.filter(response => isCompleted(response)).length || 0
})

const averageScore = computed(() => {
    if (responsesData.value.length === 0) return '0.0'
    
    const scores = responsesData.value.map(response => parseFloat(calculateResponseScore(response)))
    const sum = scores.reduce((acc, score) => acc + score, 0)
    return (sum / scores.length).toFixed(1)
})

const responseThisMonth = computed(() => {
    if (responsesData.value.length === 0) return 0
    
    const now = new Date()
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
    
    return responsesData.value.filter(response => {
        const responseDate = new Date(response.created_at)
        return responseDate >= startOfMonth
    }).length
})

// Filtrado y ordenamiento
const filteredResponses = computed(() => {
    let filtered = [...responsesData.value]
    
    // Filtro de búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(response => 
            response.survey.title.toLowerCase().includes(query) ||
            (response.survey.description || '').toLowerCase().includes(query)
        )
    }
    
    // Filtro de estado
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(response => {
            if (statusFilter.value === 'completed') {
                return isCompleted(response)
            } else if (statusFilter.value === 'incomplete') {
                return !isCompleted(response)
            }
            return true
        })
    }
    
    // Filtro de período
    if (periodFilter.value !== 'all') {
        const now = new Date()
        let startDate = new Date()
        
        switch (periodFilter.value) {
            case 'this_week':
                const dayOfWeek = now.getDay()
                startDate.setDate(now.getDate() - dayOfWeek)
                break
            case 'this_month':
                startDate = new Date(now.getFullYear(), now.getMonth(), 1)
                break
            case 'last_month':
                startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1)
                const endDate = new Date(now.getFullYear(), now.getMonth(), 0)
                filtered = filtered.filter(response => {
                    const responseDate = new Date(response.created_at)
                    return responseDate >= startDate && responseDate <= endDate
                })
                break
            case 'last_3_months':
                startDate.setMonth(now.getMonth() - 3)
                break
        }
        
        if (periodFilter.value !== 'last_month') {
            filtered = filtered.filter(response => {
                const responseDate = new Date(response.created_at)
                return responseDate >= startDate
            })
        }
    }
    
    // Ordenamiento
    filtered.sort((a, b) => {
        switch (sortBy.value) {
            case 'created_at_desc':
                return new Date(b.created_at) - new Date(a.created_at)
            case 'created_at_asc':
                return new Date(a.created_at) - new Date(b.created_at)
            case 'score_desc':
                return parseFloat(calculateResponseScore(b)) - parseFloat(calculateResponseScore(a))
            case 'score_asc':
                return parseFloat(calculateResponseScore(a)) - parseFloat(calculateResponseScore(b))
            case 'survey_title':
                return a.survey.title.localeCompare(b.survey.title)
            default:
                return 0
        }
    })
    
    return filtered
})

// Paginación
const totalPages = computed(() => Math.ceil(filteredResponses.value.length / responsesPerPage))

const paginatedResponses = computed(() => {
    const start = (currentPage.value - 1) * responsesPerPage
    const end = start + responsesPerPage
    return filteredResponses.value.slice(start, end)
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

// Helper function to safely get questions array
const getQuestionsArray = (survey) => {
    if (!survey) return []
    
    const questions = survey.questions
    if (Array.isArray(questions)) {
        return questions
    } else if (questions?.data && Array.isArray(questions.data)) {
        return questions.data
    } else if (survey.questions_count) {
        // If we only have count, create placeholder array
        return new Array(survey.questions_count).fill({})
    }
    return []
}

// Métodos auxiliares
const isCompleted = (response) => {
    // Una respuesta está completa si:
    // 1. Está marcada como completa en el servidor (is_complete = true)
    // 2. O si tiene respuestas a todas las preguntas obligatorias
    if (response.is_complete) {
        return true
    }
    
    const questionsArray = getQuestionsArray(response.survey)
    const requiredQuestions = questionsArray.filter(q => q.is_required !== false) // Por defecto las preguntas son requeridas
    
    // Si no hay preguntas o no podemos determinar cuáles son obligatorias,
    // usamos el campo is_complete del servidor
    if (requiredQuestions.length === 0) {
        return response.is_complete || false
    }
    
    // Verificar que se han respondido todas las preguntas obligatorias
    const answeredQuestionIds = response.answers.map(a => a.survey_question_id)
    const requiredQuestionIds = requiredQuestions.map(q => q.id)
    
    return requiredQuestionIds.every(id => answeredQuestionIds.includes(id))
}

const isSurveyStillActive = (survey) => {
    if (!survey.is_active) return false
    if (survey.ends_at && new Date(survey.ends_at) < new Date()) return false
    return true
}

const getCompletionPercentage = (response) => {
    // Si está marcada como completa, mostrar 100%
    if (response.is_complete) {
        return 100
    }
    
    const questionsArray = getQuestionsArray(response.survey)
    if (questionsArray.length === 0) return 0
    
    // Para el porcentaje, usar todas las preguntas (obligatorias y opcionales)
    // pero dar prioridad al estado is_complete del servidor
    return Math.round((response.answers.length / questionsArray.length) * 100)
}

const calculateResponseScore = (response) => {
    if (response.answers.length === 0) return '0.0'
    const sum = response.answers.reduce((acc, answer) => acc + (answer.likert_value || 0), 0)
    return (sum / response.answers.length).toFixed(1)
}

const getPositiveResponses = (response) => {
    if (response.answers.length === 0) return 0
    const positiveCount = response.answers.filter(answer => (answer.likert_value || 0) >= 4).length
    return Math.round((positiveCount / response.answers.length) * 100)
}

const getCommentsCount = (response) => {
    return response.answers.filter(answer => answer.comment && answer.comment.trim()).length
}

const getStatusClass = (response) => {
    if (isCompleted(response)) {
        return 'bg-green-100 text-green-800'
    } else {
        return 'bg-yellow-100 text-yellow-800'
    }
}

const getStatusText = (response) => {
    if (isCompleted(response)) {
        return 'Completada'
    } else {
        return 'Incompleta'
    }
}

const getLikertColor = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.color : 'bg-gray-100 text-gray-800'
}

// Métodos de acción
const toggleResponseMenu = (responseId) => {
    openMenus.value[responseId] = !openMenus.value[responseId]
    
    // Cerrar otros menús
    Object.keys(openMenus.value).forEach(id => {
        if (id !== responseId.toString()) {
            openMenus.value[id] = false
        }
    })
}

const viewResponseDetail = (response) => {
    console.log('Abriendo modal con respuesta:', response)
    selectedResponse.value = response
    showModal.value = true
    // Cerrar el menú si está abierto
    openMenus.value[response.id] = false
}

const closeModal = () => {
    showModal.value = false
    selectedResponse.value = null
}

const continueResponse = (response) => {
    router.visit(route('patient.surveys.answer', response.survey.id))
}

const shareWithDoctor = (response) => {
    // Implementar funcionalidad de compartir
    console.log('Compartiendo respuesta con el doctor...', response.id)
    alert('Funcionalidad de compartir en desarrollo')
}

const downloadResponse = (response) => {
    // Implementar descarga de PDF
    console.log('Descargando respuesta como PDF...', response.id)
    alert('Funcionalidad de descarga en desarrollo')
}

// Watchers
watch([searchQuery, statusFilter, periodFilter, sortBy], () => {
    currentPage.value = 1
})
</script>