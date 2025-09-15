// resources/js/Components/AppSidebar.vue

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
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
import navigation from '@/Composables/navigation.js'; // Usamos tu ruta al archivo de navegación
import ThemeToggleGroup from '@/Components/ThemeToggleGroup.vue';

// Obtenemos el usuario real y sus permisos desde Inertia
const user = computed(() => usePage().props.auth.user);
const userPermissions = computed(() => user.value?.permissions || []);

// Función para verificar si un link debe mostrarse
const canViewLink = (link) => {
  if (!link.meta?.permissions) return true;
  return link.meta.permissions.some(p => userPermissions.value.includes(p));
};

// 1. Nueva lógica que filtra y prepara la navegación
const authorizedNavigation = computed(() => {
  return navigation
    .map(item => {
      if (!item.isGroup) {
        return canViewLink(item) ? item : null;
      }
      const authorizedLinks = item.links.filter(canViewLink);
      return authorizedLinks.length > 0 ? { ...item, links: authorizedLinks } : null;
    })
    .filter(Boolean);
});
</script>

<template>
  <Sidebar>
    <SidebarHeader>
      <div class="flex items-center gap-2">
        <Icon :path="mdiHeartPulse" class="h-6 w-6 text-primary" />
        <SidebarGroupLabel class="text-lg">DiabCare</SidebarGroupLabel>
      </div>
    </SidebarHeader>

    <SidebarContent>
      <div v-for="(item, index) in authorizedNavigation" :key="index" class="px-2">
        
        <SidebarMenu v-if="!item.isGroup" class="w-full">
            <SidebarMenuItem as-child>
                <Link :href="item.url()" class="flex items-center gap-3 rounded-lg px-3 py-2 text-muted-foreground transition-colors hover:bg-muted hover:text-primary">
                    <Icon :path="item.icon" class="h-4 w-4" />
                    <span>{{ item.title }}</span>
                </Link>
            </SidebarMenuItem>
        </SidebarMenu>

        <SidebarGroup v-else>
          <Collapsible class="w-full shadow-sm">
            <CollapsibleTrigger class="w-full">
              <div class="flex w-full items-center justify-between rounded-md p-1 hover:bg-muted">
                <SidebarGroupLabel class="text-md">{{ item.groupName }}</SidebarGroupLabel>
                <Icon :path="mdiChevronDown" class="h-4 w-4 transition-transform duration-200 data-[state=open]:rotate-180" />
              </div>
            </CollapsibleTrigger>
            <CollapsibleContent class="mt-1 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95">
              <SidebarMenu>
                <SidebarMenuItem v-for="link in item.links" :key="link.title" as-child>
                  <Link :href="link.url()" class="flex items-center gap-3 rounded-lg px-3 py-2 text-muted-foreground transition-colors hover:bg-muted hover:text-primary">
                    <Icon :path="link.icon" class="h-4 w-4" />
                    <span>{{ link.title }}</span>
                  </Link>
                </SidebarMenuItem>
              </SidebarMenu>
            </CollapsibleContent>
          </Collapsible>
        </SidebarGroup>
      </div>
    </SidebarContent>

    <SidebarFooter>
      <ThemeToggleGroup />
      <div v-if="user" class="flex items-center gap-3 p-3 rounded-lg bg-muted">
        <Avatar>
          <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
        </Avatar>
        <div class="flex flex-col overflow-hidden">
          <span class="text-sm font-semibold text-foreground truncate">{{ user.name }}</span>
          <span class="text-xs text-muted-foreground truncate">{{ user.email }}</span>
        </div>
        <Link :href="route('logout')" method="post" as="button" class="ml-auto">
          <Icon :path="mdiLogout" class="h-5 w-5 text-muted-foreground hover:text-foreground" />
        </Link>
      </div>
    </SidebarFooter>
  </Sidebar>
</template>