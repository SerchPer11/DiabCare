<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h4 class="font-medium text-gray-900 mb-2">{{ title }}</h4>
            <p class="text-gray-600 text-sm mb-4">{{ description }}</p>
            <Link
                v-if="!external"
                :href="routeUrl"
                :class="buttonClasses"
            >
                <BaseIcon v-if="icon" :path="iconPath" class="w-4 h-4 mr-2" />
                {{ buttonText }}
            </Link>
            <a
                v-else
                :href="routeUrl"
                target="_blank"
                rel="noopener noreferrer"
                :class="buttonClasses"
            >
                <BaseIcon v-if="icon" :path="iconPath" class="w-4 h-4 mr-2" />
                {{ buttonText }}
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseIcon from '@/Components/BaseIcon.vue';
import { 
    mdiAccountPlus, 
    mdiCalendar, 
    mdiDatabase, 
    mdiCalendarPlus, 
    mdiClipboardList, 
    mdiFileDocumentOutline,
    mdiClipboardPulse,
    mdiCalendarCheck
} from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    route: {
        type: String,
        required: true
    },
    routeParams: {
        type: Object,
        default: () => ({})
    },
    buttonText: {
        type: String,
        default: 'Ir'
    },
    color: {
        type: String,
        default: 'blue',
        validator: value => ['blue', 'green', 'red', 'purple', 'yellow', 'gray', 'indigo'].includes(value)
    },
    icon: {
        type: String,
        default: ''
    },
    external: {
        type: Boolean,
        default: false
    }
});

const iconMap = {
    'user-plus': mdiAccountPlus,
    'calendar': mdiCalendar,
    'database': mdiDatabase,
    'calendar-plus': mdiCalendarPlus,
    'clipboard-list': mdiClipboardList,
    'file-text': mdiFileDocumentOutline,
    'activity': mdiClipboardPulse,
    'calendar-check': mdiCalendarCheck
};

const iconPath = computed(() => {
    return iconMap[props.icon] || mdiCalendar;
});

const buttonClasses = computed(() => {
    const baseClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors';
    
    const colorMap = {
        blue: 'bg-blue-600 hover:bg-blue-700',
        green: 'bg-green-600 hover:bg-green-700',
        red: 'bg-red-600 hover:bg-red-700',
        purple: 'bg-purple-600 hover:bg-purple-700',
        yellow: 'bg-yellow-600 hover:bg-yellow-700',
        gray: 'bg-gray-600 hover:bg-gray-700',
        indigo: 'bg-indigo-600 hover:bg-indigo-700'
    };
    
    return `${baseClasses} ${colorMap[props.color] || colorMap.blue}`;
});

const routeUrl = computed(() => {
    if (props.external) {
        return props.route;
    }
    return Object.keys(props.routeParams).length > 0 
        ? route(props.route, props.routeParams)
        : route(props.route);
});
</script>