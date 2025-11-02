<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-center">
            <div class="text-3xl font-bold" :class="valueColorClass">
                {{ displayValue }}
            </div>
            <div class="text-sm text-gray-600 mt-1">{{ label }}</div>
            <div v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: [Number, String],
        required: true
    },
    label: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'blue',
        validator: value => ['blue', 'green', 'red', 'purple', 'yellow', 'gray'].includes(value)
    },
    suffix: {
        type: String,
        default: ''
    },
    prefix: {
        type: String,
        default: ''
    }
});

const valueColorClass = computed(() => {
    const colorMap = {
        blue: 'text-blue-600',
        green: 'text-green-600',
        red: 'text-red-600',
        purple: 'text-purple-600',
        yellow: 'text-yellow-600',
        gray: 'text-gray-600'
    };
    return colorMap[props.color] || 'text-blue-600';
});

const displayValue = computed(() => {
    return `${props.prefix}${props.value}${props.suffix}`;
});
</script>