<!-- resources/js/Pages/Doctor/Survey/Pages/Create.vue -->
<template>
    <Head title="Crear Encuesta" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Nueva Encuesta
                </h2>
                <Link
                    :href="route('doctor.surveys.index')"
                    class="text-gray-600 hover:text-gray-900 transition-colors"
                >
                    ← Volver a mis encuestas
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Progreso de creación -->
                <div class="mb-8 bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Progreso de Creación</h3>
                        <span class="text-sm text-gray-600">Paso 1 de 1</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Completa la información básica y las preguntas de tu encuesta.
                    </p>
                </div>

                <!-- Consejos para crear encuestas -->
                <div class="mb-6 bg-blue-50 rounded-lg border border-blue-200 p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Consejos para crear una buena encuesta:</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Usa un título claro y descriptivo</li>
                                <li>• Redacta preguntas específicas y fáciles de entender</li>
                                <li>• Establece fechas de inicio y fin apropiadas</li>
                                <li>• Incluye entre 10-20 preguntas para mejores resultados</li>
                                <li>• Proporciona instrucciones claras a tus pacientes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulario de creación -->
                <div v-if="!showPreview">
                    <SurveyForm
                        :processing="form.processing"
                        :errors="form.errors"
                        route-name="doctor.surveys.store"
                        @submit="handleSubmit"
                        @cancel="handleCancel"
                    />
                </div>

                <!-- Vista previa -->
                <div v-else class="space-y-6">
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Vista Previa de la Encuesta</h3>
                                <button
                                    @click="showPreview = false"
                                    class="text-blue-600 hover:text-blue-800 transition-colors"
                                >
                                    ← Editar encuesta
                                </button>
                            </div>

                            <!-- Vista previa del contenido -->
                            <div class="border-l-4 border-blue-500 pl-6 mb-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ previewData.title }}</h2>
                                <p v-if="previewData.description" class="text-gray-600 mb-4">{{ previewData.description }}</p>
                                
                                <div v-if="previewData.instructions" class="bg-blue-50 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-blue-900 mb-2">Instrucciones:</h4>
                                    <p class="text-blue-800 text-sm">{{ previewData.instructions }}</p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Estado:</span>
                                        <span class="ml-1" :class="previewData.is_active ? 'text-green-600' : 'text-red-600'">
                                            {{ previewData.is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </div>
                                    <div v-if="previewData.starts_at">
                                        <span class="font-medium text-gray-700">Inicia:</span>
                                        <span class="ml-1 text-gray-600">{{ formatDate(previewData.starts_at) }}</span>
                                    </div>
                                    <div v-if="previewData.ends_at">
                                        <span class="font-medium text-gray-700">Termina:</span>
                                        <span class="ml-1 text-gray-600">{{ formatDate(previewData.ends_at) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Preguntas:</span>
                                        <span class="ml-1 text-gray-600">{{ previewData.questions.length }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Preguntas -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-gray-900">Preguntas:</h4>
                                <div
                                    v-for="(question, index) in previewData.questions"
                                    :key="index"
                                    class="bg-gray-50 rounded-lg p-4 border"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-gray-900">
                                            Pregunta {{ index + 1 }}
                                            <span v-if="question.is_required" class="text-red-500 ml-1">*</span>
                                        </h5>
                                    </div>
                                    <p class="text-gray-700 mb-3">{{ question.question }}</p>
                                    
                                    <!-- Escala Likert de muestra -->
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de confirmación -->
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showPreview = false"
                            type="button"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Editar encuesta
                        </button>
                        <button
                            @click="confirmCreate"
                            :disabled="form.processing"
                            type="button"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="form.processing">Creando...</span>
                            <span v-else>Crear Encuesta</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SurveyForm from '../Components/SurveyForm.vue'
import { useSurveys } from '../Composables/useSurveys'

const { likertScale, formatDate, validateSurveyForm } = useSurveys()

// Estados reactivos
const showPreview = ref(false)
const previewData = ref({})

// Formulario Inertia
const form = useForm({
    title: '',
    description: '',
    instructions: '',
    is_active: true,
    starts_at: '',
    ends_at: '',
    questions: []
})

const handleSubmit = (formData) => {
    // Validar formulario
    const errors = validateSurveyForm(formData)
    if (Object.keys(errors).length > 0) {
        // Los errores se muestran en el componente SurveyForm
        return
    }

    // Guardar datos para vista previa
    previewData.value = { ...formData }
    
    // Actualizar form con los datos
    Object.keys(formData).forEach(key => {
        form[key] = formData[key]
    })

    // Mostrar vista previa
    showPreview.value = true
}

const confirmCreate = () => {
    form.post(route('doctor.surveys.store'), {
        onSuccess: () => {
            router.visit(route('doctor.surveys.index'))
        },
        onError: (errors) => {
            // Si hay errores, volver al formulario
            showPreview.value = false
        }
    })
}

const handleCancel = () => {
    if (confirm('¿Estás seguro de que quieres cancelar? Se perderán todos los cambios.')) {
        router.visit(route('doctor.surveys.index'))
    }
}
</script>