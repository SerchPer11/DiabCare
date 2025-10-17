<template>
    <CardBox v-if="modules.data && modules.data.length > 0" class="mt-2">
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
                <tr v-for="item in modules.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Clave">
                        <span v-if="item.key">{{ item.key }}</span>
                        <span v-else class="text-gray-400">Sin Clave</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deleteModule(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="modules?.meta" :links="modules.meta.links" :total="modules.meta.total" :to="modules.meta.to"
        :from="modules.meta.from" />
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useModule } from '../Composables/useModule';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    modules: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'modules.'
    }
});

const { deleteForm } = useModule(props);

const deleteModule = (module) => {
    deleteForm(module);
};

</script>