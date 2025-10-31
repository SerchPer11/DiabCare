// resources/js/Pages/Doctor/Survey/Composables/useSurveys.js
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useSurveys() {
    const processing = ref(false)
    const errors = ref({})

    const likertScale = [
        { value: 1, label: 'Totalmente en desacuerdo', color: 'bg-red-500' },
        { value: 2, label: 'En desacuerdo', color: 'bg-orange-500' },
        { value: 3, label: 'Neutral', color: 'bg-yellow-500' },
        { value: 4, label: 'De acuerdo', color: 'bg-blue-500' },
        { value: 5, label: 'Totalmente de acuerdo', color: 'bg-green-500' },
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

        if (!formData.questions || formData.questions.length === 0) {
            errors.questions = 'Debe agregar al menos una pregunta'
        } else {
            formData.questions.forEach((question, index) => {
                if (!question.question?.trim()) {
                    errors[`questions.${index}.question`] = 'El texto de la pregunta es obligatorio'
                }
            })
        }

        if (formData.starts_at && formData.ends_at) {
            if (new Date(formData.starts_at) >= new Date(formData.ends_at)) {
                errors.ends_at = 'La fecha de fin debe ser posterior a la fecha de inicio'
            }
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