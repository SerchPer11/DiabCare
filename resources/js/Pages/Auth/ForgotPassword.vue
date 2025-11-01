<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiLockReset } from '@mdi/js';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Restablecer contraseña" />

        <div class="flex justify-center items-center min-h-[60vh]">
            <CardBox
                class="w-full max-w-md"
                :isForm="true"
                :hasBorder="true"
                :rounded="'rounded-2xl'"
                :padding="'p-6'"
                :bg="'bg-white'"
                @submit.prevent="submit"
            >
                <template #default>
                    <div class="mb-6 text-center">
                        <h2 class="text-2xl font-bold text-medic-700 mb-2">¿Olvidaste tu contraseña?</h2>
                        <p class="text-sm text-gray-600">
                            No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                        </p>
                    </div>

                    <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
                        {{ status }}
                    </div>

                    <div class="mb-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="ejemplo@correo.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                </template>
                <template #footer>
                    <div class="flex items-center justify-center">
                        <BaseButton
                        type="submit"
                        color="info"
                        :icon="mdiLockReset"
                        label="Restablecer contraseña"
                        title="Restablecer contraseña"
                        :disabled="form.processing"
                        :processing="form.processing"
                        />
                    </div>
                </template>
            </CardBox>
        </div>
    </GuestLayout>
</template>
