<template>
  <div class="card-box border bg-light">
    <div v-if="isForm" class="mb-6 text-lg font-medium">
      <form @submit.prevent="submit" >
      <slot />
      </form>
    </div>
    
    <div v-else class="mb-4 text-xl font-semibold">
      <slot />
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  isForm: {
    type: Boolean,
    default: false,
  },
});
</script>
<style scoped>
.card-box {
  border-radius: 12px; /* Bordes redondeados */
  padding: 24px; /* Espacio interior para que el contenido no se pegue a los bordes */
  width: 100%; /* Ocupa todo el ancho de su contenedor padre */
  
  /* Sombra sutil y moderna para dar efecto de profundidad */
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);

  /* Opcional: un borde ligero si prefieres eso a la sombra */
  /* border: 1px solid #e2e8f0; */
}
</style>