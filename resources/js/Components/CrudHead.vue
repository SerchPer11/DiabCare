<template>
    <Head>
        <title>{{ title }}</title>
        //falta agregar favicon
    </Head>

</template>

<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: ' ',
    },
});

</script>