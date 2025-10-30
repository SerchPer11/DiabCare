<script setup>
import BaseIcon from "@/Components/BaseIcon.vue";

defineProps({
  icon: {
    type: String,
    required: true,
  },
  class: {
    type: String,
    default: 'bg-medic-50 text-medic-500',
  },
  w: {
    type: String,
    default: "w-12",
  },
  h: {
    type: String,
    default: "h-12",
  },
  bg: Boolean,
});
</script>

<template>
  <BaseIcon
    :path="icon"
    :w="w"
    :h="h"
    size="24"
    class="rounded-full"
    :class="class"
    />
</template>
