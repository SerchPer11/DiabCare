<template>
    <CardBox v-if="clinicalLogs.data && clinicalLogs.data.length > 0" class="border-0 shadow-none overflow-auto max-h-96" bg="transparent">
        <div v-for="clinicalLog in clinicalLogs.data" :key="clinicalLog.id" class="mb-4">
            <AppointmentBase v-if="clinicalLog.activity_type === 'Appointment'" :clinicalLog="clinicalLog" />
            <RecomendationBase v-else-if="clinicalLog.activity_type === 'Recomendation'" :clinicalLog="clinicalLog" />
        </div>
    </CardBox>
        
    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay registros</span>
        </div>
    </CardBox>

</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import AppointmentBase from './AppointmentBase.vue';
import RecomendationBase from './RecomendationBase.vue';

const props = defineProps({
    clinicalLogs: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'clinicalLogs.'
    }
});
</script>