<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h4 class="font-medium text-gray-900 mb-4">{{ title }}</h4>
            <div v-if="items && items.length > 0" class="space-y-3">
                <div 
                    v-for="(item, index) in items" 
                    :key="index"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ item.title || item.name }}</div>
                        <div v-if="item.subtitle || item.description" class="text-sm text-gray-600">
                            {{ item.subtitle || item.description }}
                        </div>
                        <div v-if="item.meta" class="text-xs text-gray-500 mt-1">
                            {{ item.meta }}
                        </div>
                    </div>
                    <div v-if="item.badge" class="ml-4">
                        <span :class="getBadgeClasses(item.badge)">
                            {{ item.badge.text }}
                        </span>
                    </div>
                    <div v-if="item.action" class="ml-4">
                        <Link
                            v-if="!item.action.external"
                            :href="getActionUrl(item.action)"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                            {{ item.action.text || 'Ver' }}
                        </Link>
                        <button
                            v-else
                            @click="handleAction(item.action)"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                            {{ item.action.text || 'Acción' }}
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                {{ emptyMessage }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    items: {
        type: Array,
        default: () => []
    },
    emptyMessage: {
        type: String,
        default: 'No hay elementos para mostrar'
    }
});

const emit = defineEmits(['action']);

const getBadgeClasses = (badge) => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    const colorMap = {
        green: 'bg-green-100 text-green-800',
        blue: 'bg-blue-100 text-blue-800',
        red: 'bg-red-100 text-red-800',
        yellow: 'bg-yellow-100 text-yellow-800',
        gray: 'bg-gray-100 text-gray-800',
        purple: 'bg-purple-100 text-purple-800'
    };
    
    return `${baseClasses} ${colorMap[badge.color] || colorMap.gray}`;
};

const getActionUrl = (action) => {
    return action.params 
        ? route(action.route, action.params)
        : route(action.route);
};

const handleAction = (action) => {
    emit('action', action);
};
</script>