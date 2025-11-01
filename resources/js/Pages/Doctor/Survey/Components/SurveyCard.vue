<!-- resources/js/Pages/Doctor/Survey/Components/SurveyCard.vue -->
<template>
    <div v-if="survey" class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ survey.title }}</h3>
                        <span 
                            v-if="survey.has_responses" 
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                            title="Esta encuesta tiene respuestas y no se puede editar o eliminar"
                        >
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Protegida
                        </span>
                    </div>
                    <p v-if="survey.description" class="text-sm text-gray-600">
                        {{ survey.description }}
                    </p>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="statusClasses"
                    >
                        {{ statusText }}
                    </span>
                    <div class="relative">
                        <button
                            ref="menuButtonRef"
                            @click="showMenu = !showMenu"
                            class="p-1 text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        <div
                            v-if="showMenu"
                            ref="menuRef"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
                        >
                            <div class="py-1">
                                <button
                                    @click="handleView"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Ver detalles
                                </button>
                                <button
                                    @click="handleEdit"
                                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                    :class="survey.has_responses ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700'"
                                    :disabled="survey.has_responses"
                                    :title="survey.has_responses ? 'No se puede editar una encuesta con respuestas' : ''"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="handleResults"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Ver resultados
                                </button>
                                <hr class="my-1">
                                <button
                                    @click="handleToggleStatus"
                                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                    :class="survey.is_active ? 'text-red-700' : 'text-green-700'"
                                >
                                    {{ survey.is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                                <button
                                    @click="handleDelete"
                                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                    :class="survey.has_responses ? 'text-gray-400 cursor-not-allowed' : 'text-red-700'"
                                    :disabled="survey.has_responses"
                                    :title="survey.has_responses ? 'No se puede eliminar una encuesta con respuestas' : ''"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ survey.questions_count || 0 }}</div>
                    <div class="text-xs text-blue-800">Preguntas</div>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ survey.responses_count || 0 }}</div>
                    <div class="text-xs text-green-800">Respuestas</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ completionRate }}%</div>
                    <div class="text-xs text-purple-800">Completadas</div>
                </div>
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-amber-600">{{ daysRemaining }}</div>
                    <div class="text-xs text-amber-800">Días restantes</div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="text-xs text-gray-500 space-y-1">
                <div class="flex justify-between">
                    <span>Creada por:</span>
                    <span>{{ survey.creator?.name || 'Usuario desconocido' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Fecha:</span>
                    <span>{{ formatDate(survey.created_at) }}</span>
                </div>
                <div v-if="survey.starts_at" class="flex justify-between">
                    <span>Inicia:</span>
                    <span>{{ formatDate(survey.starts_at) }}</span>
                </div>
                <div v-if="survey.ends_at" class="flex justify-between">
                    <span>Termina:</span>
                    <span>{{ formatDate(survey.ends_at) }}</span>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div v-if="survey.responses_count > 0" class="mt-4">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs text-gray-600">Progreso de respuestas</span>
                    <span class="text-xs text-gray-600">{{ survey.responses_count }} respuestas</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${Math.min(completionRate, 100)}%` }"
                    ></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fallback cuando no hay survey -->
    <div v-else class="bg-gray-100 rounded-lg border border-gray-200 p-6">
        <div class="text-center text-gray-500">
            <p>Error cargando encuesta</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useSurveys } from '../Composables/useSurveys'
import { messageConfirm } from '@/Hooks/useErrorsForm'

const props = defineProps({
    survey: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['toggle-status'])

const { formatDate, getSurveyStatus } = useSurveys()

const showMenu = ref(false)
const menuRef = ref(null)
const menuButtonRef = ref(null)

const statusData = computed(() => getSurveyStatus(props.survey || {}))

const statusClasses = computed(() => {
    const status = statusData.value.status
    const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'scheduled': 'bg-blue-100 text-blue-800',
        'active': 'bg-green-100 text-green-800',
        'expired': 'bg-red-100 text-red-800',
        'inactive': 'bg-yellow-100 text-yellow-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
})

const statusText = computed(() => {
    const status = statusData.value.status
    const labels = {
        'draft': 'Borrador',
        'scheduled': 'Programada',
        'active': 'Activa',
        'expired': 'Expirada',
        'inactive': 'Inactiva'
    }
    return labels[status] || 'Desconocido'
})

const completionRate = computed(() => {
    // Usar el completion_rate calculado por el servidor si está disponible
    if (props.survey?.completion_rate !== undefined) {
        return props.survey.completion_rate
    }
    
    // Fallback: calcular basado en respuestas completadas vs total de respuestas
    const totalResponses = props.survey?.responses_count || 0
    const completedResponses = props.survey?.completed_responses_count || 0
    
    if (totalResponses === 0) {
        return 0
    }
    
    return Math.round((completedResponses / totalResponses) * 100)
})

const daysRemaining = computed(() => {
    if (!props.survey?.ends_at) {
        return '∞'
    }
    
    try {
        const endDate = new Date(props.survey.ends_at)
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

const handleView = () => {
    showMenu.value = false
    if (props.survey?.id) {
        router.visit(route('doctor.surveys.show', props.survey.id))
    }
}

const handleEdit = () => {
    showMenu.value = false
    if (props.survey?.has_responses) {
        return // No hacer nada si la encuesta tiene respuestas
    }
    if (props.survey?.id) {
        router.visit(route('doctor.surveys.edit', props.survey.id))
    }
}

const handleResults = () => {
    showMenu.value = false
    if (props.survey?.id) {
        router.visit(route('doctor.surveys.show-results', props.survey.id))
    }
}

const handleToggleStatus = () => {
    showMenu.value = false
    if (props.survey?.id) {
        emit('toggle-status', props.survey.id, !props.survey.is_active)
    }
}

const handleDelete = () => {
    showMenu.value = false
    if (props.survey?.has_responses) {
        return // No hacer nada si la encuesta tiene respuestas
    }
    if (props.survey?.id && props.survey?.title) {
        messageConfirm(`Se eliminará permanentemente la encuesta "${props.survey.title}" y todas sus preguntas asociadas.`).then((res) => {
            if (res.isConfirmed) {
                router.delete(route('doctor.surveys.destroy', props.survey.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        // La notificación se maneja automáticamente por el controlador
                    }
                })
            }
        })
    }
}

// Función para manejar clics fuera del menú
const handleClickOutside = (event) => {
    if (menuRef.value && !menuRef.value.contains(event.target) && 
        menuButtonRef.value && !menuButtonRef.value.contains(event.target)) {
        showMenu.value = false
    }
}

// Configurar event listeners
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Animaciones suaves */
.transition-colors {
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

/* Estilos para botones deshabilitados */
button:disabled {
    pointer-events: none;
}

button:disabled:hover {
    background-color: transparent !important;
}
</style>