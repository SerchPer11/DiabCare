<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <CrudHeadWhioutFilters :title="title" main :icon="mdiFileChart " />

        <ReportGeneral :routeName="routeName"/>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHeadWhioutFilters from '@/Components/CrudHeadWhioutFilters.vue';
import ReportGeneral from '@/Pages/Reports/Components/ReportGeneral.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiFileChart  } from '@mdi/js';

const props = defineProps({
    title: {
        type: String,
        default: 'Reportes'
    },
    routeName: {
        type: String,
        required: true
    }
});
</script>