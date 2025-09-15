<script setup>
import { computed } from 'vue';

const props = defineProps({
  path: {
    type: String,
    required: true,
  },
  size: {
    type: [String, Number],
    default: 24,
  },
  w: {
    type: [String, Number],
    default: null,
  },
  h: {
    type: [String, Number],
    default: null,
  },
});

const computedSize = computed(() => props.size || 24);
</script>

<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="w || computedSize"
    :height="h || computedSize"
    viewBox="0 0 24 24"
    fill="currentColor"
  >
    <path :d="path" />
  </svg>
</template>