<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiSend } from '@mdi/js';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificación de correo" />
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
                        <h2 class="text-2xl font-bold text-medic-700 mb-2">Verifica tu correo electrónico</h2>
                        <p class="text-sm text-gray-600">
                            Gracias por registrarte. Antes de continuar, por favor verifica tu dirección de correo haciendo clic en el enlace que te enviamos. Si no recibiste el correo, puedes solicitar otro.
                        </p>
                    </div>
                    <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600 text-center">
                        Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                    </div>
                </template>
                <template #footer>
                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-center sm:items-center">
                            <BaseButton
                            type="submit"
                            color="info"
                            :icon="mdiSend"
                            label="Reenviar correo de verificación"
                            title="Reenviar correo de verificación"
                            :disabled="form.processing"
                            :processing="form.processing"
                            />
                    </div>
                        <div class="flex justify-center mt-2">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="rounded-md text-sm text-gray-600 underline hover:text-medic-700 focus:outline-none focus:ring-2 focus:ring-medic-300 focus:ring-offset-2"
                            >
                                Cerrar sesión
                            </Link>
                        </div>
                </template>
            </CardBox>
        </div>
    </GuestLayout>
</template>
