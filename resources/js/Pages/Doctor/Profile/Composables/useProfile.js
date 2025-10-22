import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";
import { last } from 'lodash';

export const useProfile = (props = {}) => {
    const { setLoading } = useLoading();

    const profile = props.profile?.data || {};


    const form = useForm({
        //_method: profile.id ? "patch" : "post",
        name: profile.name ?? null,
        last_name: profile.last_name ?? null,
        email: profile.email ?? null,
        phone: profile.phone ?? null,
        gender: profile.gender ?? null,
        specialty_id: profile.profile?.specialty_id ?? null,
        license_number: profile.profile?.license_number ?? null,
        titulation_date: profile.profile?.titulation_date ?? null,
        birthdate: profile.birthdate ?? null,
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
