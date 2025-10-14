<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />

        <IndexBanner :title="title" :icon="mdiViewModule" :routeName="routeName" main />

        <ModuleRecords :modules="modules" :routeName="routeName" />
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ModuleRecords from '../Components/ModuleRecords.vue';
import IndexBanner from '@/Components/IndexBanner.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiViewModule } from '@mdi/js';


const props = defineProps({
    title: {
        type: String,
        default: 'Módulos'
    },
    routeName: {
        type: String,
        default: 'modules.'
    },
    modules: {
        type: Object,
        required: true
    }
});
</script>