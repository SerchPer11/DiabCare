<!-- resources/js/Pages/Patient/Survey/Components/LikertQuestion.vue -->
<template>
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <!-- Número y texto de la pregunta -->
        <div class="mb-6">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-lg font-medium text-gray-900">
                    Pregunta {{ questionNumber }}
                    <span v-if="question.is_required" class="text-red-500 ml-1">*</span>
                </h3>
                <span class="text-sm text-gray-500">
                    {{ questionNumber }}/{{ totalQuestions }}
                </span>
            </div>
            <p class="text-gray-700 leading-relaxed">{{ question.question }}</p>
        </div>

        <!-- Escala Likert -->
        <div class="mb-6">
            <p class="text-sm font-medium text-gray-700 mb-4">
                Selecciona tu respuesta (1 = Totalmente en desacuerdo, 5 = Totalmente de acuerdo):
            </p>
            
            <!-- Versión desktop/tablet -->
            <div class="hidden md:block">
                <div class="flex justify-between items-center space-x-4">
                    <label
                        v-for="option in likertScale"
                        :key="option.value"
                        class="flex-1 cursor-pointer"
                    >
                        <input
                            type="radio"
                            :name="`question_${question.id}`"
                            :value="option.value"
                            :checked="selectedValue === option.value"
                            @change="handleChange(option.value)"
                            class="sr-only"
                        />
                        <div
                            class="text-center p-4 rounded-lg border-2 transition-all duration-200"
                            :class="getOptionClasses(option.value)"
                        >
                            <div class="text-2xl font-bold mb-2">{{ option.value }}</div>
                            <div class="text-sm font-medium mb-1">{{ option.label }}</div>
                            <div class="text-xs text-gray-500">{{ option.description }}</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Versión móvil -->
            <div class="md:hidden space-y-3">
                <label
                    v-for="option in likertScale"
                    :key="option.value"
                    class="flex items-center p-4 rounded-lg border-2 cursor-pointer transition-all duration-200"
                    :class="getOptionClasses(option.value)"
                >
                    <input
                        type="radio"
                        :name="`question_${question.id}`"
                        :value="option.value"
                        :checked="selectedValue === option.value"
                        @change="handleChange(option.value)"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <div class="ml-3 flex-1">
                        <div class="flex items-center">
                            <span class="text-lg font-bold text-gray-900 mr-3">{{ option.value }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ option.description }}</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Campo de comentarios opcional -->
        <div v-if="allowComments" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Comentario adicional (opcional):
            </label>
            <textarea
                v-model="commentText"
                @input="$emit('comment-change', commentText)"
                rows="3"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                placeholder="Si deseas, puedes agregar algún comentario sobre esta pregunta..."
            ></textarea>
        </div>

        <!-- Mensaje de error -->
        <div v-if="error && question.is_required" class="mb-4">
            <p class="text-red-600 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Esta pregunta es obligatoria. Por favor selecciona una respuesta.
            </p>
        </div>

        <!-- Indicador de progreso visual -->
        <div class="flex justify-between items-center text-xs text-gray-500">
            <span v-if="question.is_required" class="flex items-center">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                Obligatoria
            </span>
            <span v-else class="flex items-center">
                <span class="w-2 h-2 bg-gray-300 rounded-full mr-1"></span>
                Opcional
            </span>
            <span v-if="selectedValue">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-1 inline-block"></span>
                Respondida
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useSurveyResponse } from '../Composables/useSurveyResponse'

const props = defineProps({
    question: {
        type: Object,
        required: true
    },
    questionNumber: {
        type: Number,
        required: true
    },
    totalQuestions: {
        type: Number,
        required: true
    },
    modelValue: {
        type: [Number, String],
        default: null
    },
    comment: {
        type: String,
        default: ''
    },
    error: {
        type: Boolean,
        default: false
    },
    allowComments: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'comment-change'])

const { likertScale } = useSurveyResponse()

const selectedValue = ref(props.modelValue)
const commentText = ref(props.comment)

// Actualizar valor seleccionado cuando cambia el prop
watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue
})

// Actualizar comentario cuando cambia el prop
watch(() => props.comment, (newValue) => {
    commentText.value = newValue
})

const getOptionClasses = (value) => {
    const isSelected = selectedValue.value === value
    const option = likertScale.find(opt => opt.value === value)
    
    if (isSelected) {
        return `${option.borderColor} ${option.bgColor} shadow-md transform scale-105`
    }
    
    return 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm'
}

const handleChange = (value) => {
    selectedValue.value = value
    emit('update:modelValue', value)
}
</script>

<style scoped>
/* Animaciones suaves para las transiciones */
.transition-all {
    transition: all 0.2s ease-in-out;
}

/* Efecto hover más suave */
@media (hover: hover) {
    .cursor-pointer:hover {
        transform: translateY(-1px);
    }
}

/* Asegurar que el input radio esté oculto en la versión desktop */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>