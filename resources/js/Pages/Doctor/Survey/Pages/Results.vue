<!-- resources/js/Pages/Doctor/Survey/Pages/Results.vue -->
<template>
    <Head :title="`Resultados: ${surveyData?.title || 'Encuesta'}`" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Resultados: {{ surveyData?.title || 'Encuesta' }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Análisis detallado de {{ totalResponses }} respuesta{{ totalResponses !== 1 ? 's' : '' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="refreshResults"
                        :disabled="loading"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
                    >
                        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ loading ? 'Actualizando...' : 'Actualizar' }}
                    </button>
                    <Link
                        :href="route('doctor.surveys.show', surveyData?.id)"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                        v-if="surveyData?.id"
                    >
                        Ver encuesta
                    </Link>
                    <Link
                        :href="route('doctor.surveys.index')"
                        class="text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        ← Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros de período -->
                <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <h3 class="text-lg font-medium text-gray-900">Filtros de Análisis</h3>
                            <div class="flex flex-col md:flex-row gap-3">
                                <select
                                    v-model="selectedPeriod"
                                    @change="applyFilters"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="all">Todas las fechas</option>
                                    <option value="last_week">Última semana</option>
                                    <option value="last_month">Último mes</option>
                                    <option value="last_quarter">Último trimestre</option>
                                    <option value="custom">Período personalizado</option>
                                </select>
                                
                                <div v-if="selectedPeriod === 'custom'" class="flex gap-2">
                                    <input
                                        v-model="customStartDate"
                                        type="date"
                                        @change="applyFilters"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <input
                                        v-model="customEndDate"
                                        type="date"
                                        @change="applyFilters"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <select
                                    v-model="selectedSegment"
                                    @change="applyFilters"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="all">Todos los pacientes</option>
                                    <option value="completed">Solo respuestas completas</option>
                                    <option value="incomplete">Solo respuestas incompletas</option>
                                    <option value="high_score">Puntuación alta (4-5)</option>
                                    <option value="low_score">Puntuación baja (1-2)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sin respuestas -->
                <div v-if="totalResponses === 0" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Sin respuestas aún</h3>
                    <p class="mt-1 text-sm text-gray-500">Esta encuesta no tiene respuestas para analizar.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('doctor.surveys.show', surveyData?.id)"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                            v-if="surveyData?.id"
                        >
                            Ver configuración de encuesta
                        </Link>
                    </div>
                </div>

                <!-- Componente de resultados -->
                <div v-else>
                    <SurveyResults
                        :survey="survey"
                        :responses="filteredResponses"
                        :question-results="filteredQuestionResults"
                    />
                </div>

                <!-- Comparación temporal (si hay datos suficientes) -->
                <div v-if="showTemporalComparison && temporalData && temporalData.length > 1" class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolución Temporal</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ temporalTrend.responses }}</div>
                                <div class="text-sm text-blue-800">
                                    Respuestas 
                                    <span :class="temporalTrend.responsesChange >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ temporalTrend.responsesChange >= 0 ? '↗' : '↘' }} 
                                        {{ Math.abs(temporalTrend.responsesChange) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ temporalTrend.avgScore }}</div>
                                <div class="text-sm text-purple-800">
                                    Puntuación promedio
                                    <span :class="temporalTrend.scoreChange >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ temporalTrend.scoreChange >= 0 ? '↗' : '↘' }} 
                                        {{ Math.abs(temporalTrend.scoreChange).toFixed(1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ temporalTrend.completionRate }}%</div>
                                <div class="text-sm text-green-800">
                                    Tasa de completitud
                                    <span :class="temporalTrend.completionChange >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ temporalTrend.completionChange >= 0 ? '↗' : '↘' }} 
                                        {{ Math.abs(temporalTrend.completionChange) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico simple de evolución -->
                        <div class="space-y-3">
                            <div
                                v-for="(period, index) in temporalData"
                                :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded"
                            >
                                <span class="text-sm font-medium text-gray-700">{{ period.label }}</span>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span>{{ period.responses }} respuestas</span>
                                    <span>Promedio: {{ period.avgScore.toFixed(1) }}/5</span>
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full"
                                            :style="{ width: `${(period.avgScore / 5) * 100}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recomendaciones basadas en resultados -->
                <div v-if="recommendations.length > 0" class="mt-8 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">Recomendaciones Clínicas</h3>
                        <div class="space-y-3">
                            <div
                                v-for="recommendation in recommendations"
                                :key="recommendation.id"
                                class="flex items-start"
                            >
                                <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <p class="text-sm text-blue-800">{{ recommendation.text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SurveyResults from '../Components/SurveyResults.vue'

const props = defineProps({
    survey: Object,
    responses: Array,
    questionResults: Array,
    temporalData: Array
})

// Fix para extraer los datos si vienen anidados en 'data'
const surveyData = computed(() => {
    return props.survey?.data ? props.survey.data : props.survey
})

// Computed para extraer questions correctamente
const questionsArray = computed(() => {
    const questions = surveyData.value?.questions
    if (Array.isArray(questions)) {
        return questions
    } else if (questions?.data && Array.isArray(questions.data)) {
        return questions.data
    }
    return []
})

// Estados reactivos
const loading = ref(false)
const selectedPeriod = ref('all')
const selectedSegment = ref('all')
const customStartDate = ref('')
const customEndDate = ref('')
const showTemporalComparison = ref(true)

// Computadas
const totalResponses = computed(() => props.responses?.length || 0)

const filteredResponses = computed(() => {
    let filtered = [...(props.responses || [])]

    // Filtro por período
    if (selectedPeriod.value !== 'all') {
        const now = new Date()
        let startDate = new Date()

        switch (selectedPeriod.value) {
            case 'last_week':
                startDate.setDate(now.getDate() - 7)
                break
            case 'last_month':
                startDate.setMonth(now.getMonth() - 1)
                break
            case 'last_quarter':
                startDate.setMonth(now.getMonth() - 3)
                break
            case 'custom':
                if (customStartDate.value && customEndDate.value) {
                    const start = new Date(customStartDate.value)
                    const end = new Date(customEndDate.value)
                    filtered = filtered.filter(response => {
                        const responseDate = new Date(response.created_at)
                        return responseDate >= start && responseDate <= end
                    })
                }
                return filtered
        }

        filtered = filtered.filter(response => {
            const responseDate = new Date(response.created_at)
            return responseDate >= startDate
        })
    }

    // Filtro por segmento
    if (selectedSegment.value !== 'all') {
        switch (selectedSegment.value) {
            case 'completed':
                filtered = filtered.filter(response => 
                    response.answers.length === questionsArray.value.length
                )
                break
            case 'incomplete':
                filtered = filtered.filter(response => 
                    response.answers.length < questionsArray.value.length
                )
                break
            case 'high_score':
                filtered = filtered.filter(response => {
                    const avg = response.answers.reduce((sum, a) => sum + a.answer, 0) / response.answers.length
                    return avg >= 4
                })
                break
            case 'low_score':
                filtered = filtered.filter(response => {
                    const avg = response.answers.reduce((sum, a) => sum + a.answer, 0) / response.answers.length
                    return avg <= 2
                })
                break
        }
    }

    return filtered
})

const filteredQuestionResults = computed(() => {
    // Recalcular estadísticas de preguntas basadas en respuestas filtradas
    return questionsArray.value.map(question => {
        const relevantAnswers = []
        
        filteredResponses.value.forEach(response => {
            const answer = response.answers.find(a => a.question_id === question.id)
            if (answer) {
                relevantAnswers.push(answer)
            }
        })

        const distribution = {}
        for (let i = 1; i <= 5; i++) {
            distribution[i] = relevantAnswers.filter(a => a.answer === i).length
        }

        const average = relevantAnswers.length > 0
            ? relevantAnswers.reduce((sum, a) => sum + a.answer, 0) / relevantAnswers.length
            : 0

        return {
            id: question.id,
            question: question.question,
            totalAnswers: relevantAnswers.length,
            distribution,
            average,
            comments: relevantAnswers
                .filter(a => a.comment)
                .map(a => ({
                    id: a.id,
                    comment: a.comment,
                    patient_name: a.response?.patient?.name || 'Paciente',
                    created_at: a.created_at
                }))
        }
    })
})

const temporalTrend = computed(() => {
    if (!props.temporalData || props.temporalData.length < 2) {
        return {
            responses: 0,
            responsesChange: 0,
            avgScore: 0,
            scoreChange: 0,
            completionRate: 0,
            completionChange: 0
        }
    }

    const latest = props.temporalData[props.temporalData.length - 1]
    const previous = props.temporalData[props.temporalData.length - 2]

    return {
        responses: latest.responses,
        responsesChange: previous.responses > 0 
            ? Math.round(((latest.responses - previous.responses) / previous.responses) * 100)
            : 0,
        avgScore: latest.avgScore.toFixed(1),
        scoreChange: latest.avgScore - previous.avgScore,
        completionRate: Math.round(latest.completionRate),
        completionChange: Math.round(latest.completionRate - previous.completionRate)
    }
})

const recommendations = computed(() => {
    const recs = []
    
    if (filteredResponses.value.length === 0) return recs

    const avgScore = filteredResponses.value.reduce((sum, response) => {
        const responseAvg = response.answers.reduce((s, a) => s + a.answer, 0) / response.answers.length
        return sum + responseAvg
    }, 0) / filteredResponses.value.length

    const completionRate = (filteredResponses.value.filter(r => 
        r.answers.length === questionsArray.value.length
    ).length / filteredResponses.value.length) * 100

    if (avgScore < 2.5) {
        recs.push({
            id: 1,
            text: 'La puntuación promedio es baja. Considera revisar individualmente a los pacientes con puntuaciones más bajas para identificar áreas de mejora específicas.'
        })
    }

    if (completionRate < 70) {
        recs.push({
            id: 2,
            text: 'La tasa de completitud es baja. Considera simplificar la encuesta o proporcionar más apoyo a los pacientes para completarla.'
        })
    }

    if (avgScore > 4) {
        recs.push({
            id: 3,
            text: '¡Excelente! Las puntuaciones son altas. Mantén las estrategias actuales y considera documentar las mejores prácticas.'
        })
    }

    const lowScoreQuestions = filteredQuestionResults.value.filter(q => q.average < 2.5 && q.totalAnswers > 0)
    if (lowScoreQuestions.length > 0) {
        recs.push({
            id: 4,
            text: `Hay ${lowScoreQuestions.length} pregunta(s) con puntuaciones consistentemente bajas. Revisa estas áreas específicas para intervenciones dirigidas.`
        })
    }

    return recs
})

// Métodos
const applyFilters = () => {
    // Los filtros se aplican automáticamente a través de las computadas
    // Aquí podrías agregar lógica adicional si necesitas hacer llamadas al servidor
}

const refreshResults = () => {
    loading.value = true
    router.reload({
        only: ['responses', 'questionResults', 'temporalData'],
        onFinish: () => {
            loading.value = false
        }
    })
}

onMounted(() => {
    // Configurar fechas por defecto para período personalizado
    const today = new Date()
    const oneMonthAgo = new Date()
    oneMonthAgo.setMonth(today.getMonth() - 1)
    
    customEndDate.value = today.toISOString().split('T')[0]
    customStartDate.value = oneMonthAgo.toISOString().split('T')[0]
})
</script>