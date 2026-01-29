<script setup>
// --- Imports de Vue e Inertia ---
import { ref, onMounted, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

// --- Imports de Componentes ---
import { SidebarProvider, SidebarTrigger } from '@/Components/ui/sidebar';
import AppSidebar from '@/Components/AppSidebar.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseIcon from '@/Components/BaseIcon.vue';

// --- Imports de Iconos ---
import { mdiMenu, mdiBell, mdiClose } from '@mdi/js'; // mdiClipboardPulse como icono por defecto

// --- 1. OBTENER DATOS INICIALES ---
// Obtenemos el usuario y sus notificaciones (compartidos desde Laravel)
const page = usePage();
const user = page.props.auth.user;
// (Necesitarás compartir 'unreadNotifications' y 'unreadNotificationsCount' desde Laravel, ver "Paso Adicional 1" abajo)
const initialNotifs = page.props.auth.unreadNotifications || [];
const initialCount = page.props.auth.unreadNotificationsCount || 0;
const csrfToken = computed(() => usePage().props.csrf_token);

// --- 2. ESTADO REACTIVO ---
const notifications = ref(initialNotifs); // Lista de notificaciones en el dropdown
const newNotificationCount = ref(initialCount); // Número en el badge rojo
const isDropdownOpen = ref(false); // Controla si el dropdown está visible

// --- 3. LÓGICA DE WEBSOCKETS EN TIEMPO REAL ---
onMounted(() => {
    if (user) {
        // Nos conectamos al canal privado del usuario
        window.Echo.private(`App.Models.User.${user.id}`)
            .notification((notification) => {
                // Esto se dispara cuando llega una nueva notificación
                console.log('¡Nueva notificación!', notification);

                // La añadimos al inicio de la lista y aumentamos el contador
                notifications.value.unshift(notification);
                newNotificationCount.value++;
            });
    }
});

const markAsRead = (notificationId) => {
    router.post(route('notifications.read', notificationId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // La quitamos de la lista y reducimos el contador
            notifications.value = notifications.value.filter(n => n.id !== notificationId);
            newNotificationCount.value = Math.max(0, newNotificationCount.value - 1);
        }
    });
};

const nav = [
  { label: 'Módulos', href: '/home#modules' },
  { label: 'Foro', href: '/forum' }
];
</script>

<template>
    <SidebarProvider>
        <div class="relative flex min-h-screen w-full ">

            <AppSidebar class="hidden md:block shadow-md" />

            <div class="flex flex-col flex-1 w-full">
                <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b px-4 shadow-md bg-white">
                    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

                    <SidebarTrigger as-child
                        class="text-medic-300 hover:text-medic-500 focus:ring-0 focus:ring-offset-0 w-6">
                        <BaseButton :icon="mdiMenu" small />
                    </SidebarTrigger>
                    <nav class="hidden md:flex md:flex-1 md:justify-center items-center gap-6">
                        <Link v-for="item in nav" :key="item.href" :href="item.href"
                            class="text-sm font-medium text-medic-600 hover:text-medic-700">{{ item.label }}</Link>
                    </nav>

                    <div class="ml-auto">
                        <div class="relative mr-6">

                            <button @click="isDropdownOpen = !isDropdownOpen"
                                class="relative p-2 rounded-lg text-medic-500 hover:bg-gray-100 focus:outline-none">
                                <BaseIcon :path="mdiBell" size="24" h="h-6" w="w-6" />

                                <span v-if="newNotificationCount > 0"
                                    class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                                    {{ newNotificationCount }}
                                </span>
                            </button>

                            <div v-if="isDropdownOpen"
                                class="absolute right-0 mt-2 w-80 sm:w-96 max-h-[70vh] overflow-y-auto rounded-lg bg-white shadow-xl z-50 border">
                                <div class="p-3 font-semibold border-b text-sm text-gray-800">
                                    Notificaciones
                                </div>

                                <div v-if="notifications.length > 0">
                                    <div v-for="notif in notifications" :key="notif.id"
                                        class="flex items-start p-3 hover:bg-medic-50 border-b last:border-b-0">
                                        <BaseIcon :path="mdiBell" size="20"
                                            class="mr-3 mt-1 text-medic-400 flex-shrink-0" />

                                        <div class="flex-1">
                                            <Link :href="notif.data.link" class="text-sm text-gray-700 cursor-pointer">
                                            {{ notif.data.message }}
                                            </Link>
                                        </div>

                                        <button @click.prevent="markAsRead(notif.id)" title="Marcar como leída"
                                            class="ml-2 p-1 rounded-full hover:bg-gray-200">
                                            <BaseIcon :path="mdiClose" size="16" class="text-gray-400" />
                                        </button>
                                    </div>
                                </div>

                                <div v-else class="p-6 text-center text-sm text-gray-500">
                                    No tienes notificaciones nuevas.
                                </div>

                                <div class="p-2 text-center bg-gray-50 border-t">
                                    <Link :href="route('notifications.index')"
                                        class="text-sm text-medic-500 hover:underline">
                                    Ver todas
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 md:p-6 bg-medic-50/85 overflow-auto">
                    <slot />
                </main>
            </div>
        </div>
    </SidebarProvider>
</template>