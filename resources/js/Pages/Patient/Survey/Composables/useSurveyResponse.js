// resources/js/Pages/Patient/Survey/Composables/useSurveyResponse.js
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useSurveyResponse() {
    const processing = ref(false)
    const errors = ref({})
    const currentQuestion = ref(0)

    const likertScale = [
        { value: 1, label: 'Totalmente en desacuerdo', color: 'bg-red-100 text-red-800', emoji: '😞' },
        { value: 2, label: 'En desacuerdo', color: 'bg-orange-100 text-orange-800', emoji: '😕' },
        { value: 3, label: 'Neutral', color: 'bg-yellow-100 text-yellow-800', emoji: '😐' },
        { value: 4, label: 'De acuerdo', color: 'bg-blue-100 text-blue-800', emoji: '🙂' },
        { value: 5, label: 'Totalmente de acuerdo', color: 'bg-green-100 text-green-800', emoji: '😊' },
    ]

    const initializeAnswers = (questions) => {
        return questions.map(question => ({
            survey_question_id: question.id,
            likert_value: null,
            comment: ''
        }))
    }

    const submitResponse = (survey, answers, routeName) => {
        processing.value = true
        errors.value = {}

        // Filtrar solo respuestas con valor seleccionado
        const validAnswers = answers.filter(answer => answer.likert_value !== null)

        if (validAnswers.length === 0) {
            errors.value = { answers: 'Debe responder al menos una pregunta' }
            processing.value = false
            return
        }

        router.post(route(routeName + 'submit', survey.id), {
            answers: validAnswers
        }, {
            onFinish: () => processing.value = false,
            onError: (errorData) => errors.value = errorData
        })
    }

    const getAnswerColor = (value) => {
        const scale = likertScale.find(s => s.value === value)
        return scale ? scale.color : 'bg-gray-100 text-gray-800'
    }

    const getAnswerEmoji = (value) => {
        const scale = likertScale.find(s => s.value === value)
        return scale ? scale.emoji : '❓'
    }

    const calculateProgress = (answers, totalQuestions) => {
        const answeredCount = answers.filter(a => a.likert_value !== null).length
        return Math.round((answeredCount / totalQuestions) * 100)
    }

    const validateAnswers = (answers, questions) => {
        const errors = {}
        const requiredQuestions = questions.filter(q => q.is_required)
        
        requiredQuestions.forEach((question, index) => {
            const answer = answers.find(a => a.survey_question_id === question.id)
            if (!answer || answer.likert_value === null) {
                errors[`answers.${index}.likert_value`] = 'Esta pregunta es obligatoria'
            }
        })

        return errors
    }

    const nextQuestion = (totalQuestions) => {
        if (currentQuestion.value < totalQuestions - 1) {
            currentQuestion.value++
        }
    }

    const previousQuestion = () => {
        if (currentQuestion.value > 0) {
            currentQuestion.value--
        }
    }

    const goToQuestion = (index) => {
        currentQuestion.value = index
    }

    const isAvailable = (survey) => {
        const now = new Date()
        
        // Verificar si está activa
        if (!survey.is_active) return false
        
        // Verificar fecha de inicio
        if (survey.starts_at && new Date(survey.starts_at) > now) return false
        
        // Verificar fecha de fin
        if (survey.ends_at && new Date(survey.ends_at) < now) return false
        
        return true
    }

    const formatDate = (dateString) => {
        if (!dateString) return ''
        
        const date = new Date(dateString)
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }

    return {
        processing,
        errors,
        currentQuestion,
        likertScale,
        initializeAnswers,
        submitResponse,
        getAnswerColor,
        getAnswerEmoji,
        calculateProgress,
        validateAnswers,
        nextQuestion,
        previousQuestion,
        goToQuestion,
        isAvailable,
        formatDate
    }
}