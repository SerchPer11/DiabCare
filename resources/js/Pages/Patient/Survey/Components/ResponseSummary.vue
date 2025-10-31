<!-- resources/js/Pages/Patient/Survey/Components/ResponseSummary.vue -->
<template>
    <div class="space-y-6">
        <!-- Encabezado de la respuesta -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ response.survey.title }}</h2>
                        <p v-if="response.survey.description" class="text-gray-600 mt-1">
                            {{ response.survey.description }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            :class="completionStatusClass"
                        >
                            {{ completionStatusText }}
                        </span>
                    </div>
                </div>

                <!-- Estadísticas de la respuesta -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ answeredCount }}</div>
                        <div class="text-sm text-blue-800">Respondidas</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ completionPercentage }}%</div>
                        <div class="text-sm text-green-800">Completado</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ averageScore }}</div>
                        <div class="text-sm text-purple-800">Puntuación media</div>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-lg">
                        <div class="text-2xl font-bold text-amber-600">{{ formatDate(response.created_at) }}</div>
                        <div class="text-sm text-amber-800">Fecha respuesta</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen visual de las respuestas -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen Visual</h3>
                
                <!-- Gráfico circular de distribución -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-800 mb-3">Distribución de Respuestas</h4>
                        <div class="space-y-2">
                            <div
                                v-for="item in distributionData"
                                :key="item.value"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="w-4 h-4 rounded mr-3"
                                        :class="item.color.replace('text-', 'bg-').replace('border-', 'bg-')"
                                    ></div>
                                    <span class="text-sm">{{ item.value }} - {{ item.label }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium mr-2">{{ item.count }}</span>
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full transition-all duration-300"
                                            :class="item.color.replace('text-', 'bg-').replace('border-', 'bg-')"
                                            :style="{ width: `${item.percentage}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-medium text-gray-800 mb-3">Análisis de Bienestar</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Nivel general:</span>
                                <span 
                                    class="px-3 py-1 rounded-full text-sm font-medium"
                                    :class="wellnessLevelClass"
                                >
                                    {{ wellnessLevel }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Puntuación total:</span>
                                <span class="font-medium">{{ totalScore }}/{{ maxPossibleScore }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Respuestas positivas:</span>
                                <span class="text-green-600 font-medium">{{ positiveResponses }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Áreas de atención:</span>
                                <span class="text-amber-600 font-medium">{{ areasOfConcern }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de respuestas por pregunta -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalle de Respuestas</h3>
                
                <div class="space-y-6">
                    <div
                        v-for="(question, index) in response.survey.questions"
                        :key="question.id"
                        class="border-b border-gray-200 pb-6 last:border-b-0"
                    >
                        <div class="mb-3">
                            <h4 class="text-base font-medium text-gray-900 mb-1">
                                Pregunta {{ index + 1 }}: {{ question.question }}
                            </h4>
                        </div>

                        <div class="ml-4">
                            <!-- Respuesta del usuario -->
                            <div v-if="getAnswerForQuestion(question.id)" class="mb-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Tu respuesta:</span>
                                    <div class="flex items-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-sm font-medium mr-2"
                                            :class="getLikertColor(getAnswerForQuestion(question.id).answer)"
                                        >
                                            {{ getAnswerForQuestion(question.id).answer }} - {{ getLikertLabel(getAnswerForQuestion(question.id).answer) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Comentario si existe -->
                                <div v-if="getAnswerForQuestion(question.id).comment" class="mt-2 p-3 bg-gray-50 rounded">
                                    <p class="text-sm text-gray-700">
                                        <strong>Comentario:</strong> {{ getAnswerForQuestion(question.id).comment }}
                                    </p>
                                </div>
                            </div>

                            <!-- Si no se respondió -->
                            <div v-else class="mb-3">
                                <span class="text-sm text-gray-500 italic">No respondida</span>
                            </div>

                            <!-- Escala visual de referencia -->
                            <div class="flex space-x-1 mt-2">
                                <div
                                    v-for="scale in likertScale"
                                    :key="scale.value"
                                    class="flex-1 h-2 rounded"
                                    :class="[
                                        scale.color.replace('text-', 'bg-').replace('border-', 'bg-'),
                                        getAnswerForQuestion(question.id)?.answer === scale.value ? 'ring-2 ring-gray-400' : 'opacity-30'
                                    ]"
                                ></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Totalmente en desacuerdo</span>
                                <span>Totalmente de acuerdo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recomendaciones (si las hay) -->
        <div v-if="recommendations.length > 0" class="bg-blue-50 rounded-lg border border-blue-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Recomendaciones Personalizadas</h3>
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

        <!-- Acciones -->
        <div class="flex flex-wrap gap-3">
            <button
                @click="handleEdit"
                v-if="canEdit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                Editar respuestas
            </button>
            <button
                @click="handlePrint"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Imprimir resumen
            </button>
            <button
                @click="handleShare"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Compartir con mi doctor
            </button>
            <button
                @click="$emit('back')"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Volver a encuestas
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    response: {
        type: Object,
        required: true
    },
    canEdit: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['back'])

const { likertScale, formatDate } = useSurveyResponse()

// Computadas para estadísticas
const answeredCount = computed(() => props.response.answers.length)

const totalQuestions = computed(() => props.response.survey.questions.length)

const completionPercentage = computed(() => {
    if (totalQuestions.value === 0) return 0
    return Math.round((answeredCount.value / totalQuestions.value) * 100)
})

const completionStatusClass = computed(() => {
    const percentage = completionPercentage.value
    if (percentage === 100) return 'bg-green-100 text-green-800'
    if (percentage >= 75) return 'bg-blue-100 text-blue-800'
    if (percentage >= 50) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
})

const completionStatusText = computed(() => {
    const percentage = completionPercentage.value
    if (percentage === 100) return 'Completada'
    if (percentage >= 75) return 'Casi completa'
    if (percentage >= 50) return 'Parcialmente completada'
    return 'Incompleta'
})

const totalScore = computed(() => {
    return props.response.answers.reduce((sum, answer) => sum + answer.answer, 0)
})

const maxPossibleScore = computed(() => {
    return answeredCount.value * 5
})

const averageScore = computed(() => {
    if (answeredCount.value === 0) return '0.0'
    return (totalScore.value / answeredCount.value).toFixed(1)
})

const wellnessLevel = computed(() => {
    const avg = parseFloat(averageScore.value)
    if (avg >= 4.5) return 'Excelente'
    if (avg >= 3.5) return 'Bueno'
    if (avg >= 2.5) return 'Regular'
    if (avg >= 1.5) return 'Deficiente'
    return 'Muy deficiente'
})

const wellnessLevelClass = computed(() => {
    const avg = parseFloat(averageScore.value)
    if (avg >= 4.5) return 'bg-green-100 text-green-800'
    if (avg >= 3.5) return 'bg-blue-100 text-blue-800'
    if (avg >= 2.5) return 'bg-yellow-100 text-yellow-800'
    if (avg >= 1.5) return 'bg-orange-100 text-orange-800'
    return 'bg-red-100 text-red-800'
})

const positiveResponses = computed(() => {
    if (answeredCount.value === 0) return 0
    const positiveCount = props.response.answers.filter(answer => answer.answer >= 4).length
    return Math.round((positiveCount / answeredCount.value) * 100)
})

const areasOfConcern = computed(() => {
    const lowScores = props.response.answers.filter(answer => answer.answer <= 2).length
    return lowScores
})

const distributionData = computed(() => {
    const distribution = {}
    
    // Contar respuestas por valor
    props.response.answers.forEach(answer => {
        distribution[answer.answer] = (distribution[answer.answer] || 0) + 1
    })
    
    // Convertir a array con datos de porcentaje
    return likertScale.map(scale => {
        const count = distribution[scale.value] || 0
        const percentage = answeredCount.value > 0 ? (count / answeredCount.value) * 100 : 0
        
        return {
            value: scale.value,
            label: scale.label,
            count,
            percentage,
            color: scale.color
        }
    })
})

const recommendations = computed(() => {
    const recs = []
    const avg = parseFloat(averageScore.value)
    
    if (avg < 3) {
        recs.push({
            id: 1,
            text: 'Considera hablar con tu médico sobre las áreas donde puntuaste bajo para explorar opciones de mejora.'
        })
    }
    
    if (areasOfConcern.value > 0) {
        recs.push({
            id: 2,
            text: 'Identifica las áreas específicas de preocupación y desarrolla un plan de acción con tu equipo médico.'
        })
    }
    
    if (positiveResponses.value > 70) {
        recs.push({
            id: 3,
            text: '¡Excelente! Mantén las prácticas que te están funcionando bien en tu cuidado de la diabetes.'
        })
    }
    
    return recs
})

// Métodos auxiliares
const getAnswerForQuestion = (questionId) => {
    return props.response.answers.find(answer => answer.question_id === questionId)
}

const getLikertColor = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.color : 'bg-gray-100 text-gray-800'
}

const getLikertLabel = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.label : 'N/A'
}

// Métodos de acción
const handleEdit = () => {
    router.visit(route('patient.surveys.answer', props.response.survey.id))
}

const handlePrint = () => {
    window.print()
}

const handleShare = () => {
    // Implementar funcionalidad de compartir
    console.log('Compartiendo con el doctor...')
}
</script>