// resources/js/Pages/Doctor/Survey/Composables/useSurveys.js
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useSurveys() {
    const processing = ref(false)
    const errors = ref({})

    const likertScale = [
        { value: 1, label: 'Totalmente en desacuerdo', color: 'bg-red-100 text-red-700 border border-red-200' },
        { value: 2, label: 'En desacuerdo', color: 'bg-orange-100 text-orange-700 border border-orange-200' },
        { value: 3, label: 'Neutral', color: 'bg-gray-100 text-gray-700 border border-gray-200' },
        { value: 4, label: 'De acuerdo', color: 'bg-blue-100 text-blue-700 border border-blue-200' },
        { value: 5, label: 'Totalmente de acuerdo', color: 'bg-green-100 text-green-700 border border-green-200' },
    ]

    const createSurvey = (data, routeName) => {
        processing.value = true
        errors.value = {}

        router.post(route(routeName + 'store'), data, {
            onFinish: () => processing.value = false,
            onError: (errorData) => errors.value = errorData
        })
    }

    const updateSurvey = (survey, data, routeName) => {
        processing.value = true
        errors.value = {}

        router.put(route(routeName + 'update', survey.id), data, {
            onFinish: () => processing.value = false,
            onError: (errorData) => errors.value = errorData
        })
    }

    const deleteSurvey = (survey, routeName) => {
        if (confirm('¿Estás seguro de que deseas eliminar esta encuesta?')) {
            router.delete(route(routeName + 'destroy', survey.id))
        }
    }

    const formatDate = (dateString) => {
        if (!dateString) return 'Fecha no disponible'
        
        try {
            const date = new Date(dateString)
            // Verificar si la fecha es válida
            if (isNaN(date.getTime())) {
                return 'Fecha no disponible'
            }
            
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })
        } catch (error) {
            return 'Fecha no disponible'
        }
    }

    const getSurveyStatus = (survey) => {
        // Validar que survey existe y tiene las propiedades necesarias
        if (!survey || typeof survey !== 'object') {
            return { 
                status: 'unknown',
                text: 'Desconocido', 
                class: 'bg-gray-100 text-gray-800' 
            }
        }

        const now = new Date()
        const startsAt = survey.starts_at ? new Date(survey.starts_at) : null
        const endsAt = survey.ends_at ? new Date(survey.ends_at) : null

        if (!survey.is_active) {
            return { 
                status: 'inactive',
                text: 'Inactiva', 
                class: 'bg-gray-100 text-gray-800' 
            }
        }

        if (startsAt && now < startsAt) {
            return { 
                status: 'scheduled',
                text: 'Programada', 
                class: 'bg-blue-100 text-blue-800' 
            }
        }

        if (endsAt && now > endsAt) {
            return { 
                status: 'expired',
                text: 'Vencida', 
                class: 'bg-red-100 text-red-800' 
            }
        }

        return { 
            status: 'active',
            text: 'Activa', 
            class: 'bg-green-100 text-green-800' 
        }
    }

    const validateSurveyForm = (formData) => {
        const errors = {}

        if (!formData.title?.trim()) {
            errors.title = 'El título es obligatorio'
        }

        if (!formData.description?.trim()) {
            errors.description = 'La descripción es obligatoria'
        }

        if (!formData.instructions?.trim()) {
            errors.instructions = 'Las instrucciones para los pacientes son obligatorias'
        }

        if (!formData.starts_at) {
            errors.starts_at = 'La fecha de inicio es obligatoria'
        } else {
            // Validar que la fecha de inicio sea hoy o posterior
            // Usar formato YYYY-MM-DD para comparación directa de strings
            const today = new Date()
            const todayString = today.toISOString().split('T')[0] // Formato YYYY-MM-DD
            
            if (formData.starts_at < todayString) {
                errors.starts_at = 'La fecha de inicio debe ser hoy o posterior'
            }
        }

        if (!formData.ends_at) {
            errors.ends_at = 'La fecha de fin es obligatoria'
        } else if (formData.starts_at && formData.ends_at) {
            if (formData.starts_at >= formData.ends_at) {
                errors.ends_at = 'La fecha de fin debe ser posterior a la fecha de inicio'
            }
        }

        if (!formData.questions || formData.questions.length === 0) {
            errors.questions = 'Debe agregar al menos una pregunta'
        } else {
            if (formData.questions.length > 50) {
                errors.questions = 'No puede tener más de 50 preguntas'
            }
            
            formData.questions.forEach((question, index) => {
                if (!question.question?.trim()) {
                    errors[`questions.${index}.question`] = 'El texto de la pregunta es obligatorio'
                }
            })
        }

        return errors
    }

    return {
        processing,
        errors,
        likertScale,
        createSurvey,
        updateSurvey,
        deleteSurvey,
        formatDate,
        getSurveyStatus,
        validateSurveyForm
    }
}