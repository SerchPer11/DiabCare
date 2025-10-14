<template>
  <CardBox>

    <template v-if="icon">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between w-full gap-4">

        <div class="ml-6">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold mr-2">{{ title }}</h1>
            <IconRounded v-if="main" :icon="icon" :color="color" bg />
            <BaseIcon v-else :path="icon" size="30" h="h-auto" w="w-auto" class="p-1 rounded-lg" />
          </div>

          <BreadCrumb :resource-title="title" class="mt-2" />
        </div>

        <div class="w-full md:w-1/3 md:max-w-sm mr-20">
          <div class="flex items-center">
            <SearchBar />
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" class="ml-4 hover:bg-medic-400 h-12"
              title="Agregar módulo" :routeName="`${routeName}create`" />
          </div>
        </div>

      </div>
    </template>
  </CardBox>
</template>

<script setup>
import CardBox from './CardBox.vue';
import BaseIcon from './BaseIcon.vue';
import IconRounded from './IconRounded.vue';
import BreadCrumb from './BreadCrumb.vue';
import SearchBar from './SearchBar.vue';
import BaseButton from './BaseButton.vue';
import { mdiPlus } from '@mdi/js'

const props = defineProps({
  title: {
    type: String,
    default: ' ',
  },
  icon: {
    type: String,
    default: '',
  },
  color: {
    type: String,
    default: 'light'
  },
  routeName: {
    type: String,
    default: ''
  },
  main: Boolean,
});

</script>