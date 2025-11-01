
import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { messageConfirmGood } from "@/Hooks/useErrorsForm";

export function useAnswers(routeName) {
    const { setLoading } = useLoading();

    const form = useForm({
        answer: null,
        forum_id: null,
    });
    const answerContents = ref({});

    const saveForm = (questionId) => {
        form.forum_id = questionId;
        form.answer = answerContents.value[questionId] || null;


        messageConfirmGood().then((res) => {
            if (res.isConfirmed) {
                form.post(route(`${routeName}store`), {
                    onBefore: () => setLoading(true),
                    onFinish: () => setLoading(false),
                    onSuccess: () => {
                        answerContents.value[questionId] = null;
                        form.reset();
                    },
                });
            }
        });
    };


    return {
        processing: computed(() => form.processing),
        saveForm,
        answerContents,
        form,
    };
}