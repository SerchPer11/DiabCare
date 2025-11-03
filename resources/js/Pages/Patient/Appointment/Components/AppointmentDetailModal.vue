<template>
    <Modal 
        v-model="showModal" 
        title="Detalle de Cita Médica"
        :maxWidth="maxWidth"
    >
        <div v-if="appointment" class="space-y-6">
            <!-- Estado y modalidad -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div :class="statusBadgeClass" class="px-3 py-1 rounded-full text-sm font-medium">
                        {{ appointment.status?.name || 'Sin estado' }}
                    </div>
                    <div :class="modalityBadgeClass" class="px-2 py-1 rounded text-xs font-medium">
                        {{ appointment.modality }}
                    </div>
                </div>
            </div>

            <!-- Información del doctor -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiDoctor" class="w-5 h-5 text-blue-600 mr-2" />
                    Información del Doctor
                </h4>
                <div class="space-y-2">
                    <p><span class="font-medium">Nombre:</span> {{ appointment.doctor?.name }} {{ appointment.doctor?.last_name }}</p>
                    <p><span class="font-medium">Email:</span> {{ appointment.doctor?.email }}</p>
                </div>
            </div>

            <!-- Fecha y hora -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiCalendar" class="w-5 h-5 text-green-600 mr-2" />
                    Fecha y Hora
                </h4>
                <div class="space-y-2">
                    <p><span class="font-medium">Fecha:</span> {{ formattedDate }}</p>
                    <p><span class="font-medium">Hora:</span> {{ formattedTime }}</p>
                </div>
            </div>

            <!-- Detalles de la cita -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiNoteText" class="w-5 h-5 text-purple-600 mr-2" />
                    Detalles de la Consulta
                </h4>
                <div class="space-y-2">
                    <div>
                        <span class="font-medium">Motivo de consulta:</span>
                        <p class="text-gray-700 mt-1">{{ appointment.reason || 'No especificado' }}</p>
                    </div>
                    <div v-if="appointment.additional_notes">
                        <span class="font-medium">Notas adicionales:</span>
                        <p class="text-gray-700 mt-1">{{ appointment.additional_notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Enlace de videollamada para citas virtuales -->
            <div v-if="appointment.modality === 'Virtual'" class="bg-indigo-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiVideo" class="w-5 h-5 text-indigo-600 mr-2" />
                    Acceso a Videollamada
                </h4>
                <div v-if="appointment.video_call_link">
                    <p class="text-sm text-gray-600 mb-3">
                        Haz clic en el siguiente enlace para unirte a tu cita virtual:
                    </p>
                    <a 
                        :href="appointment.video_call_link" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200"
                    >
                        <Icon :path="mdiVideo" class="w-4 h-4 mr-2" />
                        Unirse a la videollamada
                    </a>
                </div>
                <div v-else class="text-gray-600">
                    <p>El enlace de videollamada será proporcionado antes de la cita.</p>
                </div>
            </div>

            <!-- Información adicional para citas presenciales -->
            <div v-if="appointment.modality === 'Presencial'" class="bg-emerald-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiMapMarker" class="w-5 h-5 text-emerald-600 mr-2" />
                    Cita Presencial
                </h4>
                <p class="text-gray-700">
                    Recuerda asistir puntualmente a tu cita médica en el consultorio del doctor.
                    Se recomienda llegar 10 minutos antes de la hora programada.
                </p>
            </div>

            <!-- Recordatorios generales -->
            <div class="bg-amber-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <Icon :path="mdiInformation" class="w-5 h-5 text-amber-600 mr-2" />
                    Recordatorios Importantes
                </h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Lleva contigo tu identificación oficial</li>
                    <li>• Ten a la mano tu historial médico actualizado</li>
                    <li>• Prepara una lista de síntomas o preguntas para el doctor</li>
                    <li v-if="appointment.modality === 'Virtual'">• Asegúrate de tener una conexión a internet estable</li>
                    <li v-if="appointment.modality === 'Presencial'">• Llega 10 minutos antes de tu cita</li>
                </ul>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end space-x-3">
                <BaseButton 
                    color="secondary"
                    outline
                    @click="closeModal"
                >
                    Cerrar
                </BaseButton>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Icon from '@/Components/Icon.vue';
import { computed } from 'vue';
import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';
import { 
    mdiDoctor, 
    mdiCalendar, 
    mdiNoteText, 
    mdiVideo, 
    mdiMapMarker,
    mdiInformation
} from '@mdi/js';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    appointment: {
        type: Object,
        default: null
    },
    maxWidth: {
        type: String,
        default: '2xl'
    }
});

const emit = defineEmits(['update:modelValue']);

// Control del modal
const showModal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

// Cerrar modal
const closeModal = () => {
    showModal.value = false;
};

// Formatear fecha
const formattedDate = computed(() => {
    if (!props.appointment?.date) return 'Fecha no disponible';
    try {
        return format(parseISO(props.appointment.date), "EEEE, d 'de' MMMM 'de' yyyy", { locale: es });
    } catch (error) {
        return props.appointment.date;
    }
});

// Formatear hora
const formattedTime = computed(() => {
    if (!props.appointment?.time) return 'Hora no disponible';
    try {
        const time = props.appointment.time.substring(0, 5);
        return `${time} hrs`;
    } catch (error) {
        return props.appointment.time;
    }
});

// Clase CSS para el badge de estado
const statusBadgeClass = computed(() => {
    const statusName = props.appointment?.status?.name?.toLowerCase() || '';
    
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
    return props.appointment?.modality === 'Virtual' 
        ? 'bg-indigo-100 text-indigo-800'
        : 'bg-emerald-100 text-emerald-800';
});
</script>