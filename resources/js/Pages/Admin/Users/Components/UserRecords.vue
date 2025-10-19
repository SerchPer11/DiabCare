<template>
    <CardBox v-if="users.data && users.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar usuario"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol(es)</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in users.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Correo">
                        <span v-if="item.email">{{ item.email }}</span>
                        <span v-else class="text-gray-400">Sin Correo</span>
                    </td>
                    <td data-label="Correo">
                        <span v-if="item.email">{{ item.email }}</span>
                        <span v-else class="text-gray-400">Sin Correo</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deleteRole(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="users?.meta" :links="users.meta.links" :total="users.meta.total" :to="users.meta.to"
        :from="users.meta.from" />
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useUser } from '../Composables/useUser';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'users.'
    }
});

const { deleteForm } = useUser(props);

const deleteRole = (role) => {
    deleteForm(role);
};

</script>