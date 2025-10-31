import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from '@/Hooks/useErrorsForm';

export function useSurvey(props = {}) {
    const { setLoading } = useLoading();
    // Use props.survey directly, not props.survey.data
    const survey = props.survey || {};

    const form = useForm({
        _method: survey.id ? 'patch' : 'post',
        title: survey.title ?? null,
        description: survey.description ?? null,
        questions: survey.questions ? survey.questions.map(q => ({ question: q.question, id: q.id })) : [],
    });

    provide('form', form);
    provide('questions', form.questions);

    const saveForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        form.post(route(`${props.routeName}update`, { survey: survey.id }), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const destroyForm = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { survey: survey.id }));
            }
        });
    };

    const deleteForm = (surveyId) => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, surveyId));
            }
        });
    };

    return {
        processing: computed(() => form.processing),
        saveForm,
        updateForm,
        destroyForm,
        deleteForm,
        form,
    };
}
