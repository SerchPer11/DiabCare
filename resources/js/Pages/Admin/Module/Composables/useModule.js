import { useLoading } from'@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide,computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useModule = (props = {}) => {
    const { setLoading } = useLoading();

    const module = props.module || {};

    const form = useForm({
        _method: module.id ? "patch" : "post",
        name: module.name ?? null,
        key: module.key ?? null,
        description: module.description ?? null,
        //is_active: module.is_active !== undefined ? Boolean(module.is_active) : true,
    });

    provide("form", form);

    const saveForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
        });
    };
    const updateForm = () => {
        form.post(route(`${props.routeName}update`, { module: module.id }), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
        });
    };

    const destroyForm = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { module: module.id }));
            }
        });
    };

    const deleteForm = (module) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, module));
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
};
