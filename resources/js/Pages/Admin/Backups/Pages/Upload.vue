<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiCloudUpload" main />

        <CardBox>
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <Icon :path="mdiCloudUpload" class="mx-auto text-blue-500 mb-4" size="64" />
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Subir Respaldo Personalizado</h2>
                    <p class="text-gray-600">
                        Sube un archivo de respaldo SQL que se haya creado externamente.
                    </p>
                </div>

                <!-- Important Notes -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <Icon :path="mdiInformationOutline" class="text-amber-500 mr-3 flex-shrink-0" size="24" />
                        <div>
                            <h4 class="text-amber-800 font-bold">Requisitos del Archivo</h4>
                            <ul class="text-amber-700 mt-2 list-disc list-inside space-y-1 text-sm">
                                <li>El archivo debe ser un respaldo SQL válido (.sql)</li>
                                <li>Tamaño máximo: 500MB</li>
                                <li>Debe ser compatible con MySQL/MariaDB</li>
                                <li><strong>Solo respaldos de DiabCare son aceptados</strong></li>
                                <li>Se verificará la compatibilidad de las tablas</li>
                                <li>Se calculará automáticamente el checksum SHA256</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Compatibility Warning -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <Icon :path="mdiAlertOctagon" class="text-red-500 mr-3 flex-shrink-0" size="24" />
                        <div>
                            <h4 class="text-red-800 font-bold">⚠️ Advertencia de Compatibilidad</h4>
                            <ul class="text-red-700 mt-2 list-disc list-inside space-y-1 text-sm">
                                <li><strong>NO subas respaldos de otras aplicaciones</strong></li>
                                <li>Solo archivos SQL generados por DiabCare son seguros</li>
                                <li>Respaldos incompatibles pueden corromper tu sistema</li>
                                <li>Se verifican las tablas: users, modules, permissions, roles, patient_profiles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form @submit.prevent="handleUpload" class="space-y-6">
                    <!-- File Upload Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors">
                        <div v-if="!selectedFile">
                            <Icon :path="mdiFileUpload" class="mx-auto text-gray-400 mb-4" size="48" />
                            <div class="space-y-2">
                                <p class="text-gray-600 font-medium">
                                    Arrastra tu archivo aquí o haz clic para seleccionar
                                </p>
                                <p class="text-gray-500 text-sm">
                                    Archivos SQL hasta 500MB
                                </p>
                            </div>
                            <input
                                ref="fileInput"
                                type="file"
                                accept=".sql"
                                @change="handleFileSelect"
                                class="hidden"
                            />
                            <BaseButton
                                type="button"
                                color="info"
                                :icon="mdiFileUpload"
                                label="Seleccionar Archivo"
                                @click="$refs.fileInput.click()"
                                class="mt-4"
                            />
                        </div>

                        <!-- Selected File Info -->
                        <div v-else class="space-y-4">
                            <div class="flex items-center justify-center space-x-3">
                                <Icon :path="mdiFileCheck" class="text-green-500" size="32" />
                                <div class="text-left">
                                    <p class="font-medium text-gray-800">{{ selectedFile.name }}</p>
                                    <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 justify-center">
                                <BaseButton
                                    type="button"
                                    color="light"
                                    :icon="mdiClose"
                                    label="Cambiar"
                                    @click="clearFile"
                                />
                                <BaseButton
                                    v-if="!validating"
                                    type="button"
                                    color="info"
                                    :icon="mdiShieldCheck"
                                    label="Validar Archivo"
                                    @click="validateFile"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Validation Results -->
                    <div v-if="validation.checked" class="rounded-lg p-4" :class="validation.valid ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                        <div class="flex items-center">
                            <Icon 
                                :path="validation.valid ? mdiCheckCircle : mdiAlertCircle"
                                :class="validation.valid ? 'text-green-500' : 'text-red-500'"
                                class="mr-3"
                                size="24"
                            />
                            <div>
                                <h4 :class="validation.valid ? 'text-green-800' : 'text-red-800'" class="font-semibold">
                                    {{ validation.valid ? '✅ Archivo Válido' : '❌ Archivo Inválido' }}
                                </h4>
                                <p :class="validation.valid ? 'text-green-700' : 'text-red-700'" class="text-sm mt-1">
                                    {{ validation.message }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <BaseFormField
                        type="textarea"
                        label="Descripción del Respaldo"
                        placeholder="Descripción opcional del respaldo (ej: Respaldo antes de migración, Respaldo de producción del 01/11/2025, etc.)"
                        v-model="uploadForm.description"
                        :error="uploadForm.errors.description"
                        rows="3"
                    />

                    <!-- Upload Progress -->
                    <div v-if="uploading" class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Subiendo archivo...</span>
                            <span class="text-sm text-gray-600">{{ uploadProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-blue-500 h-2 rounded-full transition-all duration-300" 
                                :style="{ width: uploadProgress + '%' }"
                            ></div>
                        </div>
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
                            color="success"
                            :icon="mdiCloudUpload"
                            label="Subir Respaldo"
                            :loading="uploadForm.processing || uploading"
                            :disabled="!canUpload || uploadForm.processing || uploading"
                        />
                    </div>
                </form>
            </div>
        </CardBox>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardBox from '@/Components/CardBox.vue';
import CrudHead from '@/Components/CrudHead.vue';
import CrudBanner from '@/Components/CrudBanner.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import Icon from '@/Components/Icon.vue';
import {
    mdiCloudUpload,
    mdiFileUpload,
    mdiFileCheck,
    mdiClose,
    mdiShieldCheck,
    mdiCheckCircle,
    mdiAlertCircle,
    mdiAlertOctagon,
    mdiInformationOutline,
    mdiArrowLeft
} from '@mdi/js';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Subir Respaldo'
    },
    routeName: {
        type: String,
        default: 'backups.'
    }
});

// File handling
const selectedFile = ref(null);
const fileInput = ref(null);
const uploading = ref(false);
const uploadProgress = ref(0);
const validating = ref(false);

// Validation state
const validation = ref({
    checked: false,
    valid: false,
    message: ''
});

// Form
const uploadForm = useForm({
    file: null,
    description: ''
});

// Computed properties
const canUpload = computed(() => {
    return selectedFile.value && 
           validation.value.checked && 
           validation.value.valid && 
           !uploading.value;
});

// Methods
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type and size
        if (!file.name.toLowerCase().endsWith('.sql')) {
            alert('Por favor selecciona un archivo SQL válido (.sql)');
            return;
        }

        if (file.size > 500 * 1024 * 1024) { // 500MB
            alert('El archivo es demasiado grande. Máximo 500MB permitido.');
            return;
        }

        selectedFile.value = file;
        uploadForm.file = file;
        
        // Reset validation
        validation.value = {
            checked: false,
            valid: false,
            message: ''
        };
    }
};

const clearFile = () => {
    selectedFile.value = null;
    uploadForm.file = null;
    validation.value = {
        checked: false,
        valid: false,
        message: ''
    };
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const validateFile = async () => {
    if (!selectedFile.value) return;

    validating.value = true;
    
    try {
        // Read first few lines of the file to validate it's a SQL dump
        const text = await readFileHeader(selectedFile.value, 2048);
        
        // Basic SQL file validation
        const sqlKeywords = [
            'CREATE TABLE',
            'INSERT INTO',
            'DROP TABLE',
            'USE ',
            'SET ',
            'mysqldump',
            'MySQL dump',
            'MariaDB dump'
        ];

        const hasValidSql = sqlKeywords.some(keyword => 
            text.toUpperCase().includes(keyword.toUpperCase())
        );

        if (hasValidSql) {
            validation.value = {
                checked: true,
                valid: true,
                message: 'El archivo parece ser un respaldo SQL válido'
            };
        } else {
            validation.value = {
                checked: true,
                valid: false,
                message: 'El archivo no parece ser un respaldo SQL válido'
            };
        }
    } catch (error) {
        validation.value = {
            checked: true,
            valid: false,
            message: 'Error al validar el archivo: ' + error.message
        };
    } finally {
        validating.value = false;
    }
};

const readFileHeader = (file, bytes = 1024) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.onerror = (e) => reject(new Error('Error reading file'));
        reader.readAsText(file.slice(0, bytes));
    });
};

const handleUpload = () => {
    if (!canUpload.value) return;

    uploading.value = true;
    uploadProgress.value = 0;

    uploadForm.post(route(`${props.routeName}upload`), {
        forceFormData: true,
        onProgress: (progress) => {
            uploadProgress.value = Math.round(progress.percentage || 0);
        },
        onSuccess: () => {
            uploading.value = false;
            uploadProgress.value = 100;
        },
        onError: () => {
            uploading.value = false;
            uploadProgress.value = 0;
        },
        onFinish: () => {
            uploading.value = false;
        }
    });
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>