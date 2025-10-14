import { useForm } from '@inertiajs/vue3';
import { provide } from 'vue';

export const useModule = (props = {}) => {
    const module = props.module?.data || {};

    const form = useForm({
        _method: module.id ? "patch" : "post",
        name: module.name ?? null,
        key: module.key ?? null,
        description: module.description ?? null,
        is_active: module.is_active !== undefined ? Boolean(module.is_active) : true,
    });

    provide("form", form);
};
