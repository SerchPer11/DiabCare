import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useRole = (props = {}) => {
    const { setLoading } = useLoading();

    const role = props.role?.data || {};

    const form = useForm({
        _method: role.id ? "patch" : "post",
        name: role.name ?? null,
        guard_name: role.guard_name ?? 'web',
        description: role.description ?? null,

        permissions: role.permissions?.map(permission => permission.id) ?? [],
    });

    provide("form", form);
    provide("permissions", props.permissions);
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
        form.post(route(`${props.routeName}update`, { role: role.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { role: role.id }));
            }
        });
    };

    const deleteForm = (role) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, role));
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
