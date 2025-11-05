import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";
import { last } from 'lodash';

export const useUser = (props = {}) => {
    const { setLoading } = useLoading();

    const user = props.user?.data || {};


    const form = useForm({
        _method: user.id ? "patch" : "post",
        name: user.name ?? null,
        last_name: user.last_name ?? null,
        second_last_name: user.second_last_name ?? null,
        email: user.email ?? null,
        password: null,
        password_confirmation: null,

        roles: user.roles?.map(role => role.id) ?? [],

        placehHolder: user.id ? "Dejar en blanco para mantener la contraseña actual" : "Crea una contraseña",
        is_required: user.id ? false : true,
    });

    provide("form", form);
    provide("roles", props.roles);

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
        form.post(route(`${props.routeName}update`, { user: user.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { user: user.id }));
            }
        });
    };

    const deleteForm = (user) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, user));
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
