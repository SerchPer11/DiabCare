<script setup>
const props = defineProps({
  navBar: Boolean,
});
</script>

<template>
  <hr
    :class="
      props.navBar
        ? 'lg:my-0.5'
        : 'my-6 border-medic-200'
    "
    class="border-t-2 border-medic-100"
  />
</template>
