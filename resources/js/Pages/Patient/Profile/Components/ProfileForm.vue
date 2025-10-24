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

            <BaseFormField type="date" label="Fecha de nacimiento" v-model="form.birthdate"
                :error="form.errors.birthdate" required />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <BaseFormField type="select" label="Tipo de sangre" v-model="form.blood_type"
                :error="form.errors.blood_type" placeholder="Seleccione su tipo de sangre" required valueSelect="value"
                :options="bloodTypeOptions" />

            <BaseFormField type="number" label="Altura (cm)" v-model="form.height" :error="form.errors.height"
                placeholder="Ejm: 170" required />
            <BaseFormField type="number" label="Peso (kg)" v-model="form.weight" :error="form.errors.weight"
                placeholder="Ejm: 70.5" required />
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

const genders = [
    { id: '1', name: 'Masculino', value: 'male' },
    { id: '2', name: 'Femenino', value: 'female' },
    { id: '3', name: 'Otro', value: 'other' },
];

const bloodTypeOptions = [
    { id: '1', name: 'A+', value: 'A+' },
    { id: '2', name: 'A-', value: 'A-' },
    { id: '3', name: 'B+', value: 'B+' },
    { id: '4', name: 'B-', value: 'B-' },
    { id: '5', name: 'AB+', value: 'AB+' },
    { id: '6', name: 'AB-', value: 'AB-' },
    { id: '7', name: 'O+', value: 'O+' },
    { id: '8', name: 'O-', value: 'O-' },
];
</script>