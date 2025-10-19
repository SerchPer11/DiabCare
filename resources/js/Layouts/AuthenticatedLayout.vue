// resources/js/Layouts/AuthenticatedLayout.vue

<script setup>
import { SidebarProvider, SidebarTrigger } from '@/Components/ui/sidebar';
import AppSidebar from '@/Components/AppSidebar.vue';
import Icon from '@/Components/Icon.vue';
import { mdiMenu, mdiBell } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
</script>

<template>
  <SidebarProvider>
    <div class="relative flex min-h-screen w-full">
      
      <AppSidebar class="hidden md:block shadow-md" />

      <div class="flex flex-col flex-1 w-full">
        <header class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b bg-background px-4 shadow-md">
          <SidebarTrigger as-child class="text-muted-foreground hover:text-primary focus:ring-0 focus:ring-offset-0" >
              <BaseButton :icon="mdiMenu" small />
          </SidebarTrigger>
          
          <div class="ml-auto">
            <div class="mr-6">
              <button class="text-dark-foreground hover:text-muted-foreground focus:ring-0 focus:ring-offset-0">
                <Icon :path="mdiBell"/> <!-- FALTA AGREGAR LOGICA DE NOTIFICACIONES -->
              </button>
            </div>
          </div>
        </header>
        
        <main class="flex-1 p-4 md:p-6">
          <slot />
        </main>
      </div>
    </div>
  </SidebarProvider>
</template>