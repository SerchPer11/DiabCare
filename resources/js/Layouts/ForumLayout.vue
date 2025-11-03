<script setup>
import { mdiViewDashboard, mdiAccountPlus, mdiLogin } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const nav = [
    { label: 'Módulos', href: '/home#modules' },
    { label: 'Foro', href: '/forum' }
];

const page = usePage();
</script>

<template>
    <div class="relative flex min-h-screen w-full">
        <div class="flex flex-col flex-1 w-full">

            <header
                class="sticky top-0 z-10 flex justify-between md:grid md:grid-cols-3 items-center gap-4 border-b px-4 shadow-md py-3">

                <Link class="flex items-center gap-3 md:ml-40" :href="route('home')">
                <img src="/logoDiabCare.png" alt="DiabCare" class="w-8 h-8" />
                <span class="font-extrabold text-medic-700">DiabCare</span>
                </Link>

                <nav class="hidden md:flex md:justify-self-center items-center gap-6">
                    <Link v-for="item in nav" :key="item.href" :href="item.href"
                        class="text-sm font-medium text-medic-600 hover:text-medic-700">{{ item.label }}</Link>
                </nav>

                <div class="md:justify-self-end">
                    <div class="gap-2 flex flex-row flex-wrap justify-end md:mr-40">
                        <BaseButton :icon="mdiViewDashboard" color="lightDark" label="Dashboard" variant="outline"
                            routeName="dashboard" v-if="$page.props.auth?.user" />
                        <BaseButton :icon="mdiLogin" color="lightDark" label="Iniciar sesión" variant="outline"
                            routeName="login" v-if="!$page.props.auth?.user" />
                        <BaseButton :icon="mdiAccountPlus" color="info" label="Crear cuenta" routeName="register"
                            v-if="!$page.props.auth?.user" />
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 bg-medic-50/85 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>