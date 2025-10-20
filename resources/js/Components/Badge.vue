<template>
    <span :class="badgeClasses"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset gap-2">
        <span v-if="showDot" :class="dotClasses" class="p-1 rounded-full"></span>
        <slot />
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    color: {
        type: String,
        default: 'gray'
    },
    rounded: {
        type: String,
        default: 'rounded-full'
    },
    showDot: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'solid', 'outline', 'soft'].includes(value)
    },
});

const badgeClasses = computed(() => {
    const baseClasses = sizeClasses[props.size];
    const colorClasses = colorVariants[props.color]?.[props.variant];
    return `${baseClasses} ${colorClasses} ${props.rounded}`;
});

const colorVariants = {
    green: {
        default: 'text-green-800 bg-green-100 ring-green-500',
        solid: 'bg-green-600 ring-green-600 text-white',
        outline: 'border border-green-400/10 text-green-500 bg-transparent',
        soft: 'bg-green-100 text-green-700 ring-green-300'
    },
    red: {
        default: 'text-red-800 bg-red-100 ring-red-500',
        solid: 'bg-red-600 ring-red-600 text-white',
        outline: 'border border-red-400/10 text-red-500 bg-transparent',
        soft: 'bg-red-100 text-red-700 ring-red-300'
    },
    yellow: {
        default: 'text-yellow-800 bg-yellow-100 ring-yellow-500',
        solid: 'bg-yellow-600 ring-yellow-600 text-white',
        outline: 'border border-yellow-400/10 text-yellow-500 bg-transparent',
        soft: 'bg-yellow-100 text-yellow-700 ring-yellow-300'
    },
    wine: {
        default: 'text-wine-800/80 bg-wine-100/20 ring-wine-400',
        solid: 'bg-wine-400 ring-wine-800/70 text-white',
        outline: 'border border-wine-400/10 text-wine-100 bg-transparent',
        soft: 'bg-wine-50/15 text-wine-800/80 ring-wine-50'
    },
    gray: {
        default: 'text-gray-800 bg-gray-100 ring-gray-500',
        solid: 'bg-gray-600 ring-gray-600 text-white',
        outline: 'border border-gray-400/10 text-gray-500 bg-transparent',
        soft: 'bg-gray-100 text-gray-700 ring-gray-300'
    },
    forest: {
        default: 'text-forest-900 bg-forest-50/50 ring-forest-400',
        solid: 'bg-forest-900/90 text-white ring-forest-900',
        outline: 'border border-forest-400 text-forest-400 bg-transparent',
        soft: 'bg-forest-50/50 text-forest-400 ring-forest-300',
    }
};

const dotClasses = computed(() => {
    const color = props.color;
    const dotColors = {
        green: 'bg-green-400',
        red: 'bg-red-400',
        yellow: 'bg-yellow-400',
        wine: 'bg-wine-100',
        gray: 'bg-gray-400',
        forest: 'bg-forest-300'
    }
    return dotColors[color] || dotColors.gray
});

const sizeClasses = {
    xs: 'px-2 py-0.5 text-xs',
    sm: 'px-2.5 py-0.5 text-xs',
    md: 'px-3 py-1 text-sm',
    lg: 'px-4 py-1.5 text-base'
};
</script>