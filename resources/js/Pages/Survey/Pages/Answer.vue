<template>
    <AuthenticatedLayout>
        <CrudHead :title="survey?.title || 'Answer Survey'" />
        <div class="max-w-2xl mx-auto mt-6">
            <form @submit.prevent="submitForm">
                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-2">{{ survey?.title }}</h2>
                    <p class="text-gray-600 mb-4">{{ survey?.description }}</p>
                </div>
                <div v-for="(question, idx) in survey.questions" :key="question.id" class="mb-6">
                    <label class="block mb-2 font-semibold">{{ idx + 1 }}. {{ question.question }}</label>
                    <div class="flex gap-4">
                        <label v-for="value in [1,2,3,4,5]" :key="value" class="flex flex-col items-center">
                            <input type="radio" :name="'q_' + question.id" :value="value" v-model="answers[question.id]" required />
                            <span class="text-xs mt-1">{{ likertLabels[value-1] }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors[`answers.${idx}.likert_value`] || form.errors[`answers.${idx}.survey_question_id`]" />
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <BaseButton color="whiteDark" label="Cancel" :to="{ name: routeName + 'index' }" variant="outline" />
                    <BaseButton color="info" label="Submit" type="submit" :loading="processing" />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrudHead from '@/Components/CrudHead.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    survey: { type: Object, required: true },
    routeName: { type: String, default: 'surveys.' },
});

const likertLabels = [
    'Strongly Disagree',
    'Disagree',
    'Neutral',
    'Agree',
    'Strongly Agree'
];

const answers = ref({});
const processing = ref(false);

const form = useForm({
    answers: [],
});

function submitForm() {
    form.answers = props.survey.questions.map(q => ({
        survey_question_id: q.id,
        likert_value: Number(answers.value[q.id])
    }));
    processing.value = true;
    form.post(route(`${props.routeName}submit`, { survey: props.survey.id }), {
        onFinish: () => { processing.value = false; },
    });
}
</script>

<style scoped>
input[type="radio"]:checked {
    accent-color: #2563eb;
}
</style>
