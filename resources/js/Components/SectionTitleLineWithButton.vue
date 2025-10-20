<script setup>
import { mdiCog, mdiClose, mdiHome, mdiChevronRight } from "@mdi/js";
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

import BaseIcon from "@/Components/BaseIcon.vue";
import IconRounded from "@/Components/IconRounded.vue";
import { colorsBgLight } from "@/colors";

const props = defineProps({
  icon: String,
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: null
  },
  routeBack: {
    type: String,
    default: null
  },
  titleBack: {
    type: String,
    default: 'Gestión'
  },
  hisBreadCrumb: {
    type: Boolean,
    default: true
  },
  color: {
    type: String,
    default: 'light'
  },
  main: Boolean,
});

const titleClass = computed(() => {
  const base = [
    "leading-tight text-forest-400 dark:text-white",
    props.main ? 'text-2xl md:text-3xl font-bold' : 'text-xl md:text-xl font-medium'
  ];

  return base;
});

const linkClass = computed(() => {
  const base = [
    "text-forest-100 hover:text-forest-400 dark:text-gray-400 dark:hover:text-white",
  ];

  return base;
});
</script>

<template>
  <section :class="{ 'pt-6': !main }" class="mb-6 md:flex items-center justify-between md:space-y-0 space-y-2">
    <div>
      <div class="flex items-center justify-start space-x-2">
        <template v-if="icon">
          <IconRounded v-if="main" :icon="icon" :color="color" bg />
          <BaseIcon v-else :path="icon" size="30" h="h-auto" w="w-auto" class="p-1 rounded-lg"
            :class="colorsBgLight[color]" />
        </template>

        <div>
          <h1 :class="titleClass">
            {{ title }}
          </h1>
          <h2 v-if="description" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 text-justify mr-5">
            {{ description }}
          </h2>
        </div>
      </div>

      <nav v-if="hisBreadCrumb" class="flex p-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
          <li class="inline-flex items-center">
            <Link :href="route('dashboard')" class="group inline-flex items-center text-sm font-medium"
              :class="linkClass" title="Ir al Dashboard">
            <BaseIcon :path="mdiHome" class="text-forest-100 group-hover:text-forest-400" />
            Dashboard
            </Link>
          </li>

          <li v-if="routeBack">
            <div class="flex items-center">
              <svg class="rtl:rotate-180 w-3 h-3 text-forest-100 mx-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 9 4-4-4-4" />
              </svg>
              <Link :href="route(routeBack)" class="text-sm font-medium md:ms-2" :class="linkClass">
              {{ titleBack }}
              </Link>
            </div>
          </li>

          <li aria-current="page">
            <div class="flex items-center">
              <svg class="rtl:rotate-180 w-3 h-3 text-forest-100 mx-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 9 4-4-4-4" />
              </svg>
              <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                {{ title }}
              </span>
            </div>
          </li>
        </ol>
      </nav>
    </div>

    <div>
      <slot />
    </div>
  </section>
</template>