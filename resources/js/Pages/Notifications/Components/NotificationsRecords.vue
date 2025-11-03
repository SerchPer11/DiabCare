<template>
    <CardBox v-if="notifications.data && notifications.data.length > 0" class="mt-2">
        <CardBox v-for="item in notifications.data" :key="item.id" class="mt-4">
            <Link :href="item.link" class="flex items-center p-4">

            <div class="flex-shrink-0 mr-4">
                <BaseIcon :path="mdiBell" size="24" h="h-10" w="w-10"
                    class="p-2 rounded-full text-medic-500 bg-medic-100" />
            </div>

            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">
                    {{ item.message }}
                </p>
                <span class="text-xs text-gray-500">
                    {{ item.created_at }}
                </span>
            </div>

            <div class="flex-shrink-0 ml-4 text-xs">
                <span v-if="item.read_at" class="text-gray-400">
                    Leído
                </span>
                <span v-else class="px-2 py-1 font-bold text-blue-600 bg-blue-100 rounded-full">
                    Nueva
                </span>
            </div>
            </Link>
        </CardBox>
        <Pagination v-if="notifications?.meta" :links="notifications.meta.links" :total="notifications.meta.total"
            :to="notifications.meta.to" :from="notifications.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">No hay registros</span>
        </div>
    </CardBox>

</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import Pagination from "@/Components/Pagination.vue";
import BaseIcon from '@/Components/BaseIcon.vue';
import { Link } from '@inertiajs/vue3';
import { mdiBell } from '@mdi/js';

const props = defineProps({
    notifications: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'notifications.'
    }
});
</script>