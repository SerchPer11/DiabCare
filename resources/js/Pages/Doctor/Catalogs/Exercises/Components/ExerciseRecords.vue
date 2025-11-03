<template>
    <CardBox class="mt-2">
        <template v-if="Array.isArray(exercises.data) && exercises.data.length > 0">
            <div class="flex justify-end mb-4 md:mr-10">
                <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar ejercicio"
                    :routeName="`${routeName}create`" />
            </div>
            <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
                <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Intensidad</th>
                        <th>Series</th>
                        <th>Repeticiones</th>
                        <th>Descanso (seg)</th>
                        <th>Equipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in exercises.data" :key="item.id"
                        class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500">
                        <td>{{ item.name }}</td>
                        <td>{{ item.exercise_type?.name ?? '-' }}</td>
                        <td>{{ item.intensity }}</td>
                        <td>{{ item.sets }}</td>
                        <td>{{ item.repetitions }}</td>
                        <td>{{ item.rest_seconds }}</td>
                        <td>{{ item.equipment ?? 'Ninguno' }}</td>
                        <td>
                            <div class="flex gap-4 justify-center">
                                <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                    :parameter="item.id" title="Editar ejercicio" />
                                <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar ejercicio"
                                    @click="deleteExercise(item.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination 
                v-if="exercises?.links" 
                :links="exercises.links" 
                :total="exercises.total" 
                :to="exercises.to"
                :from="exercises.from" 
                typeRecords="ejercicios"
            />
        </template>
        <template v-else>
            <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">No hay registros</span>
                <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar ejercicio"
                    :routeName="`${routeName}create`" />
            </div>
        </template>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useExercise } from '../Composables/useExercise';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    exercises: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'exercises.'
    }
});

const { deleteForm } = useExercise(props);

const deleteExercise = (id) => {
    deleteForm(id);
};
</script>
