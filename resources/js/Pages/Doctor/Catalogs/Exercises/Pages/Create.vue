<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiDumbbell" />

        <ExerciseForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiSend" color="info" label="Guardar" @click="saveForm" :processing="processing" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import ExerciseForm from '../Components/ExerciseForm.vue';
import { useExercise } from '../Composables/useExercise';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiDumbbell } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend } from '@mdi/js';
import { provide, inject } from 'vue';
import BaseFormField from '@/Components/BaseFormField.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Agregar Ejercicio',
    },
    routeName: {
        type: String,
        default: 'doctor.catalogs.exercises.',
    },
    exerciseTypes: {
        type: Object,
        required: true,
    },
});

const { saveForm, processing } = useExercise(props);
provide('exerciseTypes', props.exerciseTypes);
</script>
