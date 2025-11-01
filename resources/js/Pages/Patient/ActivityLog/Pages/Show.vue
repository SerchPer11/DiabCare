<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <ActivityLogHead :title="title" main :icon="mdiArchiveClock" />
        <ActivityLogRecords :clinicalLogs="clinicalLogs" :routeName="routeName" />

        <CrudButtons>
            <div v-if="userPrimaryRole !== 'patient'" class="flex gap-2 justify-end">
                <BaseButton :icon="mdiArrowULeftBottom" color="whiteDark" label="Volver" variant="outline"
                    :routeName="`patients.index`" />
            </div>
            <div v-if="userPrimaryRole === 'patient'" class="flex gap-2 justify-end">
                <BaseButton :icon="mdiArrowULeftBottom" color="whiteDark" label="Volver" variant="outline"
                    :routeName="`patient.profile.show`" />
            </div>
        </CrudButtons>
    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import { mdiArchiveClock  } from '@mdi/js';
import ActivityLogRecords from '../Components/ActivityLogRecords.vue';
import ActivityLogHead from '../Components/ActivityLogHead.vue';
import { mdiArrowULeftBottom } from '@mdi/js';
import CrudButtons from '@/Components/CrudButtons.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { computed } from 'vue';


const props = defineProps({
    title: {
        type: String,
        default: 'Seguimiento clinico'
    },
    routeName: {
        type: String,
        default: 'Measures.'
    },
    clinicalLogs: {
        type: Object,
        required: true
    }
});

const userPrimaryRole = computed(() => {
  return usePage().props.auth?.roles?.[0] ?? null;
});
import { usePage } from '@inertiajs/vue3';
</script>