// resources/js/Layouts/GuestLayout.vue
<script setup>
import ThemeToggleGroup from '@/Components/ThemeToggleGroup.vue';
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-background text-foreground p-4">
    <div class="absolute bottom-4 left-4">
    </div>
    <slot />
  </div>
</template>