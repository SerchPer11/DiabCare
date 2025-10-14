<template>
  <AuthenticatedLayout>
    <CrudHead :title="title" />
    <CrudBanner :title="title" :routeName="routeName" :icon="mdiViewModule" />

    <ModuleForm />

    <CrudButtons>
        <div class="flex gap-2">
            <BaseButton :icon="mdiClose " color="whiteDark" label="Cancelar" />
            <BaseButton :icon="mdiSend " color="info" label="Guardar" />
        </div>
    </CrudButtons>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudButtons from '@/Components/CrudButtons.vue';
import ModuleForm from '../Components/ModuleForm.vue';
import { useModule } from '../Composables/useModule';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiViewModule } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiClose, mdiSend } from '@mdi/js';


const props = defineProps({
  title: {
    type: String,
    default: 'Crear Módulo',
  },
  routeName: {
    type: String,
    default: 'modules.',
  },
});

useModule(props);
</script>