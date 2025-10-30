<template>
  <CardBox isForm @submit.prevent="$emit('applyFilters', true)">

    <template v-if="icon">
      <div class="grid grid-cols-1 md:grid-cols-3 md:items-center w-full gap-4">

        <div class="ml-6">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold mr-2">{{ title }}</h1>
            <IconRounded v-if="main" :icon="icon" :color="color" bg />
            <BaseIcon v-else :path="icon" size="30" h="h-auto" w="w-auto" class="p-1 rounded-lg" />
          </div>
          <BreadCrumb :resource-title="title" class="mt-2" />
        </div>

        <div class="flex justify-center">
          <div class="relative w-full sm:w-100">
            <BaseFormField v-model="search" type="input" label="Buscar..." placeholder="Ingresa un parámetro..."
              :maxLength="100" />
            <BaseButton color="white" :icon="mdiMagnify" small class="absolute top-10 right-0 mr-1" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-x-4 md:mr-14">
          <BaseFormField class="w-32" @change="$emit('applyFilters', true)" type="select" label="Número de filas"
            v-model="rows" placeholder="Elige" :options="rowsPerPage" />
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
import BaseButton from './BaseButton.vue';
import { mdiMagnify } from '@mdi/js'
import BaseFormField from './BaseFormField.vue';
import { watch, onMounted, ref, useSlots } from 'vue';

const emits = defineEmits(['clearFilters', 'applyFilters']);
const slots = useSlots();

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
  total: {
    type: Number,
    default: 0
  },
  main: Boolean,
  hasPeriod: Boolean,
});



const withTrashed = defineModel('withTrashed');
const search = defineModel('search');
const rows = defineModel('rows');
const STORAGE_KEY = 'showOtherFilters'
const showOtherFilters = ref(true)

const rowsPerPage = ["5", "10", "25", "50"];


watch(withTrashed, () => {
  emits('applyFilters', true);
});

const hasOtherFilters = !!slots.default;

watch(withTrashed, () => {
  emits('applyFilters', true);
});

onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved !== null) {
    showOtherFilters.value = saved === 'true'
  }
});

watch(showOtherFilters, (newValue) => {
  localStorage.setItem(STORAGE_KEY, newValue)
});


</script>