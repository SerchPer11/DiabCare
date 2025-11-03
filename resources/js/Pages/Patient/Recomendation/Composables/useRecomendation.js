import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useRecomendation = (props = {}) => {
    const { setLoading } = useLoading();

    const recomendation = props.recomendation?.data || {};

    const form = useForm({
        _method: recomendation.id ? "patch" : "post",
        title: recomendation.title ?? null,
        recomendation_type_id: recomendation.recomendation_type_id ?? null,
        priority: recomendation.priority ?? null,
        content: recomendation.content ?? null,
        start_date: recomendation.start_date ?? null,
        end_date: recomendation.end_date ?? null,
        patient_id: recomendation.patient_id ?? null,
        doctor_id: recomendation.doctor_id ?? null,
        is_active: recomendation.is_active ?? true,

        photos: recomendation.photos ?? [],
        files: recomendation.files ?? [],
    });

    provide("form", form);
    provide("types", props.types);
    provide("priorities", props.priorities);
    provide("patients", props.patients);
    provide("doctors", props.doctors);

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
        form.post(route(`${props.routeName}update`, { recomendation: recomendation.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { recomendation: recomendation.id }));
            }
        });
    };

    const deleteForm = (recomendation) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, recomendation));
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
