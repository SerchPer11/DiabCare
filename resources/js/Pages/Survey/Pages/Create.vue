<template>
    <AuthenticatedLayout>
        <CrudHead :title="title" />
        <div class="max-w-2xl mx-auto mt-6">
            <form @submit.prevent="saveForm">
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Title</label>
                    <input v-model="form.title" type="text" class="input w-full" required />
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Description</label>
                    <textarea v-model="form.description" class="input w-full" rows="2"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Questions</label>
                    <div v-for="(q, idx) in form.questions" :key="idx" class="flex items-center gap-2 mb-2">
                        <input v-model="q.question" type="text" class="input flex-1" placeholder="Question text" required />
                        <button type="button" class="btn btn-danger" @click="removeQuestion(idx)">Remove</button>
                    </div>
                    <button type="button" class="btn btn-info mt-2" @click="addQuestion">Add Question</button>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <BaseButton color="whiteDark" label="Cancel" :to="{ name: routeName + 'index' }" variant="outline" />
                    <BaseButton color="info" label="Save" type="submit" :loading="processing" />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { useSurvey } from '../Composables/useSurvey';
import { ref } from 'vue';

const props = defineProps({
    title: { type: String, default: 'Create Survey' },
    routeName: { type: String, default: 'surveys.' },
});

const { form, saveForm, processing } = useSurvey({ routeName: props.routeName });

function addQuestion() {
    form.questions.push({ question: '' });
}
function removeQuestion(idx) {
    form.questions.splice(idx, 1);
}
</script>

<style scoped>
.input {
    @apply border rounded px-3 py-2 w-full;
}
.btn {
    @apply px-3 py-1 rounded font-semibold;
}
.btn-danger {
    @apply bg-red-500 text-white hover:bg-red-600;
}
.btn-info {
    @apply bg-blue-500 text-white hover:bg-blue-600;
}
</style>
