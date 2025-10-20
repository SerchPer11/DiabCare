<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiDumbbell" />

        <ExerciseForm />

        <CrudButtons>
            <div class="flex gap-2 justify-end">
                <BaseButton :icon="mdiClose" color="whiteDark" label="Cancelar" variant="outline"
                    :routeName="`${routeName}index`" />
                <BaseButton :icon="mdiDelete" color="danger" label="Eliminar" @click="destroyForm" :processing="processing" />
                <BaseButton :icon="mdiSend" color="info" label="Guardar" @click="updateForm" :processing="processing" />
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
import { mdiClose, mdiSend, mdiDelete } from '@mdi/js';
import { provide } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Editar Ejercicio',
    },
    routeName: {
        type: String,
        default: 'doctor.catalogs.exercises.',
    },
    exerciseTypes: {
        type: Object,
        required: true,
    },
    exercise: {
        type: Object,
        required: true,
    },
});

const { updateForm, destroyForm, processing } = useExercise(props);
provide('exerciseTypes', props.exerciseTypes);
</script>