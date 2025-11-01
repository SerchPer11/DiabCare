import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";
import { last } from 'lodash';

export const useProfile = (props = {}) => {
    const { setLoading } = useLoading();

    const profile = props.profile?.data || {};


    const form = useForm({
        _method: "put",
        id: profile.id ?? null,
        name: profile.name ?? null,
        last_name: profile.last_name ?? null,
        email: profile.email ?? null,
        phone: profile.phone ?? null,
        gender: profile.gender ?? null,
        birthdate: profile.birthdate ?? null,

        blood_type: profile.profile?.blood_type ?? null,
        height: profile.profile?.height ?? null,
        weight: profile.profile?.weight ?? null,
        roles: profile.roles ?? [],

        current_password: null,
        password: null,
        password_confirmation: null,
    });

    provide("specialties", props.specialties);
    provide("form", form);

    const saveForm = () => {
        form.post(route(`${props.routeName}update`), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
        });
    };

    return {
        processing: computed(() => form.processing),
        saveForm,
        form,
    };
};
