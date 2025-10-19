<template>
    <CardBox class="mt-2" isForm>

        <BaseFormField type="input" label="Nombre del rol" v-model="form.name" :error="form.errors.name"
            placeholder="Ejemplo: Administrador/Medico/Paciente" required :maxLength="100" />

        <BaseFormField type="textarea" label="Descripción del rol" v-model="form.description"
            :error="form.errors.description" placeholder="Describe brevemente el rol" h="h-32" :maxLength="600"
            required />

        <MultiselectGroup v-model="form.permissions" title="Asignar Permisos" placeholderSelect="Selecciona un módulo" :groups="modules" :items="permissions"
            groupValueKey="key" groupLabelKey="name" itemValueKey="id" itemLabelKey="description" />
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import MultiselectGroup from '@/Components/MultiselectGroup.vue';


const form = inject('form');
const permissions = inject('permissions');
const modules = inject('modules');
</script>