<template>
    <CardBox v-if="modules && modules.length > 0" class="mt-2">
            <table class="w-full text-center border-separate border-spacing-2 md:table-fixed sm:table-auto shadow-md">
                <thead class="h-12 border-gray-400 bg-medic-100 text-gray-600 font-semibold shadow-sm">
                    <tr>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in modules" :key="item.id" class="border border-gray-400 h-12 bg-gray-50 shadow-sm text-gray-600 ">
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
                                <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete } from '@mdi/js';

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

</script>