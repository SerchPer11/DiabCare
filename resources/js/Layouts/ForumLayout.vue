<script setup>
import Icon from '@/Components/Icon.vue';
import { mdiBell } from '@mdi/js';
</script>

<template>
    <div class="relative flex min-h-screen w-full">

      <div class="flex flex-col flex-1 w-full">
        <header class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b px-4 shadow-md">
          
          <div class="ml-auto">
            <div class="mr-6">
              <button class="text-medic-500 hover:text-medic-300 focus:ring-0 focus:ring-offset-0">
                <Icon :path="mdiBell"/> 
              </button>
            </div>
          </div>
        </header>
        
        <main class="flex-1 p-4 md:p-6 bg-medic-50/85 overflow-auto">
          <slot />
        </main>
      </div>
    </div>
</template>