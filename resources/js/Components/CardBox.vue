<script setup>
import { computed, useSlots } from "vue";
import CardBoxComponentBody from "@/Components/CardBoxComponentBody.vue";
import CardBoxComponentFooter from "@/Components/CardBoxComponentFooter.vue";

const props = defineProps({
  rounded: {
    type: String,
    default: "rounded-xl",
  },
  padding: {
    type: String,
    default: "p-1",
  },
  flex: {
    type: String,
    default: "flex-col",
  },
  bg: {
    type: String,
    default: "bg-white",
  },
  hasBorder: {
    type: Boolean,
    default: true,
  },
  hasComponentLayout: Boolean,
  hasTable: Boolean,
  isForm: Boolean,
  isHoverable: Boolean,
  isModal: Boolean,
});

const emit = defineEmits(["submit"]);

const slots = useSlots();

const hasFooterSlot = computed(() => slots.footer && !!slots.footer());

const componentClass = computed(() => {
  const base = [
    props.bg,
    props.padding,
    props.rounded,
    props.flex,
    props.isModal ? "" : "",
  ];

  if (props.isHoverable) {
    base.push("hover:shadow-lg transition-shadow duration-500");
  }

  if (props.hasBorder) {
    base.push("border-2 border-medic-200/60 shadow-md");
  }

  return base;
});

const submit = (event) => {
  emit("submit", event);
};
</script>

<template>
  <component
    :is="isForm ? 'form' : 'div'"
    :class="componentClass"
    @submit="submit"
  >
    <slot v-if="hasComponentLayout" />
    <template v-else>
      <CardBoxComponentBody :no-padding="hasTable">
        <slot />
      </CardBoxComponentBody>
      <CardBoxComponentFooter v-if="hasFooterSlot">
        <slot name="footer" />
      </CardBoxComponentFooter>
    </template>
  </component>
</template>
