<!-- resources/js/Pages/Doctor/Survey/Pages/Show.vue -->
<template>
    <Head :title="surveyData?.title || 'Encuesta'" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ surveyData?.title || 'Sin título' }}
                    </h2>
                    <p v-if="surveyData?.description" class="text-sm text-gray-600 mt-1">
                        {{ surveyData.description }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('doctor.surveys.show-results', surveyData?.id)"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                        v-if="surveyData?.id"
                    >
                        Ver resultados
                    </Link>
                    <Link
                        :href="route('doctor.surveys.edit', surveyData?.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                        v-if="surveyData?.id"
                    >
                        Editar encuesta
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
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Información general y estadísticas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <div class="flex items-center space-x-3 mb-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        :class="statusClasses"
                                    >
                                        {{ statusText }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        ID: {{ surveyData?.id || 'N/A' }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <span class="font-medium">Creado por:</span>
                                    {{ surveyData?.creator?.name || 'Usuario desconocido' }}
                                    <span class="mx-2">•</span>
                                    <span>{{ surveyData?.created_at ? formatDate(surveyData.created_at) : 'Fecha no disponible' }}</span>
                                </div>
                                
                                <div v-if="surveyData?.instructions" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="font-medium text-blue-900 mb-2">Instrucciones para pacientes:</h4>
                                    <p class="text-blue-800 text-sm">{{ surveyData.instructions }}</p>
                                </div>
                            </div>

                            <!-- Acciones rápidas -->
                            <div class="flex flex-col space-y-2">
                                <button
                                    @click="handleToggleStatus"
                                    class="text-sm px-3 py-1 rounded transition-colors"
                                    :class="surveyData?.is_active 
                                        ? 'bg-red-100 text-red-700 hover:bg-red-200' 
                                        : 'bg-green-100 text-green-700 hover:bg-green-200'"
                                    v-if="surveyData?.id"
                                >
                                    {{ surveyData?.is_active ? 'Desactivar' : 'Activar' }}
                                </button>

                            </div>
                        </div>



                        <!-- Estadísticas principales -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">{{ surveyData?.questions_count || questionsArray.length || 0 }}</div>
                                <div class="text-sm text-blue-800">Preguntas</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">{{ surveyData?.responses_count || 0 }}</div>
                                <div class="text-sm text-green-800">Respuestas</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-3xl font-bold text-purple-600">{{ completionRate }}%</div>
                                <div class="text-sm text-purple-800">Tasa Completitud</div>
                            </div>
                            <div class="text-center p-4 bg-amber-50 rounded-lg">
                                <div class="text-3xl font-bold text-amber-600">{{ daysRemaining }}</div>
                                <div class="text-sm text-amber-800">Días restantes</div>
                            </div>
                        </div>

                        <!-- Información temporal -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium text-gray-700">Creada:</span>
                                <span class="ml-1">{{ formatDate(survey.created_at) }}</span>
                            </div>
                            <div v-if="survey.starts_at">
                                <span class="font-medium text-gray-700">Inicia:</span>
                                <span class="ml-1">{{ formatDate(survey.starts_at) }}</span>
                            </div>
                            <div v-if="survey.ends_at">
                                <span class="font-medium text-gray-700">Termina:</span>
                                <span class="ml-1">{{ formatDate(survey.ends_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de actividad reciente -->
                <div v-if="survey.recent_responses && survey.recent_responses.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
                            <Link
                                :href="route('doctor.surveys.results', survey.id)"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Ver todos los resultados →
                            </Link>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="response in survey.recent_responses"
                                :key="response.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ response.patient.name }}</p>
                                        <p class="text-xs text-gray-600">{{ formatDate(response.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ response.completion_percentage }}%</p>
                                    <p class="text-xs text-gray-600">{{ response.answers_count }}/{{ survey.questions?.length || 0 }} preguntas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de preguntas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Preguntas de la Encuesta</h3>
                            <div class="text-sm text-gray-600">
                                {{ questionsArray.length }} pregunta{{ questionsArray.length !== 1 ? 's' : '' }}

                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(question, index) in questionsArray"
                                :key="question.id"
                                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                            >
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-medium text-gray-900">
                                        Pregunta {{ index + 1 }}
                                        <span v-if="question.is_required" class="text-red-500 ml-1">*</span>
                                    </h4>
                                    <div v-if="question.response_stats" class="text-right text-sm text-gray-600">
                                        <p>{{ question.response_stats.count }} respuestas</p>
                                        <p>Promedio: {{ question.response_stats.average?.toFixed(1) }}/5</p>
                                    </div>
                                </div>

                                <p class="text-gray-700 mb-4">{{ question.question }}</p>

                                <!-- Vista previa de escala Likert -->
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="scale in likertScale"
                                        :key="scale.value"
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="scale.color"
                                    >
                                        {{ scale.value }} - {{ scale.label }}
                                    </span>
                                </div>

                                <!-- Barra de progreso de respuestas -->
                                <div v-if="question.response_stats && question.response_stats.count > 0" class="mt-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs text-gray-600">Distribución de respuestas</span>
                                        <span class="text-xs text-gray-600">{{ question.response_stats.count }} respuestas</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            :style="{ width: `${getResponsePercentage(question)}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useSurveys } from '../Composables/useSurveys'

const props = defineProps({
    survey: {
        type: Object,
        required: true
    }
})

// Fix para extraer los datos si vienen anidados en 'data'
const surveyData = computed(() => {
    return props.survey?.data ? props.survey.data : props.survey
})

const { formatDate, getSurveyStatus, likertScale } = useSurveys()

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

// Computadas
const statusData = computed(() => getSurveyStatus(surveyData.value))

const statusClasses = computed(() => {
    if (!surveyData.value) return 'bg-gray-100 text-gray-800'
    return statusData.value.class || 'bg-gray-100 text-gray-800'
})

const statusText = computed(() => {
    if (!surveyData.value) return 'Desconocido'
    return statusData.value.text || 'Desconocido'
})

const completionRate = computed(() => {
    if (!surveyData.value || !surveyData.value.total_possible_responses || surveyData.value.total_possible_responses === 0) {
        return 0
    }
    return Math.round((surveyData.value.responses_count / surveyData.value.total_possible_responses) * 100)
})

const daysRemaining = computed(() => {
    if (!surveyData.value || !surveyData.value.ends_at) return '∞'
    
    try {
        const endDate = new Date(surveyData.value.ends_at)
        const today = new Date()
        
        // Validar que la fecha sea válida
        if (isNaN(endDate.getTime())) {
            return '∞'
        }
        
        const diffTime = endDate - today
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
        
        return diffDays > 0 ? diffDays : 0
    } catch (error) {
        return '∞'
    }
})



// Métodos
const getResponsePercentage = (question) => {
    if (!question.response_stats || !surveyData.value?.responses_count) return 0
    return Math.round((question.response_stats.count / surveyData.value.responses_count) * 100)
}

const handleToggleStatus = () => {
    if (!surveyData.value || !surveyData.value.id) {
        console.error('No hay información de la encuesta disponible')
        return
    }
    
    const action = surveyData.value.is_active ? 'desactivar' : 'activar'
    if (confirm(`¿Estás seguro de que quieres ${action} esta encuesta?`)) {
        router.patch(route('doctor.surveys.toggle-status', surveyData.value.id), {
            is_active: !surveyData.value.is_active
        }, {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Error al cambiar el estado de la encuesta:', errors)
            }
        })
    }
}




</script>