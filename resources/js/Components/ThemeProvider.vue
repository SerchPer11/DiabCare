// resources/js/Components/ThemeProvider.vue
<script setup>
import { useColorMode } from '@vueuse/core'
import { watchEffect } from 'vue'

const mode = useColorMode({
  selector: 'html',
  attribute: 'class',
  modes: {
    dark: 'dark',
    light: 'light',
  },
})

// Opcional: Para asegurar que el tema se aplique en el lado del servidor con Inertia (SSR)
// Si no usas SSR, puedes quitar este watchEffect.
import { usePage } from '@inertiajs/vue3'
const page = usePage()
watchEffect(() => {
  // Esta línea es un ejemplo, podrías necesitar ajustar cómo pasas el tema desde Laravel
  const ssrTheme = page.props.theme || 'light'
  mode.value = ssrTheme
})
</script>

<template>
  <slot />
</template>