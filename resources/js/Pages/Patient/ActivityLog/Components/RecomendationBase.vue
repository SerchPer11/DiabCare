<template>
    <CardBox>
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <IconRounded v-if="main" :icon="mdiHandHeart" :color="color" bg size="20" h="h-10" w="w-10" />
                <BaseIcon v-else :path="mdiClipboardTextClock" size="15" h="h-auto" w="w-auto" class="p-1 rounded-lg" />
                <h1 class="text-sm ml-2">{{ title }} - {{ clinicalLog.event_date }} • {{ clinicalLog.event_time }} </h1>

            </div>
            <div>
                <BaseButton v-if="userPrimaryRole !== 'patient'" :icon="mdiPencil" color="info" label="Ver detalle"
                    :routeName="`doctor.recomendations.edit`" :parameter="clinicalLog.activity?.id" class="ml-auto" />
                <BaseButton v-if="userPrimaryRole === 'patient'" :icon="mdiEye" color="info" label="Ver detalle"
                    :routeName="`doctor.recomendations.edit`" :parameter="clinicalLog.activity?.id" class="ml-auto" />
            </div>
        </div>
        <div class="m-6 ml-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <p class="text-xs text-gray-400">Título</p>
                <p class="text-gray-600">{{ clinicalLog.activity?.title }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Prioridad</p>
                <p class="text-gray-600">{{ priority[clinicalLog.activity?.priority] }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Fecha de inicio</p>
                <p class="text-gray-600">{{ clinicalLog.activity?.start_date }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Fecha de fin</p>
                <p class="text-gray-600">{{ clinicalLog.activity?.end_date }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Doctor</p>
                <p class="text-gray-600">{{ clinicalLog.doctor?.name }} {{ clinicalLog.doctor?.last_name }}</p>
            </div>
        </div>
    </CardBox>
</template>
<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import IconRounded from '@/Components/IconRounded.vue';
import { mdiHandHeart, mdiPencil, mdiEye } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Recomendación medica'
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

const priority = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta'
};
const userPrimaryRole = computed(() => {
    return usePage().props.auth?.roles?.[0] ?? null;
});
</script>