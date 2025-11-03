<template>
    <div class="w-full mx-auto p-4 space-y-6">
        <div class="relative">
            <input ref="fileInput" type="file" multiple accept="image/*" @change="handleFileInput" class="hidden" :disabled="disabled"/>

            <div @click="$refs.fileInput.click()" @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false" @drop.prevent="handleFileInput" :class="[
                    'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all duration-200',
                    isDragging
                        ? 'border-blue-500 bg-blue-50'
                        : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50'
                ]">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <BaseIcon :path="mdiPlus" size="24" h="h-auto" w="w-auto" class="text-gray-500" />
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-700">
                            {{ totalFiles === 0 ? 'Selecciona o arrastra imágenes aquí' : 'Agregar más imágenes' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            PNG, JPG, GIF hasta 5MB cada una
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="totalFiles > 0" class="flex justify-between items-center">
            <Badge size="md">
                {{ fileCountText }}
            </Badge>
            <button @click="removeAllFiles" class="text-sm text-red-600 hover:opacity-80 font-medium cursor-pointer">
                Eliminar todas
            </button>
        </div>

        <swiper v-if="totalFiles > 0" :spaceBetween="10" :slidesPerView="3" :autoplay="{
            delay: 2500,
            disableOnInteraction: false,
        }" :pagination="{ clickable: true }" :navigation="true" :modules="[Pagination, Navigation]" :breakpoints="{
            '@0.00': { slidesPerView: 1, spaceBetween: 10 },
            '@0.75': { slidesPerView: 2, spaceBetween: 20 },
            '@1.00': { slidesPerView: 3, spaceBetween: 40 },
        }" class="mySwiper">
            <swiper-slide v-for="(photo, index) in form.photos" :key="index" class="pb-8">
                <div class="relative group w-full h-64 md:h-72 lg:h-80 overflow-hidden rounded-lg shadow"
                    :class="{ 'border-4 border-red-600': form.errors[`photos.${index}.title`] }">
                    <img :src="photo.path" :alt="`Imagen ${index + 1}`"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    <div
                        class="absolute inset-0 bg-black/50 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-4">
                        <BaseButton :icon="mdiTrashCan" color="danger" @click="removeFile(index)"
                            title="Eliminar imagen" />
                        <BaseButton :icon="mdiCommentText" color="whiteDark" @click="openModal(photo)"
                            title="Descripción" />
                    </div>
                    <div class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                        {{ index + 1 }}/{{ totalFiles }}
                    </div>
                    <div class="absolute bottom-0 left-0 w-full bg-black/40 text-white p-2 text-xs sm:text-sm">
                        <p class="font-semibold truncate"
                            :class="{ 'text-red-600': form.errors[`photos.${index}.title`] }">
                            {{ photo.title }}
                        </p>
                        <p class="opacity-80 truncate"
                            :class="{ 'text-red-600': form.errors[`photos.${index}.description`] }">
                            {{ photo.description ?? 'Sin descripción' }}
                        </p>
                    </div>
                </div>
            </swiper-slide>
        </swiper>
    </div>
    <DialogModal :show="showFile" :closeable="true" @close="closeModal">
        <template #title>
            <SectionTitleLineWithButton title="Configurar descripción" :hisBreadCrumb="false"
                :icon="mdiTextBoxOutline" />
        </template>
        <template #content>
            <BaseFormField type="input" label="Título" v-model="selectedFile.title"
                :error="form.errors[`photos.${selectedFile.index}.title`]" placeholder="Ingresa un titulo" required />

            <BaseFormField type="textarea" label="Descripción" v-model="selectedFile.description"
                :error="form.errors[`photos.${selectedFile.index}.description`]" placeholder="Ingresa una descripción"
                height="h-24" :maxLength="150" />
        </template>
        <template #footer>
            <div class="flex justify-end gap-2">
                <BaseButton label="Aceptar" color="lightDark" @click="closeModal" :icon="mdiCheck" />
            </div>
        </template>
    </DialogModal>
</template>

<script setup>
import { mdiCheck, mdiCommentText, mdiPlus, mdiTextBoxOutline, mdiTrashCan } from '@mdi/js'
import BaseIcon from './BaseIcon.vue'
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import DialogModal from './DialogModal.vue';
import BaseButton from './BaseButton.vue';
import SectionTitleLineWithButton from './SectionTitleLineWithButton.vue';
import Badge from './Badge.vue';
import { useComments, useImage } from '@/Hooks/useImage';
import BaseFormField from './BaseFormField.vue';

const { form } = defineProps({
    form: {
        type: Object,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const { showFile, selectedFile, openModal, closeModal, } = useComments();
const { isDragging, fileCountText, totalFiles, handleFileInput, removeFile, removeAllFiles } = useImage(form, 'photos', 'imagene');
</script>