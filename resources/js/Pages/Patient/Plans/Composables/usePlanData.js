// resources/js/Pages/Patient/Plans/Composables/usePlanData.js
import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function usePlanData(initialPlans = null, initialFilters = {}) {
    // Estado reactivo para filtros
    const filters = reactive({
        status: initialFilters?.status || '',
        plan_type_id: initialFilters?.plan_type_id || '',
    })

    // Estado de carga
    const isLoading = reactive({
        search: false,
        view: false
    })

    // Opciones para filtros
    const statusOptions = [
        { value: 'activo', label: 'Activo' },
        { value: 'completado', label: 'Completado' }, 
        { value: 'cancelado', label: 'Cancelado' }
    ]

    const planTypeOptions = [
        { value: 1, label: 'Alimentación' },
        { value: 2, label: 'Actividad Física' }
    ]

    // Computadas para verificar estado de los planes
    const hasPlans = computed(() => {
        return initialPlans?.data && Array.isArray(initialPlans.data) && initialPlans.data.length > 0
    })

    const hasActiveFilters = computed(() => {
        return Object.keys(filters).some(key => filters[key] && filters[key] !== '')
    })

    const emptyStateMessage = computed(() => {
        if (hasActiveFilters.value) {
            return 'No se encontraron planes con los filtros aplicados.'
        }
        return 'Tu médico aún no te ha asignado planes de alimentación o actividad física.'
    })

    // Métodos para interactuar con los planes
    const searchPlans = () => {
        isLoading.search = true
        router.get(route('patient.plans.index'), filters, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                isLoading.search = false
            }
        })
    }

    const clearFilters = () => {
        // Limpiar filtros
        Object.keys(filters).forEach(key => {
            filters[key] = ''
        })
        
        isLoading.search = true
        router.get(route('patient.plans.index'), {}, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                isLoading.search = false
            }
        })
    }

    const viewPlan = (planId) => {
        isLoading.view = true
        router.visit(route('patient.plans.show', planId), {
            onFinish: () => {
                isLoading.view = false
            }
        })
    }

    // Métodos de utilidad para planes
    const getPlanTypeName = (typeName) => {
        if (!typeName) return 'Tipo no definido'
        return typeName === 'alimentacion' ? 'Alimentación' : 'Actividad Física'
    }

    const getPlanTypeClasses = (typeName) => {
        if (!typeName) return 'bg-gray-100 text-gray-800'
        return typeName === 'alimentacion' 
            ? 'bg-green-100 text-green-800'
            : 'bg-blue-100 text-blue-800'
    }

    const getStatusLabel = (status) => {
        const labels = {
            'activo': 'Activo',
            'completado': 'Completado',
            'cancelado': 'Cancelado'
        }
        return labels[status] || status
    }

    const getStatusClasses = (status) => {
        const classes = {
            'activo': 'bg-green-100 text-green-700',
            'completado': 'bg-blue-100 text-blue-700',
            'cancelado': 'bg-red-100 text-red-700'
        }
        return classes[status] || 'bg-gray-100 text-gray-700'
    }

    const getStatusDotClasses = (status) => {
        const classes = {
            'activo': 'bg-green-400',
            'completado': 'bg-blue-400',
            'cancelado': 'bg-red-400'
        }
        return classes[status] || 'bg-gray-400'
    }

    const calculateProgress = (startDate, endDate, status) => {
        if (status === 'completado') return 100
        if (status === 'cancelado') return 0
        
        const now = new Date()
        const start = new Date(startDate)
        const end = new Date(endDate)
        
        if (now < start) return 0
        if (now > end) return 100
        
        const total = end - start
        const elapsed = now - start
        return Math.round((elapsed / total) * 100)
    }

    const getProgressBarColor = (progress, status) => {
        if (status === 'completado') return 'bg-blue-500'
        if (status === 'cancelado') return 'bg-red-500'
        if (progress >= 75) return 'bg-green-500'
        if (progress >= 50) return 'bg-yellow-500'
        if (progress >= 25) return 'bg-orange-500'
        return 'bg-blue-500'
    }

    const formatDateRange = (startDate, endDate) => {
        const start = new Date(startDate).toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'short'
        })
        const end = new Date(endDate).toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'short'
        })
        return `${start} - ${end}`
    }

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }

    const getDaysRemaining = (endDate, status) => {
        if (status === 'completado') return 'Completado'
        if (status === 'cancelado') return 'Cancelado'
        
        const now = new Date()
        const end = new Date(endDate)
        
        if (now > end) return 'Finalizado'
        
        const diffTime = end - now
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
        
        if (diffDays === 0) return 'Último día'
        if (diffDays === 1) return '1 día restante'
        return `${diffDays} días restantes`
    }

    const getTimeStatus = (startDate, endDate) => {
        const now = new Date()
        const start = new Date(startDate)
        const end = new Date(endDate)
        
        if (now < start) {
            const diffTime = start - now
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
            return diffDays === 1 ? 'Comienza mañana' : `Comienza en ${diffDays} días`
        }
        
        if (now > end) return 'Plan finalizado'
        
        return 'En progreso'
    }

    // Debug helpers
    const debugInfo = computed(() => {
        return {
            hasPlans: hasPlans.value,
            plansCount: initialPlans?.data?.length || 0,
            hasActiveFilters: hasActiveFilters.value,
            filters: { ...filters }
        }
    })

    return {
        // Estado
        filters,
        isLoading,
        
        // Opciones
        statusOptions,
        planTypeOptions,
        
        // Computadas
        hasPlans,
        hasActiveFilters,
        emptyStateMessage,
        debugInfo,
        
        // Métodos de navegación
        searchPlans,
        clearFilters,
        viewPlan,
        
        // Métodos de utilidad
        getPlanTypeName,
        getPlanTypeClasses,
        getStatusLabel,
        getStatusClasses,
        getStatusDotClasses,
        calculateProgress,
        getProgressBarColor,
        formatDateRange,
        formatDate,
        getDaysRemaining,
        getTimeStatus
    }
}