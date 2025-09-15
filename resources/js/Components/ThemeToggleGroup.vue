// resources/js/Components/ThemeToggleGroup.vue

<script setup>
import { useColorMode } from '@vueuse/core'
import {
  ToggleGroup,
  ToggleGroupItem,
} from '@/Components/ui/toggle-group'
import { mdiWhiteBalanceSunny, mdiMoonWaningCrescent, mdiLaptop } from '@mdi/js'
import Icon from '@/Components/Icon.vue'

const mode = useColorMode()
</script>

<template>
  <ToggleGroup
    v-model="mode"
    type="single"
    variant="ghost"
    class="bg-transparent rounded-md p-1 shadow-sm"
  >
    <ToggleGroupItem
      value="light"
      aria-label="Toggle light"
      class="shadow-sm transition-all duration-300 data-[state=on]:bg-gray-200 data-[state=on]:text-accent-foreground"
    >
      <Icon :path="mdiWhiteBalanceSunny" />
    </ToggleGroupItem>

    <ToggleGroupItem
      value="dark"
      aria-label="Toggle dark"
      class="shadow-sm transition-all duration-300 data-[state=on]:bg-gray-500 data-[state=on]:text-accent-foreground"
    >
      <Icon :path="mdiMoonWaningCrescent" />
    </ToggleGroupItem>

    <ToggleGroupItem
      value="auto"
      aria-label="Toggle system"
      class="data-[state=on]:bg-gray-500 data-[state=on]:text-accent-foreground"
    >
      <Icon :path="mdiLaptop" />
    </ToggleGroupItem>
  </ToggleGroup>
</template>