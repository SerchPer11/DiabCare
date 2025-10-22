<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuItem,
    SidebarHeader,
    SidebarFooter,
} from "@/Components/ui/sidebar";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/Components/ui/collapsible";
import Icon from '@/Components/Icon.vue';
import { mdiHeartPulse, mdiLogout, mdiChevronDown } from '@mdi/js';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import ThemeToggleGroup from '@/Components/ThemeToggleGroup.vue';
import { useNavigation } from '@/Composables/useNavigation.js';
import { computed } from 'vue';

const {
    navigation: authorizedNavigation,
    user,
    userName,
    userInitials,
    userRoleLabel,
    userEmail
} = useNavigation();

// --- INICIO DE LA LÓGICA MODIFICADA ---

/**
 * Extrae el prefijo base de una ruta de recurso.
 * Ejemplo: 'users.create' -> 'users'
 * Ejemplo: 'doctor.medicaments.edit' -> 'doctor.medicaments'
 * @param {string} routeName - El nombre completo de la ruta.
 * @returns {string} - El prefijo del recurso.
 */
const getRoutePrefix = (routeName) => {
    if (!routeName || !routeName.includes('.')) {
        return routeName;
    }
    const parts = routeName.split('.');
    parts.pop(); // Elimina la última parte (index, create, edit, etc.)
    return parts.join('.'); // Une el resto para formar el prefijo
};

/**
 * Función helper mejorada para detectar si una ruta está relacionada con otra.
 * @param {string} targetRoute - La ruta del enlace del sidebar.
 * @param {string} currentRoute - La ruta actual de la página.
 * @returns {boolean}
 */
const isRouteRelated = (targetRoute, currentRoute) => {
    // La coincidencia exacta siempre tiene la máxima prioridad.
    if (route().current(targetRoute)) {
        return true;
    }

    if (!targetRoute || !currentRoute) {
        return false;
    }

    // Compara los prefijos de recurso.
    const targetPrefix = getRoutePrefix(targetRoute);
    const currentPrefix = getRoutePrefix(currentRoute);

    return targetPrefix === currentPrefix;
};

// --- FIN DE LA LÓGICA MODIFICADA ---

// Esta función no necesita cambios, ya que ahora depende de la nueva `isRouteRelated`.
const isGroupActive = (group) => {
    if (group.type !== 'group' || !group.items) return false;
    const currentRouteName = route().current();
    return group.items.some(subItem => isRouteRelated(subItem.route, currentRouteName));
};

const userPrimaryRole = computed(() => usePage().props.auth.roles[0]);

const profileRouteName = computed(() => {
    switch (userPrimaryRole.value) {
        case 'admin':
            return 'admin.profile.show';
        case 'doctor':
            return 'doctor.profile.show';
        case 'paciente':
            return 'patient.profile.show';
        default:
            return 'dashboard';
    }
});
</script>

<template>
    <Sidebar>
        <SidebarHeader class="bg-medic-50">
            <div class="flex items-center justify-between gap-2 p-3 rounded-lg">
                <SidebarGroupLabel class="text-xl text-medic-700 font-semibold">DiabCare</SidebarGroupLabel>
                <Icon :path="mdiHeartPulse" class="h-6 w-6 text-medic-700 mr-4" />
            </div>
        </SidebarHeader>

        <SidebarContent class="bg-medic-50">
            <div v-for="(item, index) in authorizedNavigation" :key="index" class="px-2">

                <!-- Item Individual -->
                <SidebarMenu v-if="item.type === 'single'" class="w-full">
                    <SidebarMenuItem as-child>
                        <Link :href="route(item.route)"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-medic-300 transition-colors hover:bg-medic-100 hover:text-medic-500"
                            :class="{ 'text-medic-500 bg-medic-100': isRouteRelated(item.route, route().current()) }">
                        <Icon :path="item.icon" class="h-4 w-4" />
                        <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuItem>
                </SidebarMenu>

                <!-- Grupo Colapsable -->
                <SidebarGroup v-else-if="item.type === 'group'">
                    <Collapsible class="w-full" :default-open="isGroupActive(item)">
                        <CollapsibleTrigger class="w-full">
                            <div class="flex w-full items-center justify-between rounded-md text-medic-300 hover:text-medic-500 py-1"
                                :class="{ ' text-medic-500': isGroupActive(item) }">
                                <SidebarGroupLabel
                                    class="text-md flex items-center gap-2 text-medic-300 hover:text-medic-500"
                                    :class="{ 'text-medic-500 font-medium': isGroupActive(item) }">
                                    <Icon :path="item.icon" class="h-4 w-4" />
                                    {{ item.title }}
                                </SidebarGroupLabel>
                                <Icon :path="mdiChevronDown"
                                    class="h-4 w-4 transition-transform duration-200 data-[state=open]:rotate-180" />
                            </div>
                        </CollapsibleTrigger>
                        <CollapsibleContent
                            class="mt-1 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95">
                            <SidebarMenu>
                                <SidebarMenuItem v-for="subItem in item.items" :key="subItem.title" as-child>
                                    <Link :href="route(subItem.route)"
                                        class="flex items-center gap-3 rounded-lg px-6 py-2 text-medic-300 transition-colors hover:bg-medic-100 hover:text-medic-500 ml-2 "
                                        :class="{ 'bg-medic-100 text-medic-500': isRouteRelated(subItem.route, route().current()) }">
                                    <Icon :path="subItem.icon" class="h-4 w-4" />
                                    <span>{{ subItem.title }}</span>
                                    </Link>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarGroup>
            </div>
        </SidebarContent>

        <SidebarFooter class="bg-medic-50">
            <!--<ThemeToggleGroup />-->
            <div v-if="user">
                <div class="flex gap-1 p-2">
                    <Link :href="route(profileRouteName)"
                        class="flex items-center gap-3 flex-1 hover:bg-medic-100 p-2 rounded-lg w-full">
                    <Avatar shape="square" class="bg-transparent">
                        <AvatarFallback class="text-medic-700 text-xl font-semibold">
                            {{ userInitials }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col overflow-hidden text-left">
                        <span class="text-sm font-semibold text-medic-700 truncate">{{ userName }}</span>
                        <span class="text-xs text-medic-500 truncate">{{ userRoleLabel }}</span>
                        <span class="text-xs text-medic-500 truncate">{{ userEmail }}</span>
                    </div>
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="ml-auto">
                    <Icon :path="mdiLogout"
                        class="h-6 w-5 text-medic-300 hover:text-red-500 transition-colors duration-200" />
                    </Link>
                </div>
            </div>
        </SidebarFooter>
    </Sidebar>
</template>