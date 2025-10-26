<template>
    <CardBox v-if="foods.data && foods.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar usuario"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Nombre</th>
                    <th>Grupo alimenticio</th>
                    <th>Porción estándar</th>
                    <th>Kilocalorías</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in foods.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Grupo">
                        <span v-if="item.foodGroup.name">{{ item.foodGroup.name }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Porción">
                        <span v-if="item.portion_size">{{ item.portion_size}} {{ item.unit.abbreviation }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Kilocalorías">
                        <span v-if="item.calories">{{ item.calories }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deleteMedication(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="foods?.meta" :links="foods.meta.links" :total="foods.meta.total" :to="foods.meta.to"
        :from="foods.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">No hay registros</span>
                <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar ejercicio"
                    :routeName="`${routeName}create`" />
            </div>
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useFood } from '../Composables/useFood';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    foods: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'foods.'
    }
});

const { deleteForm } = useFood(props);

const deleteFood = (food) => {
    deleteForm(food);
};

</script>