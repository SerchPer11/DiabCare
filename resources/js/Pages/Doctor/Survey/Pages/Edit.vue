<!-- resources/js/Pages/Doctor/Survey/Pages/Edit.vue -->
<template>
    <Head :title="`Editar: ${surveyData?.title || 'Encuesta'}`" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Encuesta: {{ surveyData?.title || 'Sin título' }}
                </h2>
                <div class="flex space-x-3">
                    <Link
                        :href="route('doctor.surveys.show', surveyData?.id)"
                        class="text-blue-600 hover:text-blue-800 transition-colors"
                        v-if="surveyData?.id"
                    >
                        Ver encuesta
                    </Link>
                    <Link
                        :href="route('doctor.surveys.index')"
                        class="text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        ← Volver a mis encuestas
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Información de la encuesta -->
                <div class="mb-6 bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Información de la Encuesta</h3>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Creada:</span>
                                    <span class="ml-1">{{ formatDate(surveyData?.created_at) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Respuestas:</span>
                                    <span class="ml-1">{{ surveyData?.responses_count || 0 }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Estado:</span>
                                    <span 
                                        class="ml-1"
                                        :class="surveyData?.is_active ? 'text-green-600' : 'text-red-600'"
                                    >
                                        {{ surveyData?.is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Preguntas:</span>
                                    <span class="ml-1">{{ questionsArray.length }}</span>
                                </div>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="statusClasses"
                        >
                            {{ statusText }}
                        </span>
                    </div>
                </div>

                <!-- Advertencias -->
                <div v-if="hasResponses && !showForceEdit" class="mb-6 bg-amber-50 rounded-lg border border-amber-200 p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-amber-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-amber-900 mb-2">⚠️ Encuesta con Respuestas</h4>
                            <p class="text-sm text-amber-800 mb-3">
                                Esta encuesta ya tiene {{ surveyData?.responses_count || 0 }} respuesta(s) de pacientes. 
                                Modificar las preguntas puede afectar la coherencia de los datos recopilados.
                            </p>
                            <div class="flex space-x-3">
                                <button
                                    @click="showForceEdit = true"
                                    class="text-sm bg-amber-600 text-white px-3 py-1 rounded hover:bg-amber-700 transition-colors"
                                >
                                    Editar de todas formas
                                </button>
                                <Link
                                    :href="route('doctor.surveys.results', survey.id)"
                                    class="text-sm border border-amber-600 text-amber-700 px-3 py-1 rounded hover:bg-amber-50 transition-colors"
                                >
                                    Ver resultados actuales
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consejos para edición -->
                <div v-if="hasResponses && showForceEdit" class="mb-6 bg-red-50 rounded-lg border border-red-200 p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-900 mb-2">Recomendaciones para Edición:</h4>
                            <ul class="text-sm text-red-800 space-y-1">
                                <li>• Evita cambiar el significado de las preguntas existentes</li>
                                <li>• Puedes agregar nuevas preguntas al final</li>
                                <li>• Considera crear una nueva encuesta para cambios mayores</li>
                                <li>• Los cambios en fechas no afectarán las respuestas existentes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulario de edición -->
                <div v-if="!hasResponses || showForceEdit">
                    <SurveyForm
                        :survey="surveyForForm"
                        :processing="form.processing"
                        :errors="form.errors"
                        route-name="doctor.surveys.update"
                        @submit="handleSubmit"
                        @cancel="handleCancel"
                    />
                </div>

                <!-- Historial de cambios (si existe) -->
                <div v-if="surveyData?.change_log && surveyData.change_log.length > 0" class="mt-8 bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Cambios</h3>
                    <div class="space-y-3">
                        <div
                            v-for="change in surveyData.change_log"
                            :key="change.id"
                            class="flex items-start space-x-3 text-sm"
                        >
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-gray-900">{{ change.description }}</p>
                                <p class="text-gray-500">{{ formatDate(change.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SurveyForm from '../Components/SurveyForm.vue'
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

const { formatDate, getSurveyStatus, validateSurveyForm } = useSurveys()

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

// Computed para preparar datos del survey para SurveyForm
const surveyForForm = computed(() => {
    if (!surveyData.value) return null
    
    return {
        ...surveyData.value,
        questions: questionsArray.value
    }
})

// Estados reactivos
const showForceEdit = ref(false)

// Computadas
const hasResponses = computed(() => (surveyData.value?.responses_count || 0) > 0)

const statusData = computed(() => getSurveyStatus(surveyData.value))

const statusClasses = computed(() => {
    if (!surveyData.value) return 'bg-gray-100 text-gray-800'
    return statusData.value.class || 'bg-gray-100 text-gray-800'
})

const statusText = computed(() => {
    if (!surveyData.value) return 'Desconocido'
    return statusData.value.text || 'Desconocido'
})

// Inicializar formulario con datos seguros
const form = useForm({
    title: '',
    description: '',
    instructions: '',
    is_active: false,
    starts_at: null,
    ends_at: null,
    questions: []
})

// Poblar formulario cuando los datos estén disponibles
watchEffect(() => {
    if (surveyData.value) {
        form.title = surveyData.value.title || ''
        form.description = surveyData.value.description || ''
        form.instructions = surveyData.value.instructions || ''
        form.is_active = surveyData.value.is_active || false
        form.starts_at = surveyData.value.starts_at || null
        form.ends_at = surveyData.value.ends_at || null
        
        // Manejar questions usando questionsArray
        if (questionsArray.value.length > 0) {
            form.questions = questionsArray.value.map(q => ({
                id: q?.id,
                question: q?.question || '',
                is_required: q?.is_required || true
            }))
        } else {
            form.questions = []
        }
    }
})

const handleSubmit = (formData) => {
    // Validar formulario
    const errors = validateSurveyForm(formData)
    if (Object.keys(errors).length > 0) {
        return
    }

    // Actualizar form con los datos
    Object.keys(formData).forEach(key => {
        form[key] = formData[key]
    })

    // Enviar actualización
    form.put(route('doctor.surveys.update', surveyData.value?.id), {
        onSuccess: () => {
            router.visit(route('doctor.surveys.show', surveyData.value?.id))
        }
    })
}

const handleCancel = () => {
    if (form.isDirty) {
        if (confirm('¿Estás seguro de que quieres cancelar? Se perderán todos los cambios.')) {
            router.visit(route('doctor.surveys.show', surveyData.value?.id))
        }
    } else {
        router.visit(route('doctor.surveys.show', surveyData.value?.id))
    }
}
</script>