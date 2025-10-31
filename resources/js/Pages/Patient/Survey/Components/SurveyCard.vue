<!-- resources/js/Pages/Patient/Survey/Components/SurveyCard.vue -->
<template>
    <div class="bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ survey.title }}</h3>
                    <p v-if="survey.description" class="text-sm text-gray-600 mb-2">
                        {{ survey.description }}
                    </p>
                    <div class="flex items-center space-x-3 text-sm">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="statusClasses"
                        >
                            {{ statusText }}
                        </span>
                        <span v-if="survey.questions_count" class="text-gray-500">
                            {{ survey.questions_count }} preguntas
                        </span>
                        <span v-if="estimatedTime" class="text-gray-500">
                            ~{{ estimatedTime }} min
                        </span>
                    </div>
                </div>
                <div v-if="completionStatus" class="ml-4 text-right">
                    <div class="text-2xl">{{ completionStatus.icon }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ completionStatus.text }}</div>
                </div>
            </div>

            <!-- Instrucciones (si las hay) -->
            <div v-if="survey.instructions" class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 mb-1">Instrucciones:</h4>
                        <p class="text-sm text-blue-800">{{ survey.instructions }}</p>
                    </div>
                </div>
            </div>

            <!-- Progreso (si ya se empezó) -->
            <div v-if="responseData && responseData.progress > 0" class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso</span>
                    <span class="text-sm text-gray-600">
                        {{ responseData.answered }}/{{ survey.questions_count }} respuestas
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${responseData.progress}%` }"
                    ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ responseData.progress.toFixed(0) }}% completado
                </p>
            </div>

            <!-- Información temporal -->
            <div class="mb-4 text-xs text-gray-500 space-y-1">
                <div v-if="survey.starts_at" class="flex justify-between">
                    <span>Disponible desde:</span>
                    <span>{{ formatDate(survey.starts_at) }}</span>
                </div>
                <div v-if="survey.ends_at" class="flex justify-between">
                    <span>Disponible hasta:</span>
                    <span>{{ formatDate(survey.ends_at) }}</span>
                </div>
                <div v-if="daysRemaining !== null" class="flex justify-between">
                    <span>Tiempo restante:</span>
                    <span :class="daysRemaining <= 3 ? 'text-red-500 font-medium' : ''">
                        {{ daysRemaining > 0 ? `${daysRemaining} días` : 'Expirado' }}
                    </span>
                </div>
            </div>

            <!-- Última respuesta (si existe) -->
            <div v-if="lastResponse" class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Última respuesta</p>
                        <p class="text-xs text-gray-500">{{ formatDate(lastResponse.created_at) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">
                            {{ lastResponse.completion_percentage }}% completado
                        </p>
                        <p class="text-xs text-gray-500">
                            Puntuación: {{ lastResponse.average_score }}/5
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex space-x-3">
                <!-- Botón principal según el estado -->
                <button
                    v-if="canAnswer"
                    @click="handleAnswer"
                    class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ responseData?.progress > 0 ? 'Continuar respuesta' : 'Responder encuesta' }}
                </button>
                
                <button
                    v-else-if="!isAvailable"
                    disabled
                    class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed"
                >
                    {{ survey.ends_at && new Date(survey.ends_at) < new Date() ? 'Encuesta expirada' : 'No disponible aún' }}
                </button>

                <button
                    v-else-if="isCompleted"
                    @click="handleViewResponse"
                    class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
                >
                    Ver mi respuesta
                </button>

                <!-- Botón secundario -->
                <button
                    v-if="hasResponses"
                    @click="handleViewHistory"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    survey: {
        type: Object,
        required: true
    },
    responseData: {
        type: Object,
        default: null
    }
})

const { formatDate } = useSurveyResponse()

// Computadas para el estado de la encuesta
const isAvailable = computed(() => {
    const now = new Date()
    const startDate = props.survey.starts_at ? new Date(props.survey.starts_at) : null
    const endDate = props.survey.ends_at ? new Date(props.survey.ends_at) : null
    
    if (startDate && now < startDate) return false
    if (endDate && now > endDate) return false
    
    return props.survey.is_active
})

const isCompleted = computed(() => {
    return props.responseData?.progress === 100
})

const canAnswer = computed(() => {
    return isAvailable.value && !isCompleted.value
})

const hasResponses = computed(() => {
    return props.survey.my_responses_count > 0
})

const lastResponse = computed(() => {
    return props.survey.last_response || null
})

const statusClasses = computed(() => {
    if (!isAvailable.value) {
        return 'bg-gray-100 text-gray-800'
    }
    if (isCompleted.value) {
        return 'bg-green-100 text-green-800'
    }
    if (props.responseData?.progress > 0) {
        return 'bg-yellow-100 text-yellow-800'
    }
    return 'bg-blue-100 text-blue-800'
})

const statusText = computed(() => {
    if (!isAvailable.value) {
        if (props.survey.ends_at && new Date(props.survey.ends_at) < new Date()) {
            return 'Expirada'
        }
        return 'No disponible'
    }
    if (isCompleted.value) {
        return 'Completada'
    }
    if (props.responseData?.progress > 0) {
        return 'En progreso'
    }
    return 'Disponible'
})

const completionStatus = computed(() => {
    if (isCompleted.value) {
        return { icon: '✅', text: 'Completada' }
    }
    if (props.responseData?.progress > 0) {
        return { icon: '⏳', text: 'En progreso' }
    }
    if (!isAvailable.value) {
        return { icon: '🔒', text: 'No disponible' }
    }
    return { icon: '📝', text: 'Pendiente' }
})

const estimatedTime = computed(() => {
    // Estimación: 30 segundos por pregunta
    if (props.survey.questions_count) {
        return Math.ceil((props.survey.questions_count * 0.5))
    }
    return null
})

const daysRemaining = computed(() => {
    if (!props.survey.ends_at) return null
    
    const endDate = new Date(props.survey.ends_at)
    const now = new Date()
    const diffTime = endDate - now
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    return diffDays
})

// Métodos de acción
const handleAnswer = () => {
    router.visit(route('patient.surveys.answer', props.survey.id))
}

const handleViewResponse = () => {
    if (lastResponse.value) {
        router.visit(route('patient.surveys.show-response', lastResponse.value.id))
    }
}

const handleViewHistory = () => {
    router.visit(route('patient.surveys.my-responses', { survey: props.survey.id }))
}
</script>