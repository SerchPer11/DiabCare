<template>
  <div class="space-y-6">
    <!-- Resumen de estadísticas -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Respuestas</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-blue-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ totalResponses }}</div>
            <div class="text-sm text-blue-800">Total Respuestas</div>
          </div>
          <div class="bg-green-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ completedResponses }}</div>
            <div class="text-sm text-green-800">Completadas</div>
          </div>
          <div class="bg-purple-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-purple-600">{{ averageScore.toFixed(1) }}</div>
            <div class="text-sm text-purple-800">Puntuación Promedio</div>
          </div>
          <div class="bg-amber-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ completionRate }}%</div>
            <div class="text-sm text-amber-800">Tasa Completitud</div>
          </div>
        </div>
      </div>
    </div>
    <!-- Resultados por pregunta -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Análisis por Pregunta</h3>
        
        <div v-if="!processedQuestionResults || processedQuestionResults.length === 0" class="text-center py-8 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay datos de preguntas</h3>
          <p class="mt-1 text-sm text-gray-500">No se encontraron estadísticas para las preguntas de esta encuesta.</p>
        </div>
        
        <div v-else class="space-y-8">
          <div
            v-for="(question, index) in processedQuestionResults"
            :key="question.id"
            class="border-b border-gray-200 pb-6 last:border-b-0"
          >
            <div class="mb-4">
              <h4 class="text-base font-medium text-gray-900 mb-2">
                Pregunta {{ index + 1 }}: {{ question.question }}
              </h4>
              <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span>{{ question.totalAnswers || 0 }} respuestas</span>
                <span>Promedio: {{ (question.average || 0).toFixed(2) }}</span>
                <span 
                  class="px-2 py-1 rounded-full text-xs"
                  :class="getScoreColor(question.average || 0)"
                >
                  {{ getScoreLabel(question.average || 0) }}
                </span>
              </div>
            </div>

            <!-- Gráfico de barras horizontales -->
            <div v-if="question.distribution" class="space-y-2">
              <div
                v-for="value in [1, 2, 3, 4, 5]"
                :key="value"
                class="flex items-center space-x-3"
              >
                <div class="w-20 text-sm text-gray-600">
                  {{ value }} - {{ getLikertLabel(value) }}
                </div>
                <div class="flex-1 bg-gray-200 rounded-full h-6 relative">
                  <div
                    class="h-6 rounded-full transition-all duration-300"
                    :class="getLikertBgColor(value)"
                    :style="{ width: `${getPercentage(question, value)}%` }"
                  ></div>
                  <span
                    v-if="getCount(question, value) > 0"
                    class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white"
                  >
                    {{ getCount(question, value) }}
                  </span>
                </div>
                <div class="w-12 text-sm text-gray-600 text-right">
                  {{ getPercentage(question, value).toFixed(0) }}%
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Respuestas individuales -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Respuestas Individuales</h3>
          <div class="flex items-center space-x-2">
            <select
              v-model="sortBy"
              class="border border-gray-300 rounded px-3 py-1 text-sm"
            >
              <option value="date">Ordenar por fecha</option>
              <option value="patient">Ordenar por paciente</option>
              <option value="score">Ordenar por puntuación</option>
            </select>
            <select
              v-model="filterBy"
              class="border border-gray-300 rounded px-3 py-1 text-sm"
            >
              <option value="all">Todas las respuestas</option>
              <option value="completed">Solo completadas</option>
              <option value="incomplete">Solo incompletas</option>
            </select>
          </div>
        </div>

        <div v-if="filteredResponses.length === 0" class="text-center py-8 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay respuestas</h3>
          <p class="mt-1 text-sm text-gray-500">Aún no se han registrado respuestas para esta encuesta.</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="response in paginatedResponses"
            :key="response.id"
            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <h4 class="font-medium text-gray-900">{{ response.user?.name || 'Paciente' }}</h4>
                <p class="text-sm text-gray-600">{{ formatDate(response.created_at) }}</p>
              </div>
              <div class="text-right">
                <div class="text-lg font-bold text-gray-900">
                  {{ calculateResponseScore(response) }}/5
                </div>
                <div class="text-xs text-gray-500">
                  {{ getRequiredAnswersCount(response) }}/{{ getRequiredQuestionsCount() }} obligatorias
                  ({{ response.answers?.length || 0 }} total)
                </div>
              </div>
            </div>
            
            <button
              @click="toggleResponseDetail(response.id)"
              class="text-sm text-blue-600 hover:text-blue-800"
            >
              {{ showResponseDetail[response.id] ? 'Ocultar' : 'Ver' }} detalles
            </button>

            <div v-if="showResponseDetail[response.id] && response.answers" class="mt-3 space-y-2">
              <div
                v-for="answer in response.answers"
                :key="answer.id || answer.survey_question_id"
                class="flex justify-between items-center text-sm"
              >
                <span class="text-gray-600">Pregunta {{ answer.survey_question_id }}:</span>
                <span
                  class="px-2 py-1 rounded-full text-xs font-medium"
                  :class="getLikertColor(answer.likert_value || answer.answer)"
                >
                  {{ answer.likert_value || answer.answer }} - {{ getLikertLabel(answer.likert_value || answer.answer) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Paginación simple -->
          <div v-if="filteredResponses.length > responsesPerPage" class="flex justify-center mt-6">
            <div class="flex space-x-2">
              <button
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
              >
                Anterior
              </button>
              <span class="px-3 py-1 text-sm text-gray-600">
                Página {{ currentPage }} de {{ totalPages }}
              </span>
              <button
                @click="currentPage++"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
              >
                Siguiente
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
// import { useSurveys } from '../Composables/useSurveys'

const props = defineProps({
  survey: {
    type: Object,
    required: true
  },
  responses: {
    type: Array,
    default: () => []
  },
  questionResults: {
    type: Array,
    default: () => []
  }
})

// const { formatDate } = useSurveys()
const formatDate = (date) => {
  if (!date) return 'Fecha no disponible'
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Estados reactivos
const showResponseDetail = ref({})
const sortBy = ref('date')
const filterBy = ref('all')
const currentPage = ref(1)
const responsesPerPage = 10

// Computadas para estadísticas
const totalResponses = computed(() => props.responses?.length || 0)

// Función para obtener las preguntas del survey
const getSurveyQuestions = () => {
  if (props.survey?.questions) {
    // Si questions es un array directamente
    if (Array.isArray(props.survey.questions)) {
      return props.survey.questions
    }
    // Si questions tiene una propiedad data (Resource)
    if (props.survey.questions.data && Array.isArray(props.survey.questions.data)) {
      return props.survey.questions.data
    }
    // Si questions es un objeto con propiedades numéricas (object con índices)
    if (typeof props.survey.questions === 'object' && !Array.isArray(props.survey.questions)) {
      const values = Object.values(props.survey.questions)
      if (values.length > 0 && typeof values[0] === 'object' && values[0].id) {
        return values
      }
    }
  }
  return []
}

// Función para contar solo preguntas obligatorias
const getRequiredQuestionsCount = () => {
  const questions = getSurveyQuestions()
  if (questions && questions.length > 0) {
    return questions.filter(q => q.is_required !== false && q.is_required !== 0).length
  }
  if (props.questionResults && Array.isArray(props.questionResults) && props.questionResults.length > 0) {
    return props.questionResults.filter(q => q.is_required !== false && q.is_required !== 0).length
  }
  return props.questionResults?.length || questions?.length || 0
}

const completedResponses = computed(() => {
  if (!props.responses?.length) return 0
  const requiredQuestionsCount = getRequiredQuestionsCount()
  
  return props.responses.filter(response => {
    if (!response.answers?.length) return false
    
    // Contar solo respuestas a preguntas obligatorias
    let requiredAnswersCount = 0
    response.answers.forEach(answer => {
      // Si tenemos las preguntas del survey, verificar si es obligatoria
      const questions = getSurveyQuestions()
      if (questions.length > 0) {
        const question = questions.find(q => q.id === answer.survey_question_id)
        if (question && (question.is_required !== false && question.is_required !== 0)) {
          requiredAnswersCount++
        }
      } else {
        // Si no tenemos las preguntas, asumir que todas son obligatorias
        requiredAnswersCount++
      }
    })
    
    return requiredAnswersCount >= requiredQuestionsCount
  }).length
})

const averageScore = computed(() => {
  if (!props.responses?.length) return 0
  
  let totalScore = 0
  let totalAnswers = 0
  
  props.responses.forEach(response => {
    if (response.answers?.length) {
      response.answers.forEach(answer => {
        totalScore += (answer.likert_value || answer.answer || 0)
        totalAnswers++
      })
    }
  })
  
  return totalAnswers > 0 ? totalScore / totalAnswers : 0
})

const completionRate = computed(() => {
  if (totalResponses.value === 0) return 0
  return Math.round((completedResponses.value / totalResponses.value) * 100)
})

// Procesar resultados de preguntas: calcularlos siempre desde las respuestas para asegurar precisión
const processedQuestionResults = computed(() => {
  const questions = getSurveyQuestions()
  if (!questions.length) {
    return []
  }
  
  return questions.map(question => {
    // Encontrar todas las respuestas para esta pregunta específica
    const answers = []
    
    if (props.responses && props.responses.length > 0) {
      props.responses.forEach(response => {
        if (response.answers && Array.isArray(response.answers)) {
          const answer = response.answers.find(a => a.survey_question_id === question.id)
          if (answer && (answer.likert_value !== null && answer.likert_value !== undefined)) {
            answers.push(answer)
          }
        }
      })
    }
    
    // Calcular distribución por valor Likert
    const distribution = {}
    for (let i = 1; i <= 5; i++) {
      distribution[i] = answers.filter(a => a.likert_value === i).length
    }
    
    // Calcular promedio
    const average = answers.length > 0
      ? answers.reduce((sum, a) => sum + (a.likert_value || 0), 0) / answers.length
      : 0
    
    return {
      id: question.id,
      question: question.question,
      is_required: question.is_required ?? true,
      order: question.order ?? 0,
      totalAnswers: answers.length,
      average: Math.round(average * 100) / 100,
      distribution: distribution
    }
  })
})

// Filtrado y ordenamiento de respuestas
const filteredResponses = computed(() => {
  let filtered = [...(props.responses || [])]

  // Aplicar filtro
  if (filterBy.value === 'completed') {
    const requiredQuestionsCount = getRequiredQuestionsCount()
    filtered = filtered.filter(response => {
      if (!response.answers?.length) return false
      
      let requiredAnswersCount = 0
      response.answers.forEach(answer => {
        const questions = getSurveyQuestions()
        if (questions.length > 0) {
          const question = questions.find(q => q.id === answer.survey_question_id)
          if (question && (question.is_required !== false && question.is_required !== 0)) {
            requiredAnswersCount++
          }
        } else {
          requiredAnswersCount++
        }
      })
      
      return requiredAnswersCount >= requiredQuestionsCount
    })
  } else if (filterBy.value === 'incomplete') {
    const requiredQuestionsCount = getRequiredQuestionsCount()
    filtered = filtered.filter(response => {
      if (!response.answers?.length) return true
      
      let requiredAnswersCount = 0
      response.answers.forEach(answer => {
        const questions = getSurveyQuestions()
        if (questions.length > 0) {
          const question = questions.find(q => q.id === answer.survey_question_id)
          if (question && (question.is_required !== false && question.is_required !== 0)) {
            requiredAnswersCount++
          }
        } else {
          requiredAnswersCount++
        }
      })
      
      return requiredAnswersCount < requiredQuestionsCount
    })
  }

  // Aplicar ordenamiento
  if (sortBy.value === 'date') {
    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  } else if (sortBy.value === 'patient') {
    filtered.sort((a, b) => {
      const aName = a.user?.name || 'Paciente'
      const bName = b.user?.name || 'Paciente'
      return aName.localeCompare(bName)
    })
  } else if (sortBy.value === 'score') {
    filtered.sort((a, b) => parseFloat(calculateResponseScore(b)) - parseFloat(calculateResponseScore(a)))
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredResponses.value.length / responsesPerPage))

const paginatedResponses = computed(() => {
  const start = (currentPage.value - 1) * responsesPerPage
  const end = start + responsesPerPage
  return filteredResponses.value.slice(start, end)
})

// Métodos auxiliares
const getCount = (question, value) => {
  return question.distribution?.[value] || 0
}

const getPercentage = (question, value) => {
  const totalAnswers = question.totalAnswers || 0
  if (totalAnswers === 0) return 0
  return (getCount(question, value) / totalAnswers) * 100
}

const getScoreColor = (score) => {
  if (score >= 4.5) return 'bg-green-100 text-green-800'
  if (score >= 3.5) return 'bg-blue-100 text-blue-800'
  if (score >= 2.5) return 'bg-yellow-100 text-yellow-800'
  if (score >= 1.5) return 'bg-orange-100 text-orange-800'
  return 'bg-red-100 text-red-800'
}

const getScoreLabel = (score) => {
  if (score >= 4.5) return 'Excelente'
  if (score >= 3.5) return 'Bueno'
  if (score >= 2.5) return 'Regular'
  if (score >= 1.5) return 'Deficiente'
  return 'Muy Deficiente'
}

const getLikertColor = (value) => {
  const colors = {
    1: 'bg-red-100 text-red-800',
    2: 'bg-orange-100 text-orange-800',
    3: 'bg-yellow-100 text-yellow-800',
    4: 'bg-blue-100 text-blue-800',
    5: 'bg-green-100 text-green-800'
  }
  return colors[value] || 'bg-gray-100 text-gray-800'
}

const getLikertLabel = (value) => {
  const labels = {
    1: 'Muy en desacuerdo',
    2: 'En desacuerdo',
    3: 'Neutral',
    4: 'De acuerdo',
    5: 'Muy de acuerdo'
  }
  return labels[value] || 'N/A'
}

const getLikertBgColor = (value) => {
  const colors = {
    1: 'bg-red-500',
    2: 'bg-orange-500',
    3: 'bg-yellow-500',
    4: 'bg-blue-500',
    5: 'bg-green-500'
  }
  return colors[value] || 'bg-gray-400'
}

const calculateResponseScore = (response) => {
  if (!response.answers?.length) return 0
  const sum = response.answers.reduce((acc, answer) => acc + (answer.likert_value || answer.answer || 0), 0)
  return (sum / response.answers.length).toFixed(1)
}

const toggleResponseDetail = (responseId) => {
  showResponseDetail.value[responseId] = !showResponseDetail.value[responseId]
}

// Función para contar respuestas a preguntas obligatorias de una respuesta específica
const getRequiredAnswersCount = (response) => {
  if (!response.answers?.length) return 0
  
  let requiredAnswersCount = 0
  response.answers.forEach(answer => {
    const questions = getSurveyQuestions()
    if (questions.length > 0) {
      const question = questions.find(q => q.id === answer.survey_question_id)
      if (question && (question.is_required !== false && question.is_required !== 0)) {
        requiredAnswersCount++
      }
    } else {
      requiredAnswersCount++
    }
  })
  
  return requiredAnswersCount
}
</script>
