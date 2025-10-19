// resources/js/Components/AppSidebar.vue

<script setup>
import { Link } from '@inertiajs/vue3';
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


const {
    navigation: authorizedNavigation,
    user,
    userName,
    userInitials,
    userRoleLabel,
    userEmail
} = useNavigation();

// Función helper para detectar si una ruta está relacionada con otra
const isRouteRelated = (targetRoute, currentRoute) => {
    // Coincidencia exacta
    if (route().current(targetRoute)) {
        return true;
    }

    // Extraer el prefijo base (antes del último punto)
    // Ejemplo: 'roles.index' -> 'roles'
    const routePrefix = targetRoute.includes('.') ? targetRoute.split('.')[0] : targetRoute;

    // Verificar si la ruta actual empieza con el mismo prefijo
    // Ejemplo: 'roles.create' empieza con 'roles.'
    if (currentRoute && currentRoute.startsWith(routePrefix + '.')) {
        return true;
    }

    return false;
};

// Función para detectar si un grupo contiene la página actual
const isGroupActive = (group) => {
    if (group.type !== 'group' || !group.items) return false;

    const currentRouteName = route().current();

    const isActive = group.items.some(subItem => {
        const isRelated = isRouteRelated(subItem.route, currentRouteName);
        return isRelated;
    });

    return isActive;
};

const openProfile = () => {
    // Lógica para abrir el perfil del usuario PENDIENTE
    alert('Abrir perfil de usuario');
};
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
                                <SidebarGroupLabel class="text-md flex items-center gap-2 text-medic-300 hover:text-medic-500"
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
                                        class="flex items-center gap-3 rounded-lg px-6 py-2 text-medic-300 transition-colors hover:bg-medic-50 hover:text-medic-500 ml-2 "
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
                    <button class="flex items-center gap-3 flex-1 hover:bg-medic-100 p-2 rounded-lg w-full"
                        @click="openProfile">
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
                    </button>
                    <Link :href="route('logout')" method="post" as="button" class="ml-auto">
                    <Icon :path="mdiLogout"
                        class="h-6 w-5 text-medic-300 hover:text-red-500 transition-colors duration-200" />
                    </Link>
                </div>
            </div>
        </SidebarFooter>
    </Sidebar>
</template>
