<template>
    <CardBox v-if="recomendations.data && recomendations.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar usuario"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Titulo</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in recomendations.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Titulo">
                        {{ item.title }}
                    </td>
                    <td data-label="Paciente">
                        <span v-if="item.patient">{{ item.patient?.name ? item.patient.name + ' ' + (item.patient.last_name ?? '') : '-' }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Doctor">
                        <span v-if="item.doctor">{{ item.doctor?.name ? item.doctor.name + ' ' + (item.doctor.last_name ?? '') : '-' }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Tipo">
                        <span v-if="item.type">{{ item.type.name }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Prioridad">
                        <span v-if="item.priority" :class="priorityColors[item.priority]">{{ priority[item.priority] }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deleteRecomendation(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="recomendations?.meta" :links="recomendations.meta.links" :total="recomendations.meta.total" :to="recomendations.meta.to"
        :from="recomendations.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">No hay registros</span>
                <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar recomendación"
                    :routeName="`${routeName}create`" />
            </div>
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useRecomendation } from '../Composables/useRecomendation';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    recomendations: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'recomendations.'
    }
});

const { deleteForm } = useRecomendation(props);

const deleteRecomendation = (recomendation) => {
    deleteForm(recomendation);
};

const priority = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta'
};

const priorityColors = {
    low: 'text-green-500',
    medium: 'text-yellow-500',
    high: 'text-red-500'
};
</script>