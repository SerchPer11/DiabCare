<template>
  <ButtonGroup>
    <Input placeholder="Agregar filtro..." class="rounded-md shadow-md h-12 text-md mx-1 bg-transparent" />
    <BaseButton color="whiteDark" :icon="mdiMagnify" small />
  </ButtonGroup>
</template>

<script setup lang="ts">
import { ButtonGroup } from "@/Components/ui/button-group"
import { Input } from "@/Components/ui/input"
import { mdiMagnify } from '@mdi/js'
import BaseButton from "./BaseButton.vue"
</script>