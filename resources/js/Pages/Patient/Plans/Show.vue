<template>
    <AuthenticatedLayout :title="`${planData.title || 'Plan'}`">
        <template #header>
            <CrudBanner 
                :title="planData?.title || 'Plan'"
                :description="`Plan ${getPlanTypeName(planData?.plan_type?.name)} asignado`"
            />
        </template>

        <div class="py-6 space-y-6">
            <!-- Información General del Plan -->
            <CardBox>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Estado -->
                    <div class="text-center">
                        <div class="mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                  :class="getStatusBadgeClasses(planData.status)">
                                <span class="w-2 h-2 rounded-full mr-2"
                                      :class="getStatusDotClasses(planData.status)"></span>
                                {{ getStatusLabel(planData.status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Estado</p>
                    </div>

                    <!-- Tipo -->
                    <div class="text-center">
                        <div class="mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="planData.plan_type?.name === 'alimentacion'" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    <path v-else d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                                {{ getPlanTypeName(planData.plan_type?.name) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Tipo de Plan</p>
                    </div>

                    <!-- Duración -->
                    <div class="text-center">
                        <div class="mb-2">
                            <p class="text-lg font-semibold text-gray-900">{{ planDuration }} días</p>
                        </div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Duración</p>
                    </div>

                    <!-- Progreso -->
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="flex items-center justify-center mb-1">
                                <span class="text-lg font-semibold text-gray-900">{{ planProgress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300"
                                     :class="progressBarColor"
                                     :style="{ width: planProgress + '%' }"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Progreso</p>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                        <p class="text-gray-900">{{ formatDate(planData.start_date) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Finalización</label>
                        <p class="text-gray-900">{{ formatDate(planData.end_date) }}</p>
                    </div>
                </div>

                <!-- Descripción -->
                <div v-if="planData.description" class="pt-6 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción del Plan</label>
                    <p class="text-gray-900 leading-relaxed">{{ planData.description }}</p>
                </div>
            </CardBox>

            <!-- Elementos del Plan -->
            <CardBox>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Elementos del Plan
                    </h3>
                    <span class="text-sm text-gray-500">{{ planData.elements?.length || 0 }} elemento(s)</span>
                </div>

                <div v-if="planData.elements && planData.elements.length > 0" class="space-y-6">
                    <div
                        v-for="(element, index) in planData.elements"
                        :key="element.id"
                        class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                    >
                        <!-- Header del elemento -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium mr-3">
                                    {{ index + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">
                                        {{ getElementName(element) }}
                                    </h4>
                                    <p class="text-sm text-gray-500">{{ getElementCategory(element) }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ element.frequency }}
                            </span>
                        </div>

                        <!-- Información del elemento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cantidad</label>
                                <p class="text-sm font-semibold text-gray-900">{{ element.quantity }} {{ element.unit }}</p>
                            </div>
                            <div v-if="element.intensity" class="bg-gray-50 rounded-lg p-3">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Intensidad</label>
                                <p class="text-sm font-semibold text-gray-900 capitalize">{{ element.intensity }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Horario</label>
                                <p class="text-sm font-semibold text-gray-900">{{ element.time_schedule }}</p>
                            </div>
                        </div>

                        <!-- Información nutricional o de ejercicio -->
                        <div v-if="element.food" class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 mb-4">
                            <h5 class="font-medium text-green-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                Información Nutricional
                            </h5>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                <div class="text-center p-2 bg-white rounded border">
                                    <div class="text-orange-600 font-bold">{{ element.food.calories || 0 }}</div>
                                    <div class="text-xs text-gray-600">Calorías</div>
                                </div>
                                <div class="text-center p-2 bg-white rounded border">
                                    <div class="text-red-600 font-bold">{{ element.food.protein || 0 }}g</div>
                                    <div class="text-xs text-gray-600">Proteínas</div>
                                </div>
                                <div class="text-center p-2 bg-white rounded border">
                                    <div class="text-yellow-600 font-bold">{{ element.food.carbohydrates || 0 }}g</div>
                                    <div class="text-xs text-gray-600">Carbohidratos</div>
                                </div>
                                <div class="text-center p-2 bg-white rounded border">
                                    <div class="text-purple-600 font-bold">{{ element.food.fats || 0 }}g</div>
                                    <div class="text-xs text-gray-600">Grasas</div>
                                </div>
                            </div>
                        </div>

                        <div v-if="element.exercise" class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h5 class="font-medium text-blue-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                                Detalles del Ejercicio
                            </h5>
                            
                            <!-- Descripción del ejercicio -->
                            <div v-if="element.exercise.description" class="mb-4 p-3 bg-white rounded-lg border">
                                <p class="text-gray-700">{{ element.exercise.description }}</p>
                            </div>

                            <!-- Métricas principales del ejercicio -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                                <div class="text-center p-3 bg-white rounded border">
                                    <div class="text-orange-600 font-bold">{{ element.exercise.duration_minutes || 0 }} min</div>
                                    <div class="text-xs text-gray-600">Duración</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded border">
                                    <div class="text-red-600 font-bold">{{ element.exercise.calories_burned || 0 }}</div>
                                    <div class="text-xs text-gray-600">Calorías</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded border">
                                    <div class="text-blue-600 font-bold capitalize">{{ element.exercise.intensity || 'No definido' }}</div>
                                    <div class="text-xs text-gray-600">Intensidad</div>
                                </div>
                                <div v-if="element.exercise.exercise_type" class="text-center p-3 bg-white rounded border">
                                    <div class="text-purple-600 font-bold">{{ element.exercise.exercise_type.name }}</div>
                                    <div class="text-xs text-gray-600">Tipo</div>
                                </div>
                            </div>

                            <!-- Series y repeticiones -->
                            <div v-if="element.exercise.sets || element.exercise.repetitions" class="mb-4">
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                                    <h6 class="font-medium text-indigo-800 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Estructura del Ejercicio
                                    </h6>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div v-if="element.exercise.sets" class="text-center p-2 bg-white rounded border">
                                            <div class="text-indigo-600 font-bold">{{ element.exercise.sets }}</div>
                                            <div class="text-xs text-gray-600">Series</div>
                                        </div>
                                        <div v-if="element.exercise.repetitions" class="text-center p-2 bg-white rounded border">
                                            <div class="text-indigo-600 font-bold">{{ element.exercise.repetitions }}</div>
                                            <div class="text-xs text-gray-600">Repeticiones</div>
                                        </div>
                                        <div v-if="element.exercise.rest_seconds" class="text-center p-2 bg-white rounded border">
                                            <div class="text-indigo-600 font-bold">{{ element.exercise.rest_seconds }}s</div>
                                            <div class="text-xs text-gray-600">Descanso</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Equipo necesario -->
                            <div v-if="element.exercise.equipment" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    <span class="text-yellow-800 font-medium">Equipo necesario:</span>
                                </div>
                                <p class="mt-1 ml-6 text-yellow-900">{{ element.exercise.equipment }}</p>
                            </div>

                            <!-- Contraindicaciones -->
                            <div v-if="element.exercise.contraindications" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-red-800 font-medium">Contraindicaciones:</span>
                                </div>
                                <p class="mt-1 ml-6 text-red-900">{{ element.exercise.contraindications }}</p>
                            </div>

                            <!-- Notas del ejercicio -->
                            <div v-if="element.exercise.notes" class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-800 font-medium">Notas adicionales:</span>
                                </div>
                                <p class="mt-1 ml-6 text-gray-700">{{ element.exercise.notes }}</p>
                            </div>

                            <!-- Mensaje si no requiere equipo -->
                            <div v-if="!element.exercise.equipment" class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-800 font-medium">No requiere equipo especial</span>
                                </div>
                            </div>
                        </div>

                        <!-- Instrucciones -->
                        <div v-if="element.instructions" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instrucciones</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-3">{{ element.instructions }}</p>
                        </div>

                        <!-- Notas -->
                        <div v-if="element.notes" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas Adicionales</label>
                            <p class="text-gray-600 bg-blue-50 rounded-lg p-3 text-sm">{{ element.notes }}</p>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sin elementos asignados</h3>
                    <p class="text-gray-500">Este plan aún no tiene elementos configurados.</p>
                </div>
            </CardBox>

            <!-- Botón volver -->
            <div class="flex justify-center">
                <BaseButton
                    :icon="mdiArrowLeft"
                    label="Volver a Mis Planes"
                    color="info"
                    @click="goBack"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiArrowLeft } from '@mdi/js';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    plan: Object,
});

// Extraer los datos del plan del resource
const planData = computed(() => props.plan?.data || props.plan || {});

// Métodos de utilidad
const getPlanTypeName = (typeName) => {
    if (!typeName) return 'Tipo no definido';
    return typeName === 'alimentacion' ? 'Alimentación' : 'Actividad Física';
};

const getStatusLabel = (status) => {
    const labels = {
        'activo': 'Activo',
        'completado': 'Completado',
        'cancelado': 'Cancelado'
    };
    return labels[status] || status;
};

const getStatusBadgeClasses = (status) => {
    const classes = {
        'activo': 'bg-green-100 text-green-800',
        'completado': 'bg-blue-100 text-blue-800',
        'cancelado': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusDotClasses = (status) => {
    const classes = {
        'activo': 'bg-green-400',
        'completado': 'bg-blue-400',
        'cancelado': 'bg-red-400'
    };
    return classes[status] || 'bg-gray-400';
};

const formatDate = (date) => {
    if (!date) return 'Fecha no definida';
    try {
        return new Date(date).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return 'Fecha inválida';
    }
};

const getElementName = (element) => {
    if (element.food) {
        return element.food.name;
    }
    if (element.exercise) {
        return element.exercise.name;
    }
    return 'Elemento sin nombre';
};

const getElementCategory = (element) => {
    if (element.food?.food_group) {
        return element.food.food_group.name;
    }
    if (element.exercise?.exercise_type) {
        return element.exercise.exercise_type.name;
    }
    return 'Sin categoría';
};

// Computadas
const planDuration = computed(() => {
    if (!planData.value?.start_date || !planData.value?.end_date) return 0;
    
    const start = new Date(planData.value.start_date);
    const end = new Date(planData.value.end_date);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
});

const planProgress = computed(() => {
    if (!planData.value?.start_date || !planData.value?.end_date) return 0;
    
    const now = new Date();
    const start = new Date(planData.value.start_date);
    const end = new Date(planData.value.end_date);
    
    if (now < start) return 0;
    if (now > end) return 100;
    
    const total = end - start;
    const elapsed = now - start;
    return Math.round((elapsed / total) * 100);
});

const progressBarColor = computed(() => {
    const progress = planProgress.value;
    if (planData.value?.status === 'completado') return 'bg-blue-500';
    if (planData.value?.status === 'cancelado') return 'bg-red-500';
    if (progress >= 75) return 'bg-green-500';
    if (progress >= 50) return 'bg-yellow-500';
    if (progress >= 25) return 'bg-orange-500';
    return 'bg-blue-500';
});

// Métodos
const goBack = () => {
    router.visit(route('patient.plans.index'));
};
</script>