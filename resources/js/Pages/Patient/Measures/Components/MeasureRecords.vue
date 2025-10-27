<template>
    <CardBox v-if="measures.data && measures.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar usuario"
                :routeName="`${routeName}create`" />
        </div>
        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th>Paciente</th>
                    <th>Tipo de monitoreo</th>
                    <th>Valor registrado</th>
                    <th>Unidad(es)</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="item in measures.data" :key="item.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500  ">
                    <td data-label="Paciente">
                        <span v-if="item.measureConfig.patient">{{ item.measureConfig.patient.name + ' ' + (item.measureConfig.patient.last_name ?? '') }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Tipo de monitoreo">
                        <span v-if="item.measureConfig.measure_type">{{ item.measureConfig.measure_type.name }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Valor registrado">
                        <span v-if="item.value" :class="valueColor(item)">{{ item.value }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Unidad(es)">
                        <span v-if="item.measureConfig.measure_type">{{ item.measureConfig.measure_type.unit }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Fecha de registro">
                        <span v-if="item.measured_at">{{ item.measured_at }}</span>
                        <span v-else class="text-gray-400">No especificado</span>
                    </td>
                    <td data-label="Acciones">
                        <div class="flex gap-4 justify-center">
                            <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                :parameter="item.id" title="Editar noticia" />
                            <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar noticia"
                                @click="deleteMeasure(item)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination v-if="measures?.meta" :links="measures.meta.links" :total="measures.meta.total" :to="measures.meta.to"
        :from="measures.meta.from" />
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
import { useMeasure } from '../Composables/useMeasure';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    measures: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'measures.'
    }
});

const { deleteForm } = useMeasure(props);

const deleteMeasure = (measure) => {
    deleteForm(measure);
};

const valueColor = (item) => {
    if (!item.measureConfig) {
        return 'text-yellow-700'; // O return '' (vacío)
    }
    const value = parseFloat(item.value);
    const range = item.measureConfig?.range;
    const measureConfig = item.measureConfig;

    const goodClass = 'text-green-600';
    const badClass = 'text-red-500';

    console.log(value, range, measureConfig.min_value, measureConfig.max_value);

    if (range === "outrange") {
        if(measureConfig.max_value >= value && value >= measureConfig.min_value ){
            return goodClass;
        }else{
            return badClass;
        }
    }else if(range === "above"){
        if(value < measureConfig.max_value){
            return goodClass;
        }else{
            return badClass;
        }
    }else if(range === "below"){
        if(value > measureConfig.min_value){
            return goodClass;
        }else{
            return badClass;
        }
    }
    return 'text-yellow-700';
};
</script>