<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Backups -->
        <CardBox class="bg-blue-50 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-700">{{ stats.total_backups || 0 }}</div>
                    <div class="text-blue-600">Total de Respaldos</div>
                </div>
                <Icon :path="mdiDatabase" class="text-blue-400" size="32" />
            </div>
        </CardBox>

        <!-- Completed Backups -->
        <CardBox class="bg-green-50 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-700">{{ stats.completed_backups || 0 }}</div>
                    <div class="text-green-600">Completados</div>
                </div>
                <Icon :path="mdiCheckCircle" class="text-green-400" size="32" />
            </div>
        </CardBox>

        <!-- Failed Backups -->
        <CardBox class="bg-red-50 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-red-700">{{ stats.failed_backups || 0 }}</div>
                    <div class="text-red-600">Fallidos</div>
                </div>
                <Icon :path="mdiAlertCircle" class="text-red-400" size="32" />
            </div>
        </CardBox>

        <!-- Storage Used -->
        <CardBox class="bg-purple-50 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-700">{{ formatFileSize(stats.total_size) }}</div>
                    <div class="text-purple-600">Almacenamiento</div>
                </div>
                <Icon :path="mdiHarddisk" class="text-purple-400" size="32" />
            </div>
        </CardBox>
    </div>

    <!-- Latest Backup Info -->
    <CardBox v-if="stats.latest_backup" class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Último Respaldo</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <div><strong>Archivo:</strong> {{ stats.latest_backup.filename }}</div>
                    <div><strong>Fecha:</strong> {{ formatDate(stats.latest_backup.created_at) }}</div>
                    <div><strong>Tamaño:</strong> {{ formatFileSize(stats.latest_backup.size) }}</div>
                </div>
            </div>
            <div class="flex gap-2">
                <BaseButton
                    color="info"
                    :icon="mdiDownload"
                    label="Descargar"
                    small
                    @click="downloadBackup(stats.latest_backup.id)"
                />
                <BaseButton
                    color="success"
                    :icon="mdiHistory"
                    label="Restaurar"
                    small
                    :routeName="`${routeName}restore`"
                    :parameter="stats.latest_backup.id"
                />
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Icon from '@/Components/Icon.vue';
import {
    mdiDatabase,
    mdiCheckCircle,
    mdiAlertCircle,
    mdiHarddisk,
    mdiDownload,
    mdiHistory
} from '@mdi/js';
import { useBackup } from '../Composables/useBackup';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'backups.'
    }
});

const { downloadBackup, formatFileSize, formatDate } = useBackup(props);
</script>