<template>
    <CardSection :hasDivider="hasDivider">
        <template #heading>
            <div class="flex justify-between">
                <div class="flex flex-row items-center gap-2">

                    <div>
                        <h3 class="text-base text-forest-400 font-semibold">{{ title }}</h3>
                        <span class="text-sm text-gray-500">{{ description }}</span>
                    </div>
                </div>

                <!-- <button @click="triggerFileInput" class="rounded relative m-0 px-3 py-2" -->
                <div class="flex justify-center items-center">
                    <button
                        class="flex transition-colors border focus:outline-none items-center relative rounded h-10 px-2 py-1 m-0"
                        :class="getButtonColor('info', false, true)">
                        <BaseIcon :path="mdiPlus" />
                        <span class="text-sm">Subir archivos</span>
                        <input ref="FileInput" class="m-0 p-0 absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            accept=".pdf,.docx" multiple @change="handleFileInput" type="file">
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <div v-if="totalFiles > 0" class="space-y-4">
                <Badge variant="soft">
                    {{ fileCountText }}
                </Badge>

                <FileForm @open-modal="openModal" @remove-file="removeFile(index)" v-for="(file, index) in form.files"
                    :key="`file-${index}`" :index="index" :file="file" :errors="form.errors" />
            </div>

            <CardBoxComponentEmpty v-else />

            <InputError :message="form.errors.files" />
        </div>
    </CardSection>

    <FileModal @close="closeModal" :showModal="showFile" :file="selectedFile" />
</template>

<script setup>
import Badge from './Badge.vue';
import BaseIcon from './BaseIcon.vue';
import CardBoxComponentEmpty from './CardBoxComponentEmpty.vue';
import CardSection from './CardSection.vue';
import FileForm from './FileForm.vue';
import FileModal from './FileModal.vue';
import InputError from './InputError.vue';
import { useFile, useModal } from '@/Hooks/useFile';
import { getButtonColor } from '@/colors.js';
import { mdiFileDocumentOutline, mdiPlus } from '@mdi/js';

const { form } = defineProps({
    form: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: 'Archivos adjuntos',
    },
    hasDivider: {
        type: Boolean,
        default: true,
    },
    iconClasses: {
        type: String,
        default: 'bg-forest-400 text-white',
    }
});

const { showFile, selectedFile, openModal, closeModal } = useModal();
const { fileCountText, totalFiles, handleFileInput, removeFile } = useFile(form);
</script>