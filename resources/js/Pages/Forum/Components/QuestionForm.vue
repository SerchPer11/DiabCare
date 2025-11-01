<template>
    <CardBox class="mt-4" isForm>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseFormField type="input" label="Título" required v-model="form.title" :error="form.errors.title"
            placeholder="Asigna un título." :maxLength="150" />
            <BaseFormField type="select" label="Categoría" required v-model="form.category_id"
                :error="form.errors.category_id" placeholder="Selecciona una categoría" :options="categories" />
        </div>
        <BaseFormField type="textarea" label="Contenido" required v-model="form.content" :error="form.errors.content"
            placeholder="Describe tu duda o situación de forma respetuosa." :maxLength="1000" h="h-24" class="mt-4" />

        <div class="flex justify-end mt-4">

            <BaseButton color="info" :icon="mdiPlus" label="Hacer una nueva pregunta" title="Agregar usuario"
                @click="saveForm" :processing="processing" />
        </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import { mdiPlus } from '@mdi/js';
import { inject } from 'vue';
import { useForum } from '../Composables/useForum.js';

const props = defineProps({
    routeName: {
        type: String,
        default: 'doctor.questions.'
    }
});
const categories = inject('categories');

const { form, processing, saveForm } = useForum(props);
</script>