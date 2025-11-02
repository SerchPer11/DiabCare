<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <CardBox>
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <Icon :path="mdiHistory" class="mx-auto text-orange-500 mb-4" size="64" />
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Restaurar Base de Datos</h2>
                    <p class="text-gray-600">
                        Se restaurará la base de datos completa desde el respaldo seleccionado.
                    </p>
                </div>

                <!-- Critical Warning -->
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <Icon :path="mdiAlertOctagon" class="text-rose-500 mr-3 flex-shrink-0" size="24" />
                        <div>
                            <h4 class="text-rose-800 font-bold text-lg">ADVERTENCIA CRÍTICA</h4>
                            <ul class="text-rose-700 mt-2 list-disc list-inside space-y-1">
                                <li><strong>Esta operación NO se puede deshacer</strong></li>
                                <li>Se perderán todos los datos actuales de la base de datos</li>
                                <li>Se creará un respaldo de seguridad antes de la restauración</li>
                                <li>El sistema puede estar fuera de servicio durante varios minutos</li>
                                <li>Solo administradores pueden realizar esta operación</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Backup Information -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                        <Icon :path="mdiInformationOutline" class="mr-2 text-slate-600" size="24" />
                        Información del Respaldo a Restaurar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Nombre del Archivo:</label>
                                <p class="text-slate-800 font-mono">{{ backupData.filename }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Fecha de Creación:</label>
                                <p class="text-slate-800">{{ formatDate(backupData.created_at) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Usuario Creador:</label>
                                <p class="text-slate-800">
                                    {{ backupData.creator?.name }} {{ backupData.creator?.last_name }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Tamaño del Archivo:</label>
                                <p class="text-slate-800">{{ formatFileSize(backupData.size) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Estado:</label>
                                <BaseButton
                                    :color="getStatusColor(backupData.status)"
                                    :label="getStatusLabel(backupData.status)"
                                    small
                                />
                            </div>
                            <div v-if="backupData.description">
                                <label class="text-sm font-medium text-slate-600">Descripción:</label>
                                <p class="text-slate-800">{{ backupData.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Integrity Check -->
                <div class="bg-sky-50 border border-sky-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Icon :path="mdiShieldCheck" class="text-sky-500 mr-3" size="24" />
                            <div>
                                <h4 class="font-semibold text-sky-800">Verificación de Integridad</h4>
                                <p class="text-sky-700 text-sm">
                                    {{ integrityStatus || 'Haga clic para verificar la integridad del respaldo' }}
                                </p>
                            </div>
                        </div>
                        <BaseButton
                            color="info"
                            :icon="mdiShieldCheck"
                            label="Verificar"
                            @click="checkIntegrityHandler"
                            :loading="checkingIntegrity"
                        />
                    </div>
                </div>

                <!-- Confirmation Form -->
                <form @submit.prevent="handleRestore" class="space-y-6">
                    <!-- Confirmation Steps -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <input
                                id="understand-warning"
                                v-model="confirmations.understandWarning"
                                type="checkbox"
                                class="mt-1 mr-3"
                            />
                            <label for="understand-warning" class="text-gray-700">
                                Entiendo que esta operación eliminará todos los datos actuales y no se puede deshacer
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input
                                id="backup-created"
                                v-model="confirmations.backupWillBeCreated"
                                type="checkbox"
                                class="mt-1 mr-3"
                            />
                            <label for="backup-created" class="text-gray-700">
                                Confirmo que se creará un respaldo de seguridad antes de la restauración
                            </label>
                        </div>

                        <div class="flex items-start">
                            <input
                                id="authorized"
                                v-model="confirmations.authorized"
                                type="checkbox"
                                class="mt-1 mr-3"
                            />
                            <label for="authorized" class="text-gray-700">
                                Tengo autorización para realizar esta operación crítica
                            </label>
                        </div>
                    </div>

                    <!-- Final Confirmation -->
                    <div class="bg-rose-50 border border-rose-300 rounded-lg p-4">
                        <BaseFormField 
                            type="input"
                            label="Escriba 'CONFIRMAR' para proceder con la restauración:"
                            v-model="restoreForm.confirmation"
                            placeholder="Escriba CONFIRMAR (en mayúsculas)"
                            :error="restoreForm.errors.confirmation"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center pt-6 border-t">
                        <BaseButton
                            :routeName="`${routeName}index`"
                            color="light"
                            :icon="mdiArrowLeft"
                            label="Cancelar"
                        />

                        <BaseButton
                            type="submit"
                            color="danger"
                            :icon="mdiHistory"
                            label="RESTAURAR BASE DE DATOS"
                            :loading="restoreForm.processing || isLoading"
                            :disabled="!canProceed || restoreForm.processing || isLoading"
                        />
                    </div>
                </form>

                <!-- Progress Indicator -->
                <div v-if="restoreForm.processing || isLoading" class="mt-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-2 border-orange-400 border-t-transparent mr-3"></div>
                            <div>
                                <h4 class="font-semibold text-orange-800">Restaurando Base de Datos...</h4>
                                <p class="text-orange-700 text-sm">
                                    Este proceso puede tomar varios minutos. Por favor, no cierre esta ventana.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardBox>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardBox from '@/Components/CardBox.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import Icon from '@/Components/Icon.vue';
import {
    mdiHistory,
    mdiAlertOctagon,
    mdiInformationOutline,
    mdiShieldCheck,
    mdiArrowLeft
} from '@mdi/js';
import { useBackup } from '../Composables/useBackup';

const props = defineProps({
    title: {
        type: String,
        default: 'Restaurar Respaldo'
    },
    backup: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'backups.'
    }
});

const {
    restoreForm,
    restoreBackup,
    checkIntegrity,
    isLoading,
    formatFileSize,
    formatDate,
    getStatusColor,
    getStatusLabel
} = useBackup(props);

// Computed property to access backup data correctly  
const backupData = computed(() => {
    // Si los datos vienen con .data (resource wrapper), usar eso, sino usar directamente
    return props.backup.data || props.backup;
});

// Confirmation states
const confirmations = ref({
    understandWarning: false,
    backupWillBeCreated: false,
    authorized: false
});

// Integrity check
const checkingIntegrity = ref(false);
const integrityStatus = ref(null);

const canProceed = computed(() => {
    return confirmations.value.understandWarning &&
           confirmations.value.backupWillBeCreated &&
           confirmations.value.authorized &&
           restoreForm.confirmation === 'CONFIRMAR';
});

const checkIntegrityHandler = async () => {
    checkingIntegrity.value = true;
    try {
        const result = await checkIntegrity(backupData.value.id);
        integrityStatus.value = result.valid 
            ? 'El respaldo está íntegro y listo para restaurar'
            : 'El respaldo está corrupto - NO se puede restaurar';
    } catch (error) {
        integrityStatus.value = 'Error al verificar la integridad';
    } finally {
        checkingIntegrity.value = false;
    }
};

const handleRestore = () => {
    if (canProceed.value) {
        restoreBackup(backupData.value.id);
    }
};
</script>