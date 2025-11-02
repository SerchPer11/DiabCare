<script setup>
import { computed } from 'vue'
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/Components/ui/accordion'
import BaseIcon from "@/Components/BaseIcon.vue";

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  icon: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    default: 'item-1',
  },
  defaultOpen: {
    type: Boolean,
    default: false,
  }
})

const defaultValue = computed(() => (props.defaultOpen ? props.value : undefined))
</script>

<template>
  <Accordion type="single" collapsible :defaultValue="defaultValue" class="w-full">
    <AccordionItem :value="value" class="border-b-0">
      
      <AccordionTrigger class="py-4 hover:no-underline">
        <div class="flex gap-2 items-center text-left m-2">
          <BaseIcon
            class="bg-medic-500 text-white rounded-lg flex-shrink-0"
            :path="icon"
            size="24"
            h="h-8"
            w="w-8"
          />
          <div>
            <p class="text-medic-400 text-xl font-bold">{{ title }}</p>
            <p v-if="subtitle" class="text-sm font-light text-slate-800 dark:text-slate-300">
              {{ subtitle }}
            </p>
          </div>
        </div>
      </AccordionTrigger>
      
      <AccordionContent class="pt-2 pb-4">
        <slot />
      </AccordionContent>
    </AccordionItem>
  </Accordion>
</template>