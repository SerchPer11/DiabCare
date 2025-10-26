<template>
    <Modal :show="showModal" :closeable="true" max-width="5xl">
        <iframe v-if="file && file.path" class="w-full h-[75vh]" :src="file.path" />
        <CardBoxComponentEmpty v-else />
    </Modal>
</template>

<script setup>
import CardBoxComponentEmpty from './CardBoxComponentEmpty.vue';
import Modal from './Modal.vue';

const props = defineProps({
    file: {
        type: Object,
        required: false,
    },
    showModal: {
        type: Boolean,
        required: true,
    }
});
</script>