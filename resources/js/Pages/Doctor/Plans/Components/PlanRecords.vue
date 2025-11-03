<template>
    <CardBox v-if="plans.data && plans.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Agregar" 
                title="Crear nuevo plan"
                :routeName="`${routeName}create`" 
            />
        </div>



        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Paciente</th>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Vigencia</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="plan in plans.data" :key="plan.id"
                    class="border border-gray-200 h-16 bg-gray-50 shadow-sm text-gray-500">
                    
                    <td data-label="Paciente">
                        <div class="flex items-center justify-center">
                            <div class="ml-2 text-left">
                                <div class="text-sm font-medium text-gray-900">{{ getFullName(plan.patient) }}</div>
                                <div class="text-xs text-gray-500">{{ plan.patient.email }}</div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Tipo">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getPlanTypeClass(plan.plan_type.name)">
                            {{ plan.plan_type.name === 'alimentacion' ? 'Alimentación' : 'Actividad Física' }}
                        </span>
                    </td>

                    <td data-label="Título">
                        <div class="text-sm font-medium text-gray-900">{{ plan.title || 'Sin título definido' }}</div>
                    </td>

                    <td data-label="Vigencia">
                        <div>
                            <div class="text-xs text-gray-600">
                                {{ formatDate(plan.start_date) }} - {{ formatDate(plan.end_date) }}
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="getVigencyClass(plan.start_date, plan.end_date)">
                                {{ getVigencyStatus(plan.start_date, plan.end_date) }}
                            </span>
                        </div>
                    </td>

                    <td data-label="Status">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getStatusClass(plan.status)">
                            {{ plan.status.charAt(0).toUpperCase() + plan.status.slice(1) }}
                        </span>
                    </td>

                    <td data-label="Acciones">
                        <div class="flex gap-2 justify-center">
                            <BaseButton 
                                color="info" 
                                :icon="mdiPencil" 
                                small 
                                :routeName="`${routeName}edit`"
                                :parameter="plan.id" 
                                title="Editar plan" 
                            />
                            <BaseButton 
                                color="danger" 
                                :icon="mdiDelete" 
                                small 
                                title="Eliminar plan"
                                @click="deletePlan(plan)" 
                            />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination 
            v-if="plans?.meta" 
            :links="plans.meta.links" 
            :total="plans.meta.total" 
            :to="plans.meta.to"
            :from="plans.meta.from" 
            typeRecords="planes"
        />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay planes registrados</span>
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Crear Plan" 
                title="Crear primer plan"
                :routeName="`${routeName}create`" 
            />
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Pagination from "@/Components/Pagination.vue";
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { usePlan } from '../Composables/usePlan';

const props = defineProps({
    plans: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'doctor.plans.'
    }
});

const { deleteForm } = usePlan(props);

const deletePlan = (plan) => {
    deleteForm(plan);
};

// Métodos de utilidad
const getFullName = (patient) => {
    const parts = [patient.name, patient.last_name, patient.second_last_name]
        .filter(part => part && part.trim() !== ''); // Filtrar partes vacías o null
    return parts.join(' ');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getVigencyStatus = (startDate, endDate) => {
    const now = new Date();
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (now < start) return 'Por iniciar';
    if (now > end) return 'Vencido';
    return 'Vigente';
};

const getVigencyClass = (startDate, endDate) => {
    const status = getVigencyStatus(startDate, endDate);
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
</script>