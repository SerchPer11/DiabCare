<template>
    <CardBox>
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <IconRounded v-if="main" :icon="mdiClipboardTextClock" :color="color" bg size="20" h="h-10" w="w-10" />
                <BaseIcon v-else :path="mdiClipboardTextClock" size="15" h="h-auto" w="w-auto" class="p-1 rounded-lg" />
                <h1 class="text-sm ml-2">{{ title }} - {{ clinicalLog.event_date }} • {{ clinicalLog.event_time }} </h1>

            </div>
            <div>
                <BaseButton :icon="mdiPencil" color="info" label="Ver detalle" :routeName="`doctor.appointments.edit`"
                    :parameter="clinicalLog.activity.id" class="ml-auto" />
            </div>
        </div>
        <div class="m-6 ml-10 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-400">Motivo</p>
                <p class="text-gray-600">{{ clinicalLog.activity.reason }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Modalidad</p>
                <p class="text-gray-600">{{ clinicalLog.activity.modality }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Doctor</p>
                <p class="text-gray-600">{{ clinicalLog.doctor.name }} {{ clinicalLog.doctor.last_name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Estado</p>
                <p class="text-gray-600">{{ clinicalLog.activity.status.description }}</p>
            </div>

        </div>
    </CardBox>
</template>
<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import IconRounded from '@/Components/IconRounded.vue';
import { mdiClipboardTextClock, mdiPencil } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Cita medica'
    },
    color: {
        type: String,
        default: 'light'
    },
    main: {
        type: Boolean,
        default: true
    },
    clinicalLog: {
        type: Object,
        required: true
    }
});
</script>