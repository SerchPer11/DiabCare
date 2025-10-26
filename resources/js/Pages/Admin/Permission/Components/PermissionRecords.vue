<template>
    <CardBox v-if="permissions.data && permissions.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar módulo"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Nombre</th>
                    <th>Clave</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in permissions.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Clave">
                        <span v-if="item.module_key">{{ item.module_key }}</span>
                        <span v-else class="text-gray-400">Sin Clave</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deletePermission(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="permissions?.meta" :links="permissions.meta.links" :total="permissions.meta.total" :to="permissions.meta.to"
        :from="permissions.meta.from" />
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
import { usePermission } from '../Composables/usePermission';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    permissions: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'permissions.'
    }
});

const { deleteForm } = usePermission(props);

const deletePermission = (permission) => {
    deleteForm(permission);
};

</script>