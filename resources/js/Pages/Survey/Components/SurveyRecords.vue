
<template>
    <CardBox class="mt-2">
        <template v-if="Array.isArray(surveys.data) && surveys.data.length > 0">
            <div class="flex justify-end mb-4 md:mr-10" v-if="!isPatient">
                <BaseButton color="info" :icon="mdiPlus" label="Add" title="Add survey"
                    :routeName="`${routeName}create`" />
            </div>
            <table class="w-full text-center md:table-fixed sm:table-auto shadow-md">
                <thead class="h-12 border-gray-200 bg-medic-50 text-gray-600 shadow-sm">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Questions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in surveys.data" :key="item.id"
                        class="border border-gray-200 h-12 bg-gray-50 shadow-sm text-gray-500">
                        <td>{{ item.title }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.questions?.length ?? 0 }}</td>
                        <td>
                            <div class="flex gap-4 justify-center">
                                <BaseButton v-if="!isPatient" color="info" :icon="mdiPencil" small :routeName="`${routeName}edit`"
                                    :parameter="item.id" title="Edit survey" />
                                <BaseButton v-if="!isPatient" color="danger" :icon="mdiDelete" small title="Delete survey"
                                    @click="deleteSurvey(item.id)" />
                                <BaseButton v-if="isPatient" color="info" label="Answer" title="Answer survey"
                                    :routeName="`${routeName}answer`" :parameter="{ survey: item.id }" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination v-if="surveys?.meta" :links="surveys.meta.links" :total="surveys.meta.total" :to="surveys.meta.to"
            :from="surveys.meta.from" />
        </template>
        <template v-else>
            <div class="flex items-center justify-center gap-4 py-8">
                <span class="text-gray-500 text-lg">Sin encuestas</span>
                <BaseButton v-if="!isPatient" color="info" :icon="mdiPlus" label="Add" title="Add survey"
                    :routeName="`${routeName}create`" />
            </div>
        </template>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPencil, mdiDelete, mdiPlus } from '@mdi/js';
import { useSurvey } from '../Composables/useSurvey';
import Pagination from '@/Components/Pagination.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    surveys: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'surveys.'
    }
});

const { deleteForm } = useSurvey(props);

const deleteSurvey = (id) => {
    deleteForm(id);
};

const page = usePage();
const isPatient = page.props.auth?.roles?.includes('patient');
</script>
