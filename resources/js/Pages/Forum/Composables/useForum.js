import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirmGood } from "@/Hooks/useErrorsForm";

export const useForum = (props = {}) => {
    const { setLoading } = useLoading();

    const question = props.question?.data || {};

    const form = useForm({
        _method: question.id ? "patch" : "post",
        title: question.title ?? null,
        content: question.content ?? null,
        category_id: question.category_id ?? null,
    });

    provide("form", form);
    provide("categories", props.categories);

    const saveForm = () => {
        messageConfirmGood().then((res) => {
            if (res.isConfirmed) {
                form.post(route(`${props.routeName}store`), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
            onSuccess: () => {
                form.reset();
            }
        });
            }
        });
    };

    return {
        processing: computed(() => form.processing),
        saveForm,
        form,
    };
};
