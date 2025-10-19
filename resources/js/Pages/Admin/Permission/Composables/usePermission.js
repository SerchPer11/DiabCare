import { useLoading } from'@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide,computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const usePermission = (props = {}) => {
    const { setLoading } = useLoading();

    const permission = props.permission || {};

    const form = useForm({
        _method: permission.id ? "patch" : "post",
        name: permission.name ?? null,
        guard_name: permission.guard_name ?? 'web',
        description: permission.description ?? null,
        module_key: permission.module_key ?? null,
    });

    provide("form", form);
    provide("modules", props.modules);

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
        form.post(route(`${props.routeName}update`, { permission: permission.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { permission: permission.id }));
            }
        });
    };

    const deleteForm = (permission) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, permission));
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
