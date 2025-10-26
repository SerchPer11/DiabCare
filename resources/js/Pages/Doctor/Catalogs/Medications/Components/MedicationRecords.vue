<template>
    <CardBox v-if="medications.data && medications.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar usuario"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Presentación</th>
                    <th>Administración</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in medications.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Tipo">
                        <span v-if="item.type.name">{{ item.type.name }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Presentación">
                        <span v-if="item.presentation.name">{{ item.presentation.name }}</span>
                        <span v-else class="text-gray-400">No especificada</span>
                    </td>
                    <td data-label="Administración">
                        <span v-if="item.administration.name">{{ item.administration.name }}</span>
                        <span v-else class="text-gray-400">No especificada</span>
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
        <Pagination v-if="medications?.meta" :links="medications.meta.links" :total="medications.meta.total" :to="medications.meta.to"
        :from="medications.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">No hay registros</span>
                <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar medicación"
                    :routeName="`${routeName}create`" />
            </div>
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useMedication } from '../Composables/useMedication';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    medications: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'medications.'
    }
});

const { deleteForm } = useMedication(props);

const deleteMedication = (medication) => {
    deleteForm(medication);
};

</script>