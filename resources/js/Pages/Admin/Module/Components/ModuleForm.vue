<template>
  <CardBox class="mt-2" isForm>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <BaseFormField type="input" label="Nombre del módulo" v-model="form.name" :error="form.errors.name"
        placeholder="Modulo de servicios" required :maxLength="100"/>
      <BaseFormField type="input" label="Clave del módulo" v-model="form.key" :error="form.errors.key"
        placeholder="services" required :maxLength="100"/>
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
</script>