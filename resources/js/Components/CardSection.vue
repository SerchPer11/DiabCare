<template>
    <BaseDivider v-if="hasDivider" />

    <section>
        <div :class="{ 'flex flex-row items-center gap-2': $slots.icon || icon }">
            <slot name="icon" />

            <BaseIcon v-if="icon" class="p-2 rounded-lg" :path="icon" size="24" h="h-10" w="w-10"
                :class="iconClasses" />

            <slot v-if="$slots.heading" name="heading" />

            <div v-else>
                <h3 class="text-base text-forest-400 font-semibold">{{ title }}</h3>
                <span class="text-sm text-gray-500">{{ description }}</span>
            </div>
        </div>

        <div class="mt-6">
            <slot />
        </div>
    </section>
</template>

<script setup>
import BaseDivider from '@/Components/BaseDivider.vue';
import BaseIcon from './BaseIcon.vue';

defineProps({
    title: String,
    description: String,
    hasDivider: { type: Boolean, default: true },
    icon: String,
    iconClasses: {
        type: String,
        default: 'bg-forest-400 text-white'
    },
});
</script>