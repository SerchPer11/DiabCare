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
import { useNavigation } from '@/Composables/useNavigation.js';

const {
    navigation: authorizedNavigation,
    user,
    userName,
    userInitials,
    userRoleLabel,
    userEmail
} = useNavigation();

const openProfile = () => {
    alert('Abrir perfil de usuario');
};
</script>

<template>
    <!-- Sidebar con fondo pastel y bordes suaves -->
    <Sidebar
        class="bg-sidebar text-mono-700 border-r border-mono-200 dark:text-mono-100 dark:bg-mono-800/40 dark:border-mono-700">
        <!-- Header con acento pastel -->
        <SidebarHeader class="px-3 py-3">
            <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-primary-200/40 dark:bg-mono-700/40">
                <SidebarGroupLabel class="text-lg font-semibold text-mono-800 dark:text-mono-100">
                    DiabCare
                </SidebarGroupLabel>
                <Icon :path="mdiHeartPulse" class="h-6 w-6 text-primary-600" />
            </div>
        </SidebarHeader>

        <SidebarContent>
            <div v-for="(item, index) in authorizedNavigation" :key="index" class="px-2">
                <!-- Item individual -->
                <SidebarMenu v-if="item.type === 'single'" class="w-full">
                    <SidebarMenuItem as-child>
                        <Link :href="route(item.route)" class="flex items-center gap-3 rounded-lg px-3 py-2 transition-colors
                     text-mono-700 hover:text-primary-700 hover:bg-primary-200/40
                     dark:text-mono-100 dark:hover:text-primary-200 dark:hover:bg-mono-700/50" :class="{
                        'bg-primary-200/60 text-primary-800 dark:bg-mono-700/60 dark:text-primary-200 font-medium':
                            route().current(item.route)
                    }">
                        <Icon :path="item.icon" class="h-4 w-4" />
                        <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuItem>
                </SidebarMenu>

                <!-- Grupo colapsable -->
                <SidebarGroup v-else-if="item.type === 'group'">
                    <Collapsible class="w-full">
                        <CollapsibleTrigger class="w-full">
                            <div class="flex w-full items-center justify-between rounded-lg px-3 py-2
                       text-mono-700 hover:text-primary-700 hover:bg-primary-200/40
                       dark:text-mono-100 dark:hover:text-primary-200 dark:hover:bg-mono-700/50">
                                <SidebarGroupLabel class="text-sm flex items-center gap-2">
                                    <Icon :path="item.icon" class="h-4 w-4" />
                                    {{ item.title }}
                                </SidebarGroupLabel>
                                <Icon :path="mdiChevronDown"
                                    class="h-4 w-4 transition-transform duration-200 data-[state=open]:rotate-180" />
                            </div>
                        </CollapsibleTrigger>

                        <CollapsibleContent class="mt-1 data-[state=open]:animate-in data-[state=closed]:animate-out
                     data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0
                     data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95">
                            <SidebarMenu>
                                <SidebarMenuItem v-for="subItem in item.items" :key="subItem.title" as-child>
                                    <Link :href="route(subItem.route)" class="flex items-center gap-3 rounded-lg px-6 py-2 ml-2 transition-colors
                           text-mono-700 hover:text-primary-700 hover:bg-primary-200/40
                           dark:text-mono-100 dark:hover:text-primary-200 dark:hover:bg-mono-700/50" :class="{
                            'bg-primary-200/60 text-primary-800 dark:bg-mono-700/60 dark:text-primary-200 font-medium':
                                route().current(subItem.route)
                        }">
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

        <!-- Footer con tarjeta suave -->
        <SidebarFooter>
            <div v-if="user">
                <div class="flex gap-1 p-2">
                    <button class="flex items-center gap-3 flex-1 p-2 rounded-lg w-full
                   hover:bg-forest-200/50 dark:hover:bg-mono-700/50" @click="openProfile">
                        <Avatar shape="square" class="bg-transparent">
                            <AvatarFallback class="text-mono-800 dark:text-mono-100 text-xl font-semibold">
                                {{ userInitials }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="flex flex-col overflow-hidden text-left">
                            <span class="text-sm font-semibold text-mono-800 dark:text-mono-100 truncate">
                                {{ userName }}
                            </span>
                            <span class="text-xs text-mono-500 dark:text-mono-300 truncate">
                                {{ userRoleLabel }}
                            </span>
                            <span class="text-xs text-mono-500 dark:text-mono-300 truncate">
                                {{ userEmail }}
                            </span>
                        </div>
                    </button>

                    <Link :href="route('logout')" method="post" as="button" class="ml-auto">
                    <Icon :path="mdiLogout"
                        class="h-5 w-5 text-mono-500 hover:text-rose-600 transition-colors duration-200" />
                    </Link>
                </div>
            </div>
        </SidebarFooter>
    </Sidebar>
</template>
