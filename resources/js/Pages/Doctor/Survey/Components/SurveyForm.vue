<!-- resources/js/Pages/Doctor/Survey/Components/SurveyForm.vue -->
<template>
    <div class="space-y-6">
        <!-- Información básica de la encuesta -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-medium mb-4">Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Título *
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': allErrors.title }"
                        placeholder="Ej: Encuesta de Calidad de Vida"
                    />
                    <InputError :message="allErrors.title" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                    </label>
                    <select
                        v-model="form.is_active"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': allErrors.is_active }"
                    >
                        <option :value="true">Activa</option>
                        <option :value="false">Inactiva</option>
                    </select>
                    <InputError :message="allErrors.is_active" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Inicio *
                    </label>
                    <input
                        v-model="form.starts_at"
                        type="date"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': allErrors.starts_at }"
                    />
                    <InputError :message="allErrors.starts_at" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Fin *
                    </label>
                    <input
                        v-model="form.ends_at"
                        type="date"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': allErrors.ends_at }"
                    />
                    <InputError :message="allErrors.ends_at" class="mt-1" />
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción *
                </label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': allErrors.description }"
                    placeholder="Describe el propósito de esta encuesta..."
                ></textarea>
                <InputError :message="allErrors.description" class="mt-1" />
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Instrucciones para los pacientes *
                </label>
                <textarea
                    v-model="form.instructions"
                    rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': allErrors.instructions }"
                    placeholder="Ej: Por favor responde basándote en tu experiencia de las últimas 2 semanas..."
                ></textarea>
                <InputError :message="allErrors.instructions" class="mt-1" />
            </div>
        </div>

        <!-- Preguntas de la encuesta -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Preguntas de la Encuesta</h3>
                <button
                    @click="addQuestion"
                    type="button"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    + Agregar Pregunta
                </button>
            </div>

            <div v-if="form.questions.length === 0" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay preguntas</h3>
                <p class="mt-1 text-sm text-gray-500">Comienza agregando la primera pregunta de tu encuesta.</p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="(question, index) in form.questions"
                    :key="index"
                    class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                >
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm font-medium text-gray-600">Pregunta {{ index + 1 }}</span>
                        <div class="flex space-x-2">
                            <button
                                @click="moveQuestion(index, -1)"
                                :disabled="index === 0"
                                type="button"
                                class="text-gray-400 hover:text-gray-600 disabled:opacity-50"
                            >
                                ↑
                            </button>
                            <button
                                @click="moveQuestion(index, 1)"
                                :disabled="index === form.questions.length - 1"
                                type="button"
                                class="text-gray-400 hover:text-gray-600 disabled:opacity-50"
                            >
                                ↓
                            </button>
                            <button
                                @click="removeQuestion(index)"
                                type="button"
                                class="text-red-500 hover:text-red-700"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <textarea
                                v-model="question.question"
                                rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': allErrors[`questions.${index}.question`] }"
                                placeholder="Escribe tu pregunta aquí..."
                            ></textarea>
                            <InputError :message="allErrors[`questions.${index}.question`]" class="mt-1" />
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="question.is_required"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <label class="ml-2 text-sm text-gray-700">
                                Pregunta obligatoria
                            </label>
                        </div>

                        <!-- Vista previa de escala Likert -->
                        <div class="mt-3 p-3 bg-white rounded border">
                            <p class="text-xs text-gray-500 mb-2">Vista previa (Escala Likert):</p>
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

            <InputError :message="allErrors.questions" class="mt-2" />
        </div>

        <!-- Errores generales -->
        <div v-if="Object.keys(allErrors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">
                        Por favor corrige los siguientes errores:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li v-for="(error, key) in allErrors" :key="key">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-3">
            <button
                @click="$emit('cancel')"
                type="button"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Cancelar
            </button>
            <button
                @click="handleSubmit"
                :disabled="processing"
                type="button"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <span v-if="processing">Guardando...</span>
                <span v-else>{{ isEditing ? 'Actualizar' : 'Crear' }} Encuesta</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useSurveys } from '../Composables/useSurveys'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    survey: Object,
    routeName: String,
    processing: Boolean,
    errors: Object
})

const emit = defineEmits(['submit', 'cancel'])

const { likertScale, validateSurveyForm } = useSurveys()

const isEditing = ref(!!props.survey)
const clientValidationErrors = ref({})

// Combinar errores del servidor (props.errors) con errores de validación del cliente
const allErrors = computed(() => {
    return { ...props.errors, ...clientValidationErrors.value }
})

const form = ref({
    title: props.survey?.title || '',
    description: props.survey?.description || '',
    instructions: props.survey?.instructions || '',
    is_active: props.survey?.is_active ?? true,
    starts_at: props.survey?.starts_at || '',
    ends_at: props.survey?.ends_at || '',
    questions: (() => {
        const questions = props.survey?.questions
        if (Array.isArray(questions)) {
            return questions.map(q => ({
                question: q.question,
                is_required: q.is_required ?? true
            }))
        } else if (questions?.data && Array.isArray(questions.data)) {
            return questions.data.map(q => ({
                question: q.question,
                is_required: q.is_required ?? true
            }))
        }
        return []
    })()
})

const addQuestion = () => {
    form.value.questions.push({
        question: '',
        is_required: true
    })
}

const removeQuestion = (index) => {
    form.value.questions.splice(index, 1)
}

const moveQuestion = (index, direction) => {
    const newIndex = index + direction
    if (newIndex >= 0 && newIndex < form.value.questions.length) {
        const questions = [...form.value.questions]
        const [movedQuestion] = questions.splice(index, 1)
        questions.splice(newIndex, 0, movedQuestion)
        form.value.questions = questions
    }
}

// Limpiar errores de validación del cliente cuando cambian los datos
watch(form, () => {
    // Solo limpiar errores específicos si el valor ha cambiado
    if (Object.keys(clientValidationErrors.value).length > 0) {
        const newErrors = { ...clientValidationErrors.value }
        
        // Verificar cada error y eliminar si el campo ya no tiene el problema
        Object.keys(newErrors).forEach(key => {
            const currentValidation = validateSurveyForm(form.value)
            if (!currentValidation[key]) {
                delete newErrors[key]
            }
        })
        
        clientValidationErrors.value = newErrors
    }
}, { deep: true })

const handleSubmit = () => {
    const validationErrors = validateSurveyForm(form.value)
    if (Object.keys(validationErrors).length > 0) {
        // Guardar los errores de validación para mostrarlos en el template
        clientValidationErrors.value = validationErrors
        return
    }
    
    // Limpiar errores si la validación pasa
    clientValidationErrors.value = {}
    
    emit('submit', form.value)
}
</script>