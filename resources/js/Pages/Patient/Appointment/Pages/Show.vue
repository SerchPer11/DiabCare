<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto">
                <!-- Header con navegación -->
                <div class="mb-6">
                    <BaseButton
                        color="secondary"
                        outline
                        @click="goBack"
                        class="mb-4"
                    >
                        <Icon :path="mdiArrowLeft" class="w-4 h-4 mr-2" />
                        Volver a mis citas
                    </BaseButton>
                    
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">Detalle de Cita Médica</h1>
                        <div class="flex items-center space-x-3">
                            <div :class="statusBadgeClass" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ appointment.status?.name || 'Sin estado' }}
                            </div>
                            <div :class="modalityBadgeClass" class="px-2 py-1 rounded text-xs font-medium">
                                {{ appointment.modality }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de información -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Información del doctor -->
                    <CardBox class="h-fit">
                        <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                            <Icon :path="mdiDoctor" class="w-6 h-6 text-blue-600 mr-2" />
                            Información del Doctor
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Nombre completo</span>
                                <p class="text-gray-900 font-medium">{{ appointment.doctor?.name }} {{ appointment.doctor?.last_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Email de contacto</span>
                                <p class="text-gray-900">{{ appointment.doctor?.email }}</p>
                            </div>
                        </div>
                    </CardBox>

                    <!-- Fecha y hora -->
                    <CardBox class="h-fit">
                        <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                            <Icon :path="mdiCalendar" class="w-6 h-6 text-green-600 mr-2" />
                            Fecha y Hora
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Fecha de la cita</span>
                                <p class="text-gray-900 font-medium">{{ formattedDate }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Hora programada</span>
                                <p class="text-gray-900">{{ formattedTime }}</p>
                            </div>
                        </div>
                    </CardBox>
                </div>

                <!-- Acciones de la cita -->
                <CardBox v-if="canChangeStatus" class="mt-6 border-blue-200 bg-blue-50">
                    <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                        <Icon :path="mdiInformation" class="w-6 h-6 text-blue-600 mr-2" />
                        Acciones de la Cita
                    </h3>
                    <p class="text-gray-700 mb-4">
                        ¿Deseas confirmar tu asistencia a esta cita médica o necesitas cancelarla?
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <BaseButton
                            v-for="status in availableStatuses"
                            :key="status.id"
                            :color="status.name === 'aceptada' ? 'success' : 'danger'"
                            @click="updateStatus(status.id)"
                            class="flex items-center"
                        >
                            <Icon 
                                :path="status.name === 'aceptada' ? mdiCheckBold : mdiClose" 
                                class="w-4 h-4 mr-2" 
                            />
                            {{ status.name === 'aceptada' ? 'Aceptar Cita' : 'Cancelar Cita' }}
                        </BaseButton>
                    </div>
                </CardBox>

                <!-- Detalles de la consulta -->
                <CardBox class="mt-6">
                    <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                        <Icon :path="mdiNoteText" class="w-6 h-6 text-purple-600 mr-2" />
                        Detalles de la Consulta
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Motivo de consulta</span>
                            <p class="text-gray-900 mt-1">{{ appointment.reason || 'No especificado' }}</p>
                        </div>
                        <div v-if="appointment.additional_notes">
                            <span class="text-sm font-medium text-gray-500">Notas adicionales</span>
                            <p class="text-gray-900 mt-1">{{ appointment.additional_notes }}</p>
                        </div>
                    </div>
                </CardBox>

                <!-- Enlace de videollamada para citas virtuales -->
                <CardBox v-if="appointment.modality === 'Virtual'" class="mt-6 border-indigo-200 bg-indigo-50">
                    <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                        <Icon :path="mdiVideo" class="w-6 h-6 text-indigo-600 mr-2" />
                        Acceso a Videollamada
                    </h3>
                    <div v-if="appointment.video_call_link">
                        <p class="text-gray-700 mb-4">
                            Tu cita médica será virtual. Utiliza el siguiente enlace para unirte a la videollamada en el horario programado:
                        </p>
                        <BaseButton
                            color="info"
                            class="inline-flex items-center"
                            @click="joinVideoCall"
                        >
                            <Icon :path="mdiVideo" class="w-4 h-4 mr-2" />
                            Unirse a la videollamada
                        </BaseButton>
                    </div>
                    <div v-else>
                        <p class="text-gray-600">
                            El enlace de videollamada será proporcionado por el doctor antes de la hora de tu cita. 
                            Revisa tu email o contacta al doctor si no recibes el enlace.
                        </p>
                    </div>
                </CardBox>


            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Icon from '@/Components/Icon.vue';
import { computed } from 'vue';
import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';
import { router } from '@inertiajs/vue3';
import { 
    mdiArrowLeft,
    mdiDoctor, 
    mdiCalendar, 
    mdiNoteText, 
    mdiVideo, 
    mdiInformation,
    mdiCheckBold,
    mdiClose
} from '@mdi/js';

const props = defineProps({
    appointment: {
        type: Object,
        required: true
    },
    availableStatuses: {
        type: Array,
        default: () => []
    }
});

// Título de la página
const title = 'Detalle de Cita';

// Funciones de navegación
const goBack = () => {
    router.visit(route('patient.appointments.index'));
};

const joinVideoCall = () => {
    if (props.appointment.video_call_link) {
        window.open(props.appointment.video_call_link, '_blank', 'noopener,noreferrer');
    }
};

// Función para actualizar el estado de la cita
const updateStatus = (statusId) => {
    router.patch(route('patient.appointments.update-status', props.appointment.id), {
        status_id: statusId
    });
};

// Verificar si se pueden cambiar los estados
const canChangeStatus = computed(() => {
    const currentStatus = props.appointment.status?.name?.toLowerCase();
    // Solo permitir cambios si está programada
    return currentStatus === 'programada';
});

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
</script>