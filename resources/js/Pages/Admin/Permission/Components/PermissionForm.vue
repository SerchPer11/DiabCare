<template>
  <CardBox class="mt-2" isForm>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <BaseFormField type="input" label="Nombre del permiso" v-model="form.name" :error="form.errors.name"
        placeholder="Ejemplo: patient.create" required :maxLength="100" />

        <BaseFormField class="mb-4" type="select" label="Módulo" v-model="form.module_key" :error="form.errors.module_key"
        placeholder="Selecciona un modulo" required :options="modules" valueSelect="key" />
    </div>

    

    <BaseFormField type="textarea" label="Descripción del módulo" v-model="form.description"
      :error="form.errors.description" placeholder="Describe brevemente el módulo" h="h-32" :maxLength="600" required />

  </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';


const form = inject('form');
const modules = inject('modules');
</script>