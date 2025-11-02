<template>
    <CardBox class="mt-4">
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiDiabetes" size="24" h="h-8"
                w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte por evolución de glucosa</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por evolución de glucosa.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter=""/>
            </div>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiNotebookMultiple" size="24"
                h="h-8" w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte por adherencia a planes</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por adherencia a planes.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter="" />
            </div>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiClipboardTextClock"
                size="24" h="h-8" w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte por estado de citas</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por estado de citas.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter="" />
            </div>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiOrderBoolDescendingVariant"
                size="24" h="h-8" w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte por tipo de diabetes</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por tipo de diabetes.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter="diabetes-type" />
            </div>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiAccountGroup " size="24" h="h-8"
                w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte demografico</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por edad y género.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter="demographic" />
            </div>
        </div>
        <BaseDivider />
        <div class="flex gap-2 items-center text-left m-2">
            <BaseIcon class="bg-medic-500 text-gray-100 rounded-lg flex-shrink-0" :path="mdiClipboardPulse" size="24" h="h-8"
                w="w-8" />
            <div>
                <p class="text-medic-400 text-xl font-bold">Reporte por mediciones agregadas</p>
                <p class="text-sm font-light text-gray-500">
                    Reporte que muestra la cantidad de pacientes por mediciones agregadas.
                </p>
            </div>
            <div class="ml-auto">
                <BaseButton :icon="mdiEye" color="info" label="Ver" :routeName="`${routeName}show`" class="w-32" parameter="measures" />
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import BaseButton from '@/Components/BaseButton.vue';
import {
    mdiDiabetes,
    mdiEye,
    mdiNotebookMultiple,
    mdiClipboardTextClock,
    mdiOrderBoolDescendingVariant,
    mdiAccountGroup,
    mdiClipboardPulse
} from '@mdi/js';

const props = defineProps({
    routeName: {
        type: String,
        required: true
    }
});
</script>