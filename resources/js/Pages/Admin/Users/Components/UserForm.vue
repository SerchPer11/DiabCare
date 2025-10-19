<template>
    <CardBox class="mt-2" isForm>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField type="input" label="Nombre del usuario" v-model="form.name" :error="form.errors.name"
                placeholder="Asigna un nombre" required :maxLength="100" />

            <BaseFormField type="input" label="Correo electrónico" v-model="form.email" :error="form.errors.email"
                placeholder="Ejemplo: usuario@ejemplo.com" required :maxLength="100" />

            <BaseFormField type="password" label="Contraseña" v-model="form.password" :error="form.errors.password"
                placeholder="Dejar en blanco para mantener la contraseña actual" :maxLength="100" />

            <BaseFormField type="password" label="Confirmar Contraseña" v-model="form.password_confirmation"
                :error="form.errors.password_confirmation"
                placeholder="Dejar en blanco para mantener la contraseña actual" :maxLength="100" />
        </div>
        <MultiCheck v-model="form.roles" label="Rol(es) Asignados" :options="roles" :error="form.errors.roles"
            optionValue="id" optionLabel="name" optionKey="id" />
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import MultiCheck from '@/Components/MultiCheck.vue';


const form = inject('form');
const roles = inject('roles');

</script>