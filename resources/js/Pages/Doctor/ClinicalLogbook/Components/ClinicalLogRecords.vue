<template>
    <CardBox v-if="entries.data && entries.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Agregar" 
                title="Crear nueva entrada de bitácora"
                :routeName="`${routeName}create`" 
            />
        </div>

        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="entry in entries.data" :key="entry.id"
                    class="border border-gray-200 h-16 bg-gray-50 shadow-sm text-gray-500">
                    
                    <td data-label="Fecha/Hora">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ formatDate(entry.event_datetime) }}</div>
                            <div class="text-xs text-gray-500">{{ formatTime(entry.event_datetime) }}</div>
                        </div>
                    </td>

                    <td data-label="Tipo">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getEventTypeClass(entry.event_type)">
                            {{ entry.event_type_name }}
                        </span>
                    </td>

                    <td data-label="Título">
                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs" :title="entry.title">
                            {{ entry.title }}
                        </div>
                        <div v-if="entry.related" class="text-xs text-gray-500 mt-1">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {{ getRelatedTypeLabel(entry.related.type) }}
                            </span>
                        </div>
                    </td>

                    <td data-label="Paciente">
                        <div class="flex items-center justify-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-blue-600">
                                    {{ (entry.patient?.name || 'P').charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div class="ml-2 text-left">
                                <div class="text-sm font-medium text-gray-900">{{ getFullName(entry.patient) || 'Sin paciente' }}</div>
                                <div class="text-xs text-gray-500">{{ entry.patient?.email || '' }}</div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Doctor">
                        <div class="text-sm text-gray-900">{{ getFullName(entry.doctor) || 'Sin doctor' }}</div>
                    </td>

                    <td data-label="Acciones">
                        <div class="flex gap-2 justify-center">
                            <BaseButton 
                                color="info" 
                                :icon="mdiPencil" 
                                small 
                                :routeName="`${routeName}edit`"
                                :parameter="entry.id" 
                                title="Editar entrada" 
                            />
                            <BaseButton 
                                color="danger" 
                                :icon="mdiDelete" 
                                small 
                                title="Eliminar entrada"
                                @click="deleteEntry(entry)" 
                            />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination 
            v-if="entries?.meta" 
            :links="entries.meta.links" 
            :total="entries.meta.total" 
            :to="entries.meta.to"
            :from="entries.meta.from" 
            typeRecords="entradas"
        />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay entradas de bitácora registradas</span>
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Crear Primera Entrada" 
                title="Crear primera entrada de bitácora"
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
import { useClinicalLogBook } from '../Composables/useClinicalLogBook';

const props = defineProps({
    entries: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'doctor.clinical-logbook.'
    }
});

const { deleteForm } = useClinicalLogBook(props);

const deleteEntry = (entry) => {
    deleteForm(entry);
};

// Métodos de utilidad
const getFullName = (person) => {
    if (!person) return '';
    const parts = [person.name, person.last_name, person.second_last_name]
        .filter(part => part && part.trim() !== '');
    return parts.join(' ');
};

const formatDate = (datetime) => {
    return new Date(datetime).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getEventTypeClass = (eventType) => {
    const classes = {
        'observation': 'bg-blue-100 text-blue-800',
        'medication_adjustment': 'bg-yellow-100 text-yellow-800',
        'incident': 'bg-red-100 text-red-800',
        'document': 'bg-green-100 text-green-800'
    };
    return classes[eventType] || 'bg-gray-100 text-gray-800';
};

const getRelatedTypeLabel = (type) => {
    const labels = {
        'App\\Models\\Appointment': 'Cita',
        'App\\Models\\Plan': 'Plan',
        'App\\Models\\Doctor\\Catalogs\\Medication': 'Medicamento'
    };
    return labels[type] || 'Relacionado';
};
</script>