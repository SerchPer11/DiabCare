<template>
  <AuthenticatedLayout>
    <CrudHead :title="title" />
    <CrudBanner :title="title" :routeName="routeName" :icon="mdiKey" />

    <PermissionForm />

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
import PermissionForm from '../Components/PermissionForm.vue';
import { usePermission } from '../Composables/usePermission';
import CrudBanner from '@/Components/CrudBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiKey } from '@mdi/js';
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
  modules: {
    type: Object,
    required: true,
  },
});

const { saveForm, processing } = usePermission(props);
</script>