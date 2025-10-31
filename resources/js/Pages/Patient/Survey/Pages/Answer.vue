<!-- resources/js/Pages/Patient/Survey/Pages/Answer.vue -->
<template>
    <Head :title="`Responder: ${survey.title}`" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ survey.title }}
                    </h2>
                    <p v-if="survey.description" class="text-sm text-gray-600 mt-1">
                        {{ survey.description }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">
                        Pregunta {{ currentQuestionIndex + 1 }} de {{ questionsArray.length }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ Math.round(progress) }}% completado
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Barra de progreso -->
                <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progreso de la encuesta</span>
                        <span class="text-sm text-gray-600">{{ answeredQuestions }}/{{ questionsArray.length }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div
                            class="bg-blue-600 h-3 rounded-full transition-all duration-300 ease-out"
                            :style="{ width: `${progress}%` }"
                        ></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Iniciado</span>
                        <span v-if="progress > 50" class="text-blue-600 font-medium">¡Más de la mitad!</span>
                        <span>Completado</span>
                    </div>
                </div>

                <!-- Instrucciones (solo al inicio) -->
                <div v-if="currentQuestionIndex === 0 && survey.instructions && !hasStarted" class="mb-6 bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Instrucciones:</h4>
                            <p class="text-sm text-blue-800 mb-4">{{ survey.instructions }}</p>
                            <button
                                @click="hasStarted = true"
                                class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
                            >
                                Entendido, comenzar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pregunta actual -->
                <div v-if="hasStarted && currentQuestion">
                    <LikertQuestion
                        :question="currentQuestion"
                        :question-number="currentQuestionIndex + 1"
                        :total-questions="questionsArray.length"
                        v-model="currentAnswer"
                        :comment="currentComment"
                        :error="showValidationError && currentQuestion.is_required && !currentAnswer"
                        @comment-change="currentComment = $event"
                    />
                </div>

                <!-- Navegación -->
                <div v-if="hasStarted" class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center">
                        <!-- Botón anterior -->
                        <button
                            @click="goToPreviousQuestion"
                            :disabled="currentQuestionIndex === 0"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            ← Anterior
                        </button>

                        <!-- Indicadores de preguntas -->
                        <div class="flex space-x-2 max-w-md overflow-x-auto">
                            <button
                                v-for="(question, index) in questionsArray"
                                :key="question.id"
                                @click="goToQuestion(index)"
                                class="w-8 h-8 rounded-full text-xs font-medium transition-colors flex-shrink-0"
                                :class="getQuestionIndicatorClass(index)"
                                :title="`Pregunta ${index + 1}`"
                            >
                                {{ index + 1 }}
                            </button>
                        </div>

                        <!-- Botón siguiente/finalizar -->
                        <button
                            v-if="currentQuestionIndex < questionsArray.length - 1"
                            @click="goToNextQuestion"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Siguiente →
                        </button>
                        <button
                            v-else
                            @click="showSubmitConfirmation = true"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                        >
                            Finalizar Encuesta
                        </button>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                        <div>
                            <span v-if="answeredQuestions > 0">
                                {{ answeredQuestions }} de {{ questionsArray.length }} preguntas respondidas
                            </span>
                            <span v-else>
                                Comienza respondiendo la primera pregunta
                            </span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button
                                @click="saveProgress"
                                :disabled="savingProgress"
                                class="text-blue-600 hover:text-blue-800 disabled:opacity-50"
                            >
                                <span class="flex items-center">
                                    <svg v-if="savingProgress" class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ savingProgress ? 'Guardando...' : 'Guardar progreso' }}
                                </span>
                            </button>
                            <button
                                @click="showExitConfirmation = true"
                                class="text-red-600 hover:text-red-800"
                            >
                                Salir
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resumen antes de enviar -->
                <div v-if="showSubmitConfirmation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-0 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Revisar Respuestas</h3>
                                <button 
                                    @click="showSubmitConfirmation = false"
                                    class="text-gray-400 hover:text-gray-600"
                                >
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Estadísticas del resumen -->
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center p-3 bg-blue-50 rounded">
                                    <div class="text-2xl font-bold text-blue-600">{{ answeredQuestions }}</div>
                                    <div class="text-xs text-blue-800">Respondidas</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded">
                                    <div class="text-2xl font-bold text-green-600">{{ Math.round(progress) }}%</div>
                                    <div class="text-xs text-green-800">Completado</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded">
                                    <div class="text-2xl font-bold text-purple-600">{{ averageScore }}</div>
                                    <div class="text-xs text-purple-800">Promedio</div>
                                </div>
                            </div>

                            <!-- Lista de respuestas -->
                            <div class="max-h-64 overflow-y-auto mb-6 space-y-3">
                                <div
                                    v-for="(question, index) in questionsArray"
                                    :key="question.id"
                                    class="p-3 border rounded-lg"
                                    :class="answers[question.id] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'"
                                >
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                P{{ index + 1 }}: {{ question.question }}
                                            </p>
                                            <p v-if="answers[question.id]" class="text-sm text-gray-600 mt-1">
                                                Respuesta: {{ answers[question.id] }} - {{ getLikertLabel(answers[question.id]) }}
                                            </p>
                                            <p v-else class="text-sm text-red-600 mt-1">
                                                {{ question.is_required ? 'Pregunta obligatoria sin responder' : 'Sin responder (opcional)' }}
                                            </p>
                                        </div>
                                        <button
                                            @click="goToQuestionAndCloseModal(index)"
                                            class="text-blue-600 hover:text-blue-800 text-sm ml-2"
                                        >
                                            Editar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button
                                    @click="showSubmitConfirmation = false"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Continuar editando
                                </button>
                                <button
                                    @click="submitSurvey"
                                    :disabled="submitting || !canSubmit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {{ submitting ? 'Enviando...' : 'Enviar respuestas' }}
                                </button>
                            </div>

                            <p v-if="!canSubmit" class="text-red-600 text-sm mt-2">
                                Faltan respuestas obligatorias. Por favor, completa todas las preguntas marcadas con *.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmación de salida -->
                <div v-if="showExitConfirmation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">¿Salir de la encuesta?</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Si sales ahora, tu progreso se guardará automáticamente y podrás continuar más tarde.
                            </p>
                            <div class="flex justify-end space-x-3">
                                <button
                                    @click="showExitConfirmation = false"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors"
                                >
                                    Continuar
                                </button>
                                <button
                                    @click="exitSurvey"
                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                                >
                                    Salir y guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div
            v-if="notification.show"
            class="fixed top-4 right-4 z-50 max-w-sm"
        >
            <div
                class="rounded-lg p-4 shadow-lg border"
                :class="{
                    'bg-green-50 border-green-200 text-green-800': notification.type === 'success',
                    'bg-red-50 border-red-200 text-red-800': notification.type === 'error',
                    'bg-blue-50 border-blue-200 text-blue-800': notification.type === 'info'
                }"
            >
                <div class="flex items-center">
                    <svg
                        v-if="notification.type === 'success'"
                        class="w-5 h-5 mr-2 text-green-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg
                        v-else-if="notification.type === 'error'"
                        class="w-5 h-5 mr-2 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg
                        v-else
                        class="w-5 h-5 mr-2 text-blue-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ notification.message }}</span>
                    <button
                        @click="notification.show = false"
                        class="ml-auto -mr-1 p-1 hover:bg-black hover:bg-opacity-10 rounded"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LikertQuestion from '../Components/LikertQuestion.vue'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    survey: Object,
    existingResponse: Object
})

const { initializeAnswers, likertScale, validateAnswers } = useSurveyResponse()

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
const hasStarted = ref(false)
const currentQuestionIndex = ref(0)
const answers = ref({})
const comments = ref({})
const showValidationError = ref(false)
const showSubmitConfirmation = ref(false)
const showExitConfirmation = ref(false)
const submitting = ref(false)
const savingProgress = ref(false)

// Notification system
const notification = ref({
    show: false,
    message: '',
    type: 'success' // 'success' | 'error' | 'info'
})

const showNotification = (message, type = 'success') => {
    notification.value = {
        show: true,
        message,
        type
    }
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        notification.value.show = false
    }, 3000)
}

// Computadas para la pregunta actual
const currentQuestion = computed(() => questionsArray.value[currentQuestionIndex.value])
const currentAnswer = computed({
    get: () => answers.value[currentQuestion.value?.id],
    set: (value) => {
        if (currentQuestion.value) {
            answers.value[currentQuestion.value.id] = value
            showValidationError.value = false
            autoSave()
        }
    }
})

const currentComment = computed({
    get: () => comments.value[currentQuestion.value?.id] || '',
    set: (value) => {
        if (currentQuestion.value) {
            comments.value[currentQuestion.value.id] = value
            autoSave()
        }
    }
})

// Estadísticas de progreso
const answeredQuestions = computed(() => {
    return Object.keys(answers.value).filter(id => answers.value[id] != null).length
})

const progress = computed(() => {
    if (questionsArray.value.length === 0) return 0
    return (answeredQuestions.value / questionsArray.value.length) * 100
})

const averageScore = computed(() => {
    const answered = Object.values(answers.value).filter(v => v != null)
    if (answered.length === 0) return '0.0'
    const sum = answered.reduce((acc, val) => acc + val, 0)
    return (sum / answered.length).toFixed(1)
})

const canSubmit = computed(() => {
    const requiredQuestions = questionsArray.value.filter(q => q.is_required)
    return requiredQuestions.every(q => answers.value[q.id] != null)
})

// Métodos de navegación
const goToQuestion = (index) => {
    if (index >= 0 && index < questionsArray.value.length) {
        currentQuestionIndex.value = index
        showValidationError.value = false
    }
}

const goToPreviousQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--
        showValidationError.value = false
    }
}

const goToNextQuestion = () => {
    const current = currentQuestion.value
    
    // Validar pregunta obligatoria
    if (current.is_required && !currentAnswer.value) {
        showValidationError.value = true
        return
    }
    
    if (currentQuestionIndex.value < questionsArray.value.length - 1) {
        currentQuestionIndex.value++
        showValidationError.value = false
    }
}

const goToQuestionAndCloseModal = (index) => {
    showSubmitConfirmation.value = false
    goToQuestion(index)
}

// Métodos para indicadores visuales
const getQuestionIndicatorClass = (index) => {
    const question = questionsArray.value[index]
    const hasAnswer = answers.value[question?.id] != null
    const isCurrent = index === currentQuestionIndex.value
    
    if (isCurrent) {
        return 'bg-blue-600 text-white border-2 border-blue-600'
    } else if (hasAnswer) {
        return 'bg-green-100 text-green-800 border-2 border-green-300'
    } else if (question.is_required) {
        return 'bg-red-100 text-red-800 border-2 border-red-300'
    } else {
        return 'bg-gray-100 text-gray-600 border-2 border-gray-300'
    }
}

const getLikertLabel = (value) => {
    const option = likertScale.find(item => item.value === value)
    return option ? option.label : 'N/A'
}

// Auto-guardado
let autoSaveTimeout = null

const autoSave = () => {
    if (autoSaveTimeout) {
        clearTimeout(autoSaveTimeout)
    }
    
    autoSaveTimeout = setTimeout(() => {
        // Silent auto-save without showing notification
        if (savingProgress.value) return
        
        savingProgress.value = true
        
        router.post(route('patient.surveys.save-progress', props.survey.id), {
            answers: answers.value,
            comments: comments.value,
            current_question: currentQuestionIndex.value
        }, {
            preserveState: true,
            preserveScroll: true,
            only: [],
            onFinish: () => {
                savingProgress.value = false
            }
        })
    }, 2000) // Guardar después de 2 segundos de inactividad
}

const saveProgress = async () => {
    if (savingProgress.value) return
    
    savingProgress.value = true
    
    try {
        router.post(route('patient.surveys.save-progress', props.survey.id), {
            answers: answers.value,
            comments: comments.value,
            current_question: currentQuestionIndex.value
        }, {
            preserveState: true,
            preserveScroll: true,
            only: [], // No actualizar nada en la UI
            onSuccess: () => {
                // Show success notification
                showNotification('Progreso guardado correctamente', 'success')
            },
            onError: (errors) => {
                // Show error notification
                showNotification('Error al guardar el progreso', 'error')
                console.error('Error saving progress:', errors)
            }
        })
    } catch (error) {
        console.error('Error saving progress:', error)
        showNotification('Error al guardar el progreso', 'error')
    } finally {
        savingProgress.value = false
    }
}

// Envío final
const submitSurvey = async () => {
    if (!canSubmit.value || submitting.value) return
    
    submitting.value = true
    
    try {
        // Transformar los datos al formato esperado por el backend
        const formattedAnswers = Object.keys(answers.value).map(questionId => ({
            survey_question_id: parseInt(questionId),
            likert_value: answers.value[questionId],
            comment: comments.value[questionId] || null
        }))
        
        router.post(route('patient.surveys.submit', props.survey.id), {
            answers: formattedAnswers
        }, {
            onSuccess: () => {
                router.visit(route('patient.surveys.index'))
            },
            onError: (errors) => {
                console.error('Submission errors:', errors)
                submitting.value = false
                showSubmitConfirmation.value = false
            }
        })
    } catch (error) {
        console.error('Error submitting survey:', error)
        submitting.value = false
    }
}

// Salir de la encuesta
const exitSurvey = () => {
    saveProgress().then(() => {
        router.visit(route('patient.surveys.index'))
    })
}

// Prevenir salida accidental
const handleBeforeUnload = (event) => {
    if (Object.keys(answers.value).length > 0) {
        event.preventDefault()
        event.returnValue = ''
    }
}

// Inicialización
onMounted(() => {
    // Cargar respuestas existentes si las hay
    if (props.existingResponse) {
        props.existingResponse.answers.forEach(answer => {
            answers.value[answer.survey_question_id] = answer.likert_value
            if (answer.comment) {
                comments.value[answer.survey_question_id] = answer.comment
            }
        })
        
        // Ir a la primera pregunta sin respuesta o a la primera
        let startIndex = 0
        for (let i = 0; i < questionsArray.value.length; i++) {
            const questionId = questionsArray.value[i].id
            if (!answers.value[questionId]) {
                startIndex = i
                break
            }
        }
        currentQuestionIndex.value = startIndex
        hasStarted.value = true
    }
    
    // Agregar listener para prevenir salida accidental
    window.addEventListener('beforeunload', handleBeforeUnload)
})

onBeforeUnmount(() => {
    // Limpiar timeout de auto-guardado
    if (autoSaveTimeout) {
        clearTimeout(autoSaveTimeout)
    }
    
    // Remover listener
    window.removeEventListener('beforeunload', handleBeforeUnload)
    
    // Guardar progreso final
    if (Object.keys(answers.value).length > 0) {
        saveProgress()
    }
})
</script>