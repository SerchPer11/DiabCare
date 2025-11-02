<template>
    <CardBox v-if="backups.data && backups.data.length > 0" class="mt-2">
        <div class="flex justify-end mb-4 md:mr-10 gap-2">
            <BaseButton 
                color="warning" 
                :icon="mdiWrench" 
                label="Reparar Estados" 
                title="Corregir estados inconsistentes de respaldos"
                @click="fixStatuses"
                small
            />
            <BaseButton 
                color="success" 
                :icon="mdiCloudUpload" 
                label="Subir Respaldo" 
                title="Subir un respaldo personalizado"
                :routeName="`${routeName}upload.show`" 
                small
            />
            <BaseButton 
                color="info" 
                :icon="mdiPlus" 
                label="Crear Respaldo" 
                title="Crear nuevo respaldo"
                :routeName="`${routeName}create`" 
            />
        </div>

        <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
            <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                <tr>
                    <th class="w-1/5">Nombre del Archivo</th>
                    <th class="w-1/6">Fecha de Creación</th>
                    <th class="w-1/6">Usuario</th>
                    <th class="w-1/8">Tamaño</th>
                    <th class="w-1/8">Estado</th>
                    <th class="w-1/4">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr 
                    v-for="backup in backups.data" 
                    :key="backup.id"
                    class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500"
                >
                    <td data-label="Nombre" class="px-2">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-700">{{ backup.filename }}</span>
                            <span 
                                v-if="backup.description" 
                                class="text-sm text-gray-500"
                            >
                                {{ backup.description }}
                            </span>
                        </div>
                    </td>
                    
                    <td data-label="Fecha">
                        {{ formatDate(backup.created_at) }}
                    </td>
                    
                    <td data-label="Usuario">
                        <span v-if="backup.creator">
                            {{ backup.creator.name }} {{ backup.creator.last_name }}
                        </span>
                        <span v-else class="text-gray-400">Sin Usuario</span>
                    </td>
                    
                    <td data-label="Tamaño">
                        {{ formatFileSize(backup.size) }}
                    </td>
                    
                    <td data-label="Estado">
                        <BaseButton
                            :color="getStatusColor(backup.status)"
                            :label="getStatusLabel(backup.status)"
                            small
                            :title="getStatusLabel(backup.status)"
                        />
                    </td>
                    
                    <td data-label="Acciones">
                        <div class="flex gap-2 justify-center flex-wrap">
                            <!-- Download Button -->
                            <BaseButton 
                                v-if="backup.status === 'completed'"
                                color="info" 
                                :icon="mdiDownload" 
                                small
                                title="Descargar respaldo"
                                @click="downloadBackup(backup.id)" 
                            />
                            
                            <!-- Restore Button -->
                            <BaseButton 
                                v-if="backup.status === 'completed'"
                                color="success" 
                                :icon="mdiHistory" 
                                small
                                title="Restaurar respaldo"
                                :routeName="`${routeName}restore`"
                                :parameter="backup.id"
                            />
                            
                            <!-- Integrity Check Button -->
                            <BaseButton 
                                v-if="backup.status === 'completed'"
                                color="warning" 
                                :icon="mdiShieldCheck" 
                                small
                                title="Verificar integridad"
                                @click="handleIntegrityCheck(backup.id)" 
                                :loading="isChecking === backup.id"
                            />
                            
                            <!-- Delete Button -->
                            <BaseButton 
                                color="danger" 
                                :icon="mdiDelete" 
                                small
                                title="Eliminar respaldo"
                                @click="deleteForm(backup)" 
                            />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination 
            v-if="backups?.meta" 
            :links="backups.meta.links" 
            :total="backups.meta.total" 
            :to="backups.meta.to"
            :from="backups.meta.from" 
        />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay respaldos disponibles</span>
            <div class="flex gap-2">
                <BaseButton 
                    color="success" 
                    :icon="mdiCloudUpload" 
                    label="Subir Respaldo" 
                    title="Subir respaldo personalizado"
                    :routeName="`${routeName}upload.show`" 
                />
                <BaseButton 
                    color="info" 
                    :icon="mdiPlus" 
                    label="Crear Respaldo" 
                    title="Crear primer respaldo"
                    :routeName="`${routeName}create`" 
                />
            </div>
        </div>
    </CardBox>

    <!-- Integrity Check Modal -->
    <Modal 
        :show="showIntegrityModal" 
        @close="showIntegrityModal = false"
    >
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Verificación de Integridad</h3>
            <div class="flex items-center gap-4">
                <Icon 
                    :path="integrityResult?.valid ? mdiCheckCircle : mdiAlertCircle"
                    :class="integrityResult?.valid ? 'text-green-500' : 'text-red-500'"
                    size="24"
                />
                <span>{{ integrityResult?.message || 'Verificando...' }}</span>
            </div>
            <div class="mt-6 flex justify-end">
                <BaseButton 
                    @click="showIntegrityModal = false"
                    color="light"
                    label="Cerrar"
                />
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import Modal from '@/Components/Modal.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Pagination from '@/Components/Pagination.vue';
import Icon from '@/Components/Icon.vue';
import { 
    mdiPencil, 
    mdiDelete, 
    mdiPlus, 
    mdiDownload,
    mdiHistory,
    mdiShieldCheck,
    mdiCheckCircle,
    mdiAlertCircle,
    mdiWrench,
    mdiCloudUpload
} from '@mdi/js';
import { useBackup } from '../Composables/useBackup';
import { router } from '@inertiajs/vue3';
import { messageConfirm } from "@/Hooks/useErrorsForm";

const props = defineProps({
    backups: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'backups.'
    }
});

const { 
    deleteForm, 
    downloadBackup, 
    checkIntegrity,
    formatFileSize,
    formatDate,
    getStatusColor,
    getStatusLabel 
} = useBackup(props);

// Fix backup statuses
const fixStatuses = () => {
    messageConfirm('¿Está seguro de que desea corregir los estados inconsistentes de los respaldos?').then((res) => {
        if (res.isConfirmed) {
            router.post(route(`${props.routeName || 'backups.'}fix-statuses`), {}, {
                preserveScroll: true,
            });
        }
    });
};

// Integrity check state
const showIntegrityModal = ref(false);
const integrityResult = ref(null);
const isChecking = ref(null);

const handleIntegrityCheck = async (backupId) => {
    isChecking.value = backupId;
    integrityResult.value = null;
    showIntegrityModal.value = true;
    
    try {
        const result = await checkIntegrity(backupId);
        integrityResult.value = result;
    } catch (error) {
        integrityResult.value = {
            valid: false,
            message: 'Error al verificar la integridad'
        };
    } finally {
        isChecking.value = null;
    }
};
</script>