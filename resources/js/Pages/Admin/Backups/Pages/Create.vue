<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <CardBox>
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <Icon :path="mdiDatabase" class="mx-auto text-blue-500 mb-4" size="64" />
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Crear Respaldo de Base de Datos</h2>
                    <p class="text-gray-600">
                        Se creará un respaldo completo de la base de datos del sistema. 
                        Este proceso puede tomar varios minutos dependiendo del tamaño de los datos.
                    </p>
                </div>

                <!-- Warning Alert -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <Icon :path="mdiAlert" class="text-amber-500 mr-3 flex-shrink-0" size="20" />
                        <div>
                            <h4 class="text-amber-800 font-semibold">Importante</h4>
                            <ul class="text-amber-700 text-sm mt-2 list-disc list-inside space-y-1">
                                <li>El proceso de respaldo puede afectar el rendimiento del sistema</li>
                                <li>No cierre esta ventana durante el proceso</li>
                                <li>Se recomienda realizar respaldos durante horas de menor actividad</li>
                                <li>El respaldo se almacenará en el servidor y podrá descargarse posteriormente</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleSubmit">
                    <BaseFormField 
                        type="textarea"
                        label="Descripción del Respaldo (Opcional)"
                        v-model="createForm.description"
                        placeholder="Ej: Respaldo antes de actualización del sistema..."
                        :error="createForm.errors.description"
                        :maxLength="500"
                        h="h-24"
                    />
                    <p class="text-sm text-gray-500 mt-2">
                        Agregue una descripción para identificar fácilmente este respaldo más tarde.
                    </p>

                    <!-- Current System Info -->
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-slate-800 mb-3 flex items-center">
                            <Icon :path="mdiInformation" class="mr-2 text-slate-600" size="20" />
                            Información del Sistema
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600">Base de Datos:</span>
                                <span class="ml-2 font-medium text-slate-800">{{ dbInfo.database || 'DiabCare' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-600">Fecha Actual:</span>
                                <span class="ml-2 font-medium text-slate-800">{{ currentDate }}</span>
                            </div>
                            <div>
                                <span class="text-slate-600">Usuario:</span>
                                <span class="ml-2 font-medium text-slate-800">{{ $page.props.auth.user.name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-600">Tipo:</span>
                                <span class="ml-2 font-medium text-slate-800">Respaldo Completo</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <BaseButton
                            :routeName="`${routeName}index`"
                            color="light"
                            :icon="mdiArrowLeft"
                            label="Cancelar"
                        />

                        <BaseButton
                            type="submit"
                            color="success"
                            :icon="mdiDatabase"
                            label="Crear Respaldo"
                            :loading="createForm.processing || isLoading"
                            :disabled="createForm.processing || isLoading"
                        />
                    </div>
                </form>

                <!-- Progress Indicator -->
                <div v-if="createForm.processing || isLoading" class="mt-6">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-2 border-indigo-400 border-t-transparent mr-3"></div>
                            <div>
                                <h4 class="font-semibold text-indigo-800">Creando Respaldo...</h4>
                                <p class="text-indigo-700 text-sm">Por favor, no cierre esta ventana.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardBox>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardBox from '@/Components/CardBox.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import Icon from '@/Components/Icon.vue';
import {
    mdiDatabase,
    mdiAlert,
    mdiTextBox,
    mdiInformation,
    mdiArrowLeft
} from '@mdi/js';
import { useBackup } from '../Composables/useBackup';

const props = defineProps({
    title: {
        type: String,
        default: 'Crear Respaldo'
    },
    routeName: {
        type: String,
        default: 'backups.'
    }
});

const { createForm, createBackup, isLoading } = useBackup(props);

// System information
const dbInfo = {
    database: 'DiabCare' // This could be passed from backend
};

const currentDate = computed(() => {
    return new Date().toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
});

const handleSubmit = () => {
    createBackup();
};
</script>