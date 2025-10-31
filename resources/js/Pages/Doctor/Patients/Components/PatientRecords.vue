<template>
    <CardBox v-if="patients.data && patients.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Seguimiento del paciente</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in patients.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Nombre">
                        {{ item.name }}
                    </td>
                    <td data-label="Apellido">
                        {{ item.last_name }}
                    </td>
                    <td data-label="Correo">
                        {{ item.email }}
                    </td>
                    <td data-label="Seguimiento del paciente">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiEye " small :routeName="`patient.clinical-log.show`"
                                :parameter="item.id" title="Ver seguimiento" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="patients?.meta" :links="patients.meta.links" :total="patients.meta.total" :to="patients.meta.to"
        :from="patients.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">No hay registros</span>
            </div>
    </CardBox>
    
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiEye } from '@mdi/js';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    patients: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'patients.'
    }
});
</script>