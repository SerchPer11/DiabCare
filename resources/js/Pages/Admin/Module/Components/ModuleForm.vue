<template>
  <CardBox class="mt-2" isForm>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <BaseFormField type="input" label="Nombre del módulo" v-model="form.name" :error="form.errors.name"
        placeholder="Asigna un nombre al módulo" required :maxLength="100" />
      <BaseFormField type="input" label="Clave del módulo" v-model="form.key" :error="form.errors.key"
        placeholder="Asigna una clave al módulo" required :maxLength="100" />
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

const pruebas =[
  { id: 1, name: 'Opción 1' },
  { id: 2, name: 'Opción 2' },
  { id: 3, name: 'Opción 3' },
];
</script>