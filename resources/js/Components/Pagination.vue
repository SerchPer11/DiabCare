<template>
    <div class="flex flex-col lg:flex-row items-center justify-between mt-6">
        <div class="mt-4 lg:mt-0 lg:pr-2 order-2">
            <ul class="pagination flex flex-wrap gap-1 md:flex-nowrap">
                <li v-for="({ url, label, active }, key) in links" :key="key">
                    <component :is="getComponent(url)" v-bind="getLinkProps(url)"
                        :class="getLinkClasses(url, label, active)" :disabled="!url"
                        :aria-label="getAriaLabel(label, active)" :aria-current="active ? 'page' : undefined">
                        <span v-if="isPrevious(label)" class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>

                        <span v-else-if="isNext(label)" class="flex items-center gap-1">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>

                        <span v-else v-html="label"></span>
                    </component>
                </li>
            </ul>
        </div>

        <div class="order-1 text-sm text-gray-600 dark:text-gray-400">
            <span v-if="total > 0">
                Mostrando del {{ from }} al {{ to }} de un total de
                {{ total }} {{ typeRecords }}
            </span>
            <span v-else>Sin {{ typeRecords }}</span>
        </div>
    </div>

    <div class="vl-parent">
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" loader="dots" color="#283C2A" />
    </div>
</template>

<script setup>
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import { usePagination } from "@/Hooks/usePagination";

const props = defineProps({
    typeRecords: {
        type: String,
        default: 'registros',
    },
    preserveScroll: {
        type: Boolean,
        default: true
    },
    preserveState: {
        type: Boolean,
        default: true
    },
    links: {
        type: Array,
        required: true,
    },
    total: {
        type: Number,
        default: 0,
    },
    per_page: {
        type: Number,
        default: 5,
    },
    from: {
        type: Number,
        default: 0,
    },
    to: {
        type: Number,
        default: 0,
    },
})

const { isLoading, getAriaLabel, getLinkProps, getLinkClasses, getComponent, isPrevious, isNext } = usePagination(props);

</script>