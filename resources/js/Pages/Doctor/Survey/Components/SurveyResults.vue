<!-- resources/js/Pages/Doctor/Survey/Components/SurveyResults.vue -->
<template>
    <div class="space-y-6">
        <!-- Resumen de estadísticas -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Respuestas</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ totalResponses }}</div>
                        <div class="text-sm text-blue-800">Total Respuestas</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ completedResponses }}</div>
                        <div class="text-sm text-green-800">Completadas</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ averageScore.toFixed(1) }}</div>
                        <div class="text-sm text-purple-800">Puntuación Promedio</div>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-amber-600">{{ completionRate }}%</div>
                        <div class="text-sm text-amber-800">Tasa Completitud</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados por pregunta -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Análisis por Pregunta</h3>
                
                <div class="space-y-8">
                    <div
                        v-for="(question, index) in questionResults"
                        :key="question.id"
                        class="border-b border-gray-200 pb-6 last:border-b-0"
                    >
                        <!-- Pregunta -->
                        <div class="mb-4">
                            <h4 class="text-base font-medium text-gray-900 mb-2">
                                Pregunta {{ index + 1 }}: {{ question.question }}
                            </h4>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span>{{ question.totalAnswers }} respuestas</span>
                                <span>Promedio: {{ question.average.toFixed(2) }}</span>
                                <span 
                                    class="px-2 py-1 rounded-full text-xs"
                                    :class="getScoreColor(question.average)"
                                >
                                    {{ getScoreLabel(question.average) }}
                                </span>
                            </div>
                        </div>

                        <!-- Gráfico de barras horizontales -->
                        <div class="space-y-2">
                            <div
                                v-for="option in likertScale"
                                :key="option.value"
                                class="flex items-center space-x-3"
                            >
                                <div class="w-20 text-sm text-gray-600">
                                    {{ option.value }} - {{ option.label }}
                                </div>
                                <div class="flex-1 bg-gray-200 rounded-full h-6 relative">
                                    <div
                                        class="h-6 rounded-full transition-all duration-300"
                                        :class="option.color.replace('text-', 'bg-').replace('border-', 'bg-')"
                                        :style="{ width: `${getPercentage(question, option.value)}%` }"
                                    ></div>
                                    <span
                                        v-if="getCount(question, option.value) > 0"
                                        class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white"
                                    >
                                        {{ getCount(question, option.value) }}
                                    </span>
                                </div>
                                <div class="w-12 text-sm text-gray-600 text-right">
                                    {{ getPercentage(question, option.value).toFixed(0) }}%
                                </div>
                            </div>
                        </div>

                        <!-- Comentarios de esta pregunta -->
                        <div v-if="question.comments && question.comments.length > 0" class="mt-4">
                            <button
                                @click="toggleComments(question.id)"
                                class="flex items-center text-sm text-blue-600 hover:text-blue-800"
                            >
                                <svg
                                    class="w-4 h-4 mr-1 transition-transform"
                                    :class="{ 'transform rotate-180': showComments[question.id] }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                Ver comentarios ({{ question.comments.length }})
                            </button>
                            
                            <div v-if="showComments[question.id]" class="mt-3 space-y-2">
                                <div
                                    v-for="comment in question.comments"
                                    :key="comment.id"
                                    class="bg-gray-50 rounded p-3 text-sm"
                                >
                                    <p class="text-gray-700">{{ comment.comment }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Por: {{ comment.patient_name }} - {{ formatDate(comment.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exportar resultados -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Exportar Resultados</h3>
                <div class="flex flex-wrap gap-3">
                    <button
                        @click="exportToPDF"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        📄 Exportar PDF
                    </button>
                    <button
                        @click="exportToExcel"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        📊 Exportar Excel
                    </button>
                    <button
                        @click="exportToCSV"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        📋 Exportar CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Respuestas individuales -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Respuestas Individuales</h3>
                    <div class="flex items-center space-x-2">
                        <select
                            v-model="sortBy"
                            class="border border-gray-300 rounded px-3 py-1 text-sm"
                        >
                            <option value="date">Ordenar por fecha</option>
                            <option value="patient">Ordenar por paciente</option>
                            <option value="score">Ordenar por puntuación</option>
                        </select>
                        <select
                            v-model="filterBy"
                            class="border border-gray-300 rounded px-3 py-1 text-sm"
                        >
                            <option value="all">Todas las respuestas</option>
                            <option value="completed">Solo completadas</option>
                            <option value="incomplete">Solo incompletas</option>
                        </select>
                    </div>
                </div>

                <div v-if="filteredResponses.length === 0" class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay respuestas</h3>
                    <p class="mt-1 text-sm text-gray-500">Aún no se han registrado respuestas para esta encuesta.</p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="response in paginatedResponses"
                        :key="response.id"
                        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ response.patient.name }}</h4>
                                <p class="text-sm text-gray-600">{{ formatDate(response.created_at) }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">
                                    {{ calculateResponseScore(response) }}/5
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ response.answers.length }}/{{ questionResults.length }} respondidas
                                </div>
                            </div>
                        </div>
                        
                        <button
                            @click="toggleResponseDetail(response.id)"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            {{ showResponseDetail[response.id] ? 'Ocultar' : 'Ver' }} detalles
                        </button>

                        <div v-if="showResponseDetail[response.id]" class="mt-3 space-y-2">
                            <div
                                v-for="answer in response.answers"
                                :key="answer.question_id"
                                class="flex justify-between items-center text-sm"
                            >
                                <span class="text-gray-600">P{{ answer.question_order || answer.question_id }}:</span>
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getLikertColor(answer.answer)"
                                >
                                    {{ answer.answer }} - {{ getLikertLabel(answer.answer) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación simple -->
                    <div v-if="filteredResponses.length > responsesPerPage" class="flex justify-center mt-6">
                        <div class="flex space-x-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                            >
                                Anterior
                            </button>
                            <span class="px-3 py-1 text-sm text-gray-600">
                                Página {{ currentPage }} de {{ totalPages }}
                            </span>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                            >
                                Siguiente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useSurveys } from '../Composables/useSurveys'

const props = defineProps({
    survey: {
        type: Object,
        required: true
    },
    responses: {
        type: Array,
        default: () => []
    },
    questionResults: {
        type: Array,
        default: () => []
    }
})

const { likertScale, formatDate } = useSurveys()

// Estados reactivos
const showComments = ref({})
const showResponseDetail = ref({})
const sortBy = ref('date')
const filterBy = ref('all')
const currentPage = ref(1)
const responsesPerPage = 10

// Computadas para estadísticas
const totalResponses = computed(() => props.responses.length)

const completedResponses = computed(() => {
    return props.responses.filter(response => {
        return response.answers.length === props.questionResults.length
    }).length
})

const averageScore = computed(() => {
    if (props.questionResults.length === 0) return 0
    
    const sum = props.questionResults.reduce((acc, question) => acc + question.average, 0)
    return sum / props.questionResults.length
})

const completionRate = computed(() => {
    if (totalResponses.value === 0) return 0
    return Math.round((completedResponses.value / totalResponses.value) * 100)
})

// Filtrado y ordenamiento de respuestas
const filteredResponses = computed(() => {
    let filtered = props.responses

    // Aplicar filtro
    if (filterBy.value === 'completed') {
        filtered = filtered.filter(response => 
            response.answers.length === props.questionResults.length
        )
    } else if (filterBy.value === 'incomplete') {
        filtered = filtered.filter(response => 
            response.answers.length < props.questionResults.length
        )
    }

    // Aplicar ordenamiento
    if (sortBy.value === 'date') {
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    } else if (sortBy.value === 'patient') {
        filtered.sort((a, b) => a.patient.name.localeCompare(b.patient.name))
    } else if (sortBy.value === 'score') {
        filtered.sort((a, b) => calculateResponseScore(b) - calculateResponseScore(a))
    }

    return filtered
})

const totalPages = computed(() => Math.ceil(filteredResponses.value.length / responsesPerPage))

const paginatedResponses = computed(() => {
    const start = (currentPage.value - 1) * responsesPerPage
    const end = start + responsesPerPage
    return filteredResponses.value.slice(start, end)
})

// Métodos auxiliares
const getCount = (question, value) => {
    return question.distribution[value] || 0
}

const getPercentage = (question, value) => {
    if (question.totalAnswers === 0) return 0
    return (getCount(question, value) / question.totalAnswers) * 100
}

const getScoreColor = (score) => {
    if (score >= 4.5) return 'bg-green-100 text-green-800'
    if (score >= 3.5) return 'bg-blue-100 text-blue-800'
    if (score >= 2.5) return 'bg-yellow-100 text-yellow-800'
    if (score >= 1.5) return 'bg-orange-100 text-orange-800'
    return 'bg-red-100 text-red-800'
}

const getScoreLabel = (score) => {
    if (score >= 4.5) return 'Excelente'
    if (score >= 3.5) return 'Bueno'
    if (score >= 2.5) return 'Regular'
    if (score >= 1.5) return 'Deficiente'
    return 'Muy Deficiente'
}

const getLikertColor = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.color : 'bg-gray-100 text-gray-800'
}

const getLikertLabel = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.label : 'N/A'
}

const calculateResponseScore = (response) => {
    if (response.answers.length === 0) return 0
    const sum = response.answers.reduce((acc, answer) => acc + answer.answer, 0)
    return (sum / response.answers.length).toFixed(1)
}

const toggleComments = (questionId) => {
    showComments.value[questionId] = !showComments.value[questionId]
}

const toggleResponseDetail = (responseId) => {
    showResponseDetail.value[responseId] = !showResponseDetail.value[responseId]
}

// Métodos de exportación
const exportToPDF = () => {
    // Implementar exportación a PDF
    console.log('Exportando a PDF...')
}

const exportToExcel = () => {
    // Implementar exportación a Excel
    console.log('Exportando a Excel...')
}

const exportToCSV = () => {
    // Implementar exportación a CSV
    console.log('Exportando a CSV...')
}
</script>