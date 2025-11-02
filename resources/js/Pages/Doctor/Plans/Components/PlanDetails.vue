<template>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Información Básica -->
            <CardBox>
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Información del Plan</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          :class="getStatusClass(plan.status)">
                        {{ plan.status.charAt(0).toUpperCase() + plan.status.slice(1) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Título</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ plan.title }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo de Plan</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getPlanTypeClass(plan.plan_type.name)">
                                {{ plan.plan_type.name === 'alimentacion' ? 'Alimentación' : 'Actividad Física' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha de Inicio</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(plan.start_date) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha de Finalización</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(plan.end_date) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vigencia</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getVigencyClass()">
                                {{ getVigencyStatus() }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Progreso</dt>
                        <dd class="mt-1">
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                         :style="{ width: plan.progress_percentage + '%' }"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ plan.progress_percentage }}%
                                </span>
                            </div>
                        </dd>
                    </div>
                </div>

                <div v-if="plan.description" class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                    <dd class="mt-2 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                        {{ plan.description }}
                    </dd>
                </div>
            </CardBox>

            <!-- Elementos del Plan -->
            <CardBox>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Elementos del Plan ({{ plan.elements.length }})
                    </h2>
                </div>

                <div v-if="plan.elements.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Sin elementos</h3>
                    <p class="mt-2">Este plan no tiene elementos configurados.</p>
                </div>

                <div v-else class="space-y-4">
                    <CardBox
                        v-for="(element, index) in plan.elements"
                        :key="element.id"
                        class="p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ index + 1 }}</span>
                                </div>
                                <h3 class="ml-3 text-base font-medium text-gray-900">{{ element.name }}</h3>
                            </div>
                        </div>

                        <div class="ml-11">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cantidad</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ element.quantity }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Horario</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ element.time_schedule }}</dd>
                                </div>
                            </div>

                            <div v-if="element.instructions">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Instrucciones</dt>
                                <dd class="mt-1 text-sm text-gray-700 bg-gray-50 rounded-md p-3">
                                    {{ element.instructions }}
                                </dd>
                            </div>
                        </div>
                    </CardBox>
                </div>
            </CardBox>
        </div>

        <!-- Panel Lateral -->
        <div class="space-y-6">
            <!-- Información del Paciente -->
            <CardBox>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Paciente</h3>
                
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-lg font-medium text-blue-600">
                            {{ plan.patient.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ plan.patient.name }}</p>
                        <p class="text-sm text-gray-500">{{ plan.patient.email }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-if="plan.patient.phone">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ plan.patient.phone }}</dd>
                    </div>

                    <div v-if="plan.patient.birth_date">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Fecha de Nacimiento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(plan.patient.birth_date) }}</dd>
                    </div>
                </div>
            </CardBox>

            <!-- Estadísticas del Plan -->
            <CardBox>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Estadísticas</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Elementos totales</span>
                            <span class="font-medium text-gray-900">{{ plan.elements.length }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Días de duración</span>
                            <span class="font-medium text-gray-900">{{ getDurationDays() }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Días restantes</span>
                            <span class="font-medium text-gray-900">{{ getRemainingDays() }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Creado</span>
                            <span class="font-medium text-gray-900">{{ formatDate(plan.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </CardBox>

            <!-- Acciones -->
            <CardBox>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones</h3>
                
                <div class="space-y-3">
                    <BaseButton
                        color="info"
                        :icon="mdiPencil"
                        label="Editar Plan"
                        :routeName="`${routeName}edit`"
                        :parameter="plan.id"
                        class="w-full"
                    />

                    <BaseButton
                        color="light"
                        :icon="mdiContentDuplicate"
                        label="Duplicar Plan"
                        @click="duplicatePlan"
                        class="w-full"
                    />

                    <BaseButton
                        color="danger"
                        :icon="mdiDelete"
                        label="Eliminar Plan"
                        @click="confirmDelete"
                        class="w-full"
                    />
                </div>
            </CardBox>
        </div>
    </div>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiContentDuplicate } from '@mdi/js';
import { usePlan } from '../Composables/usePlan';

const props = defineProps({
    plan: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'doctor.plans.'
    }
});

const { destroyForm, duplicateForm } = usePlan(props);

// Métodos de utilidad
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getVigencyStatus = () => {
    const now = new Date();
    const start = new Date(props.plan.start_date);
    const end = new Date(props.plan.end_date);
    
    if (now < start) return 'Por iniciar';
    if (now > end) return 'Vencido';
    return 'Vigente';
};

const getVigencyClass = () => {
    const status = getVigencyStatus();
    const classes = {
        'Por iniciar': 'bg-yellow-100 text-yellow-800',
        'Vigente': 'bg-green-100 text-green-800',
        'Vencido': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getPlanTypeClass = (type) => {
    return type === 'alimentacion' 
        ? 'bg-green-100 text-green-800'
        : 'bg-blue-100 text-blue-800';
};

const getStatusClass = (status) => {
    const classes = {
        'activo': 'bg-green-100 text-green-800',
        'finalizado': 'bg-gray-100 text-gray-800',
        'cancelado': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getDurationDays = () => {
    const start = new Date(props.plan.start_date);
    const end = new Date(props.plan.end_date);
    const diffTime = Math.abs(end - start);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const getRemainingDays = () => {
    const now = new Date();
    const end = new Date(props.plan.end_date);
    
    if (now > end) return 0;
    
    const diffTime = Math.abs(end - now);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const duplicatePlan = () => {
    duplicateForm(props.plan);
};

const confirmDelete = () => {
    destroyForm();
};
</script>