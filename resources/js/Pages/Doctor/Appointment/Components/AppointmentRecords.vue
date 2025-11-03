<template>
    <CardBox v-if="appointments.data && appointments.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10">
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Agregar" 
                title="Crear nueva cita"
                :routeName="`${routeName}create`" 
            />
        </div>

        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
                <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Modalidad</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in appointments.data" :key="item.id"
                        class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500">
                        <td>{{ item.patient?.name ? item.patient.name + ' ' + (item.patient.last_name ?? '') : '-' }}</td>
                        <td>{{ item.doctor?.name ? item.doctor.name + ' ' + (item.doctor.last_name ?? '') : '-' }}</td>
                        <td>{{ item.date }}</td>
                        <td>{{ item.time }}</td>
                        <td>{{ item.modality }}</td>
                        <td>{{ item.reason }}</td>
                        <td>{{ item.status?.name ?? '-' }}</td>
                        <td>
                            <div class="flex gap-4 justify-center">
                                <BaseButton color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                    :parameter="item.id" title="Editar cita" />
                                <BaseButton color="danger" :icon="mdiDelete" small title="Eliminar cita"
                                    @click="deleteAppointment(item.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        <Pagination 
            v-if="appointments?.links" 
            :links="appointments.links" 
            :total="appointments.total" 
            :to="appointments.to"
            :from="appointments.from" 
            typeRecords="citas"
        />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay citas registradas</span>
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Crear Cita" 
                title="Crear primera cita"
                :routeName="`${routeName}create`" 
            />
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useAppointment } from '../Composables/useAppointment';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    appointments: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'appointments.'
    }
});

const { deleteForm } = useAppointment(props);

const deleteAppointment = (id) => {
    deleteForm(id);
};
</script>
