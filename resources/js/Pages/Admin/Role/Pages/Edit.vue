<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudBanner :title="title" :routeName="routeName" :icon="mdiCardAccountDetails" main />

        <RoleForm />

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
import RoleForm from '../Components/RoleForm.vue';
import { useRole } from '../Composables/useRole';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiCardAccountDetails } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend, mdiDelete } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Editar Rol',
    },
    routeName: {
        type: String,
        default: 'roles.',
    },
    permissions: {
        type: Object,
        required: true,
    },
    modules: {
        type: Object,
        required: true,
    },
    role: {
        type: Object,
        required: true,
    },
});

const { updateForm, destroyForm, processing } = useRole(props);
</script>