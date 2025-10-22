<template>
    <CardBox class="mt-2" isForm>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseFormField type="input" label="Nombre" v-model="form.name" :error="form.errors.name"
                placeholder="Ingrese su nombre" required :maxLength="100" />

            <BaseFormField type="input" label="Apellido" v-model="form.last_name" :error="form.errors.last_name"
                placeholder="Ingrese su apellido" required :maxLength="100" />

            <BaseFormField type="input" label="Correo electrónico" v-model="form.email" :error="form.errors.email"
                placeholder="Ingrese su correo electrónico" required :maxLength="100" :disabled="true" />

            <BaseFormField type="input" label="Teléfono" v-model="form.phone" :error="form.errors.phone"
                placeholder="Ingrese su teléfono" required :maxLength="13" />

            <BaseFormField type="select" label="Género" v-model="form.gender" :error="form.errors.gender"
                placeholder="Seleccione su género" required valueSelect="value" :options="genders" />

            <BaseFormField type="select" label="Especialidad" v-model="form.specialty_id"
                :error="form.errors.specialty_id" placeholder="Seleccione su especialidad" required
                :options="specialties" />

            <BaseFormField type="input" label="Número de licencia" v-model="form.license_number"
                :error="form.errors.licence_number" placeholder="Ingrese su número de licencia" required
                :maxLength="50" />

            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <BaseFormField type="date" label="Fecha de titulación" v-model="form.titulation_date"
                    :error="form.errors.titulation_date" required />

                <BaseFormField type="date" label="Fecha de nacimiento" v-model="form.birthdate"
                    :error="form.errors.birthdate" required />
            </div>
        </div>
        <CardBox class="mt-4">
            <Label class="block text-md font-medium text-gray-700 mb-2 mt-2">Actualización de Contraseña</Label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                <BaseFormField type="password" label="Contraseña Actual" v-model="form.current_password"
                    :error="form.errors.current_password" placeholder="Ingrese su contraseña actual" :maxLength="100" />
                <BaseFormField type="password" label="Nueva Contraseña" v-model="form.password"
                    :error="form.errors.password" placeholder="Ingrese su nueva contraseña" :maxLength="100" />
                <BaseFormField type="password" label="Confirmar Contraseña" v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation" placeholder="Confirme su nueva contraseña"
                    :maxLength="100" />
            </div>
        </CardBox>
    </CardBox>
</template>

<script setup>
import BaseFormField from '@/Components/BaseFormField.vue';
import { inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import Label from '@/Components/ui/label/Label.vue';


const form = inject('form');
const specialties = inject('specialties');

const genders = [
    { id: '1', name: 'Masculino', value: 'male' },
    { id: '2', name: 'Femenino', value: 'female' },
    { id: '3', name: 'Otro', value: 'other' },
];
</script>