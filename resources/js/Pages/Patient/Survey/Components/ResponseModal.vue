<!-- resources/js/Pages/Patient/Survey/Components/ResponseModal.vue -->
<template>
    <!-- Modal overlay directo -->
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
        style="z-index: 9999;"
        @click.self="closeModal"
    >
            <!-- Contenedor del modal -->
            <div
                class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
                @click.stop
            >
            <!-- Header del modal -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ response?.survey?.title || 'Detalle de Respuesta' }}
                    </h2>
                    <p v-if="response?.created_at" class="text-sm text-gray-600 mt-1">
                        Respondida el {{ formatDate(response.created_at) }}
                    </p>
                </div>
                <button
                    @click="closeModal"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-2"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Contenido principal -->
            <div class="overflow-y-auto" style="max-height: calc(90vh - 200px);">
                <div class="p-6">
                    <!-- Información básica -->
                    <div v-if="response" class="space-y-6">
                        <!-- Estadísticas generales -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-lg font-bold text-blue-600">{{ averageScore }}</div>
                                <div class="text-xs text-blue-800">Promedio</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600">{{ positiveResponsesCount }}</div>
                                <div class="text-xs text-green-800">Positivas (4-5)</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-lg font-bold text-purple-600">{{ response.answers?.length || 0 }}</div>
                                <div class="text-xs text-purple-800">Respondidas</div>
                            </div>
                            <div class="text-center p-3 bg-amber-50 rounded-lg">
                                <div class="text-lg font-bold text-amber-600">{{ commentsCount }}</div>
                                <div class="text-xs text-amber-800">Comentarios</div>
                            </div>
                        </div>

                        <!-- Progreso -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso</span>
                                <span class="text-sm text-gray-600">{{ completionPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="isCompleted ? 'bg-green-500' : 'bg-blue-500'"
                                    :style="{ width: `${completionPercentage}%` }"
                                ></div>
                            </div>
                        </div>

                        <!-- Lista de preguntas y respuestas -->
                        <div class="space-y-4">
                            <div
                                v-for="(question, index) in getQuestionsArray()"
                                :key="question.id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <!-- Pregunta -->
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-base font-medium text-gray-900 mb-1">
                                            Pregunta {{ index + 1 }}
                                            <span v-if="question.is_required" class="text-red-500 ml-1 text-sm">*</span>
                                        </h4>
                                        <p class="text-sm text-gray-700">{{ question.question }}</p>
                                    </div>
                                    <div class="ml-3">
                                        <span
                                            v-if="getAnswerForQuestion(question.id)"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                            :class="getLikertColor(getAnswerForQuestion(question.id).likert_value)"
                                        >
                                            {{ getAnswerForQuestion(question.id).likert_value }}/5
                                        </span>
                                        <span v-else class="text-xs text-gray-400">No respondida</span>
                                    </div>
                                </div>

                                <!-- Respuesta -->
                                <div v-if="getAnswerForQuestion(question.id)" class="space-y-3">
                                    <!-- Escala Likert visual -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <span class="text-sm font-medium text-gray-700">Respuesta:</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex space-x-1">
                                                <div
                                                    v-for="value in [1, 2, 3, 4, 5]"
                                                    :key="value"
                                                    class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium border"
                                                    :class="value <= getAnswerForQuestion(question.id).likert_value 
                                                        ? 'bg-blue-600 text-white border-blue-600' 
                                                        : 'bg-gray-100 text-gray-400 border-gray-300'"
                                                >
                                                    {{ value }}
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-600 ml-2">
                                                {{ getLikertLabel(getAnswerForQuestion(question.id).likert_value) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Comentario -->
                                    <div v-if="getAnswerForQuestion(question.id).comment" class="p-3 bg-blue-50 rounded border-l-4 border-blue-400">
                                        <h5 class="text-xs font-medium text-blue-900 mb-1">Comentario:</h5>
                                        <p class="text-xs text-blue-800">{{ getAnswerForQuestion(question.id).comment }}</p>
                                    </div>
                                </div>

                                <!-- No respondida -->
                                <div v-else class="p-3 bg-gray-50 rounded border-dashed border-2 border-gray-300 text-center">
                                    <p class="text-xs text-gray-500">
                                        <span v-if="question.is_required" class="text-red-500 font-medium">Pregunta obligatoria</span>
                                        <span v-else>Pregunta opcional</span>
                                        - No respondida
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500">No hay datos de respuesta disponibles</p>
                    </div>
                </div>
            </div>

            <!-- Footer con botones -->
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
                <button
                    @click="closeModal"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cerrar
                </button>
                <button
                    v-if="response && !isCompleted && isSurveyActive"
                    @click="continueResponse"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Continuar respuesta
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    response: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close'])

const { formatDate, likertScale } = useSurveyResponse()

// Debug logs
watch(() => props.show, (newValue) => {
    console.log('Modal show state changed:', newValue)
})

watch(() => props.response, (newValue) => {
    console.log('Modal response changed:', newValue)
})

// Helper function to safely get questions array
const getQuestionsArray = () => {
    if (!props.response?.survey) return []
    
    const questions = props.response.survey.questions
    if (Array.isArray(questions)) {
        return questions
    } else if (questions?.data && Array.isArray(questions.data)) {
        return questions.data
    }
    return []
}

// Computadas
const isCompleted = computed(() => {
    return props.response?.is_complete || false
})

const completionPercentage = computed(() => {
    const totalQuestions = getQuestionsArray().length
    if (totalQuestions === 0) return 0
    return Math.round(((props.response?.answers?.length || 0) / totalQuestions) * 100)
})

const averageScore = computed(() => {
    if (!props.response?.answers || props.response.answers.length === 0) return '0.0'
    
    const sum = props.response.answers.reduce((acc, answer) => acc + (answer.likert_value || 0), 0)
    return (sum / props.response.answers.length).toFixed(1)
})

const positiveResponsesCount = computed(() => {
    if (!props.response?.answers || props.response.answers.length === 0) return 0
    return props.response.answers.filter(answer => (answer.likert_value || 0) >= 4).length
})

const commentsCount = computed(() => {
    if (!props.response?.answers) return 0
    return props.response.answers.filter(answer => answer.comment && answer.comment.trim()).length
})

const isSurveyActive = computed(() => {
    const survey = props.response?.survey
    if (!survey) return false
    
    if (!survey.is_active) return false
    if (survey.ends_at && new Date(survey.ends_at) < new Date()) return false
    return true
})

// Métodos
const getAnswerForQuestion = (questionId) => {
    if (!props.response?.answers) return null
    return props.response.answers.find(answer => answer.survey_question_id === questionId)
}

const getLikertColor = (value) => {
    const colors = {
        1: 'bg-red-100 text-red-800',
        2: 'bg-orange-100 text-orange-800',
        3: 'bg-yellow-100 text-yellow-800',
        4: 'bg-blue-100 text-blue-800',
        5: 'bg-green-100 text-green-800'
    }
    return colors[value] || 'bg-gray-100 text-gray-800'
}

const getLikertLabel = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.label : 'Sin respuesta'
}

const closeModal = () => {
    console.log('Cerrando modal')
    emit('close')
}

const continueResponse = () => {
    if (props.response?.survey?.id) {
        router.visit(route('patient.surveys.answer', props.response.survey.id))
        closeModal()
    }
}


</script>

<style scoped>
/* Estilos para mejor visibilidad */
.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

button {
    min-height: 40px;
    cursor: pointer;
    font-weight: 500;
}

button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>