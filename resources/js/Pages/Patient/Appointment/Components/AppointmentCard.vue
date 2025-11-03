<template>
    <CardBox class="hover:shadow-lg transition-shadow duration-200 cursor-pointer" @click="goToShow">
        <!-- Header de la tarjeta -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div :class="statusBadgeClass" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ appointment.status?.name || 'Sin estado' }}
                </div>
                <div :class="modalityBadgeClass" class="px-2 py-1 rounded text-xs font-medium">
                    {{ appointment.modality }}
                </div>
            </div>
            <Icon :path="mdiCalendar" class="w-6 h-6 text-gray-400" />
        </div>

        <!-- Información del doctor -->
        <div class="mb-4">
            <div class="flex items-center space-x-2 mb-2">
                <Icon :path="mdiDoctor" class="w-5 h-5 text-blue-600" />
                <span class="font-semibold text-gray-900">
                    {{ appointment.doctor?.name }} {{ appointment.doctor?.last_name }}
                </span>
            </div>
            <p class="text-sm text-gray-600 ml-7">{{ appointment.doctor?.email }}</p>
        </div>

        <!-- Fecha y hora -->
        <div class="mb-4">
            <div class="flex items-center space-x-2 mb-1">
                <Icon :path="mdiClockOutline" class="w-5 h-5 text-green-600" />
                <span class="font-medium text-gray-900">{{ formattedDate }}</span>
            </div>
            <p class="text-sm text-gray-600 ml-7">{{ formattedTime }}</p>
        </div>

        <!-- Razón de la cita -->
        <div class="mb-4">
            <div class="flex items-start space-x-2">
                <Icon :path="mdiNoteText" class="w-5 h-5 text-purple-600 mt-0.5" />
                <div>
                    <span class="font-medium text-gray-900 block">Motivo de consulta</span>
                    <p class="text-sm text-gray-600">{{ appointment.reason || 'No especificado' }}</p>
                </div>
            </div>
        </div>

        <!-- Notas adicionales si existen -->
        <div v-if="appointment.additional_notes" class="mb-4">
            <div class="flex items-start space-x-2">
                <Icon :path="mdiInformation" class="w-5 h-5 text-amber-600 mt-0.5" />
                <div>
                    <span class="font-medium text-gray-900 block">Notas adicionales</span>
                    <p class="text-sm text-gray-600">{{ appointment.additional_notes }}</p>
                </div>
            </div>
        </div>

        <!-- Enlace de videollamada para citas virtuales -->
        <div v-if="appointment.modality === 'Virtual' && appointment.video_call_link" class="mb-4">
            <div class="flex items-center space-x-2">
                <Icon :path="mdiVideo" class="w-5 h-5 text-red-600" />
                <a 
                    :href="appointment.video_call_link" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium underline"
                >
                    Unirse a la videollamada
                </a>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100" @click.stop>
            <BaseButton
                color="info"
                small
                @click="goToShow"
            >
                <Icon :path="mdiArrowRight" class="w-4 h-4 mr-2" />
                Ver detalles
            </BaseButton>
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Icon from '@/Components/Icon.vue';
import { computed } from 'vue';
import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';
import { router } from '@inertiajs/vue3';
import { 
    mdiCalendar, 
    mdiDoctor, 
    mdiClockOutline, 
    mdiNoteText, 
    mdiInformation, 
    mdiVideo, 
    mdiArrowRight
} from '@mdi/js';

const props = defineProps({
    appointment: {
        type: Object,
        required: true
    }
});

// defineEmits(['view']); // Ya no se necesita

// Formatear fecha
const formattedDate = computed(() => {
    if (!props.appointment.date) return 'Fecha no disponible';
    try {
        return format(parseISO(props.appointment.date), "EEEE, d 'de' MMMM 'de' yyyy", { locale: es });
    } catch (error) {
        return props.appointment.date;
    }
});

// Formatear hora
const formattedTime = computed(() => {
    if (!props.appointment.time) return 'Hora no disponible';
    try {
        // Si la hora viene en formato HH:mm:ss, extraer solo HH:mm
        const time = props.appointment.time.substring(0, 5);
        return `${time} hrs`;
    } catch (error) {
        return props.appointment.time;
    }
});

// Clase CSS para el badge de estado
const statusBadgeClass = computed(() => {
    const statusName = props.appointment.status?.name?.toLowerCase() || '';
    
    const statusClasses = {
        'programada': 'bg-blue-100 text-blue-800',
        'aceptada': 'bg-emerald-100 text-emerald-800',
        'en proceso': 'bg-yellow-100 text-yellow-800',
        'completada': 'bg-purple-100 text-purple-800',
        'cancelada': 'bg-red-100 text-red-800',
        'reagendada': 'bg-orange-100 text-orange-800'
    };
    
    return statusClasses[statusName] || 'bg-gray-100 text-gray-800';
});

// Clase CSS para el badge de modalidad
const modalityBadgeClass = computed(() => {
    return props.appointment.modality === 'Virtual' 
        ? 'bg-indigo-100 text-indigo-800'
        : 'bg-emerald-100 text-emerald-800';
});

// Función para navegar a la página Show
const goToShow = () => {
    router.visit(route('patient.appointments.show', props.appointment.id));
};
</script>