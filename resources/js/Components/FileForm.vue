<template>
    <CardBox rounded="rounded-md" bg="bg-white shadow-xs border border-gray-100 dark:border-gray-600">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-shrink-0">
                <div class="relative">
                    <BaseIcon @click="$emit('openModal', file)"
                        class="text-white bg-wine-100 rounded-lg cursor-pointer" h="h-14" w="w-14" size="32"
                        :path="mdiFileDocumentOutline" />

                    <BaseButton @click="$emit('removeFile')"
                        class="absolute -top-3 -right-4 md:-right-2 h-6 w-6 rounded-full p-0" :icon="mdiClose"
                        color="danger" roundedFull small />
                </div>
                <InputError class="md:w-24" :message="getErrors('file', index)" />
            </div>
            <div class="w-full">
                <BaseFormField type="input" label="Titulo" v-model="file.title" :error="getErrors('title', index)"
                placeholder="Ejm: Ingerir frutas y verduras" required :maxLength="100" />

                <BaseFormField type="textarea" label="Descripción" v-model="file.description" :error="getErrors('description', index)"
                placeholder="Ingresa la descripción" required :maxLength="1000" />
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import BaseButton from '@/Components/BaseButton.vue';
import BaseIcon from './BaseIcon.vue';
import CardBox from '@/Components/CardBox.vue';
import InputError from './InputError.vue';
import { mdiClose, mdiFileDocumentOutline } from '@mdi/js';
import BaseFormField from '@/Components/BaseFormField.vue';

const props = defineProps({
    index: { type: Number, required: true },
    errors: { type: Object, required: true },
    file: { type: Object, required: true },
});

const getErrors = (field, index) => {
    return props.errors[`files.${index}.${field}`];
};

</script>