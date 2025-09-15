// resources/js/Layouts/AuthenticatedLayout.vue

<script setup>
import { SidebarProvider, SidebarTrigger } from '@/Components/ui/sidebar';
import AppSidebar from '@/Components/AppSidebar.vue';
import { Button } from '@/Components/ui/button';
import Icon from '@/Components/Icon.vue';
import { mdiMenu } from '@mdi/js';
import ThemeToggleGroup from '@/Components/ThemeToggleGroup.vue'; // No olvides importarlo si lo usas aquí
</script>

<template>
  <SidebarProvider>
    <div class="relative flex min-h-screen w-full">
      
      <AppSidebar class="hidden md:block" />

      <div class="flex flex-col flex-1 w-full">
        <header class="sticky top-0 z-10 flex h-14 items-center gap-4 border-b bg-background px-4">
          <SidebarTrigger as-child class="text-muted-foreground hover:text-primary focus:ring-0 focus:ring-offset-0">
            <Button size="icon" variant="outline" class="md:hidden">
              <Icon :path="mdiMenu" class="h-5 w-5" />
              <span class="sr-only">Toggle Menu</span>
            </Button>
          </SidebarTrigger>
          
          <div class="ml-auto">
            <!-- Aquí puedes colocar otros elementos del encabezado, como el selector de tema -->
          </div>
        </header>
        
        <main class="flex-1 p-4 md:p-6">
          <slot />
        </main>
      </div>
    </div>
  </SidebarProvider>
</template>