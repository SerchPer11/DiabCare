<template>
    <CardBox>
        <div class="">
            <div class="ml-6">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold mr-2">{{ title }}</h1>
                    <IconRounded v-if="main" :icon="icon" :color="color" bg />
                    <BaseIcon v-else :path="icon" size="30" h="h-auto" w="w-auto" class="p-1 rounded-lg" />
                </div>
                <BreadCrumb :resource-title="title" class="mt-2" />
            </div>
        </div>
    </CardBox>
</template>

<script setup lang="ts">
import CardBox from '@/Components/CardBox.vue';
import BaseIcon from '@/Components/BaseIcon.vue';
import IconRounded from '@/Components/IconRounded.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'light'
    },
    icon: {
        type: String,
        default: '',
    },
    main: Boolean,
});
</script>