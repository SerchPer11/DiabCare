import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useMedication = (props = {}) => {
    const { setLoading } = useLoading();

    const medication = props.medication?.data || {};


    const form = useForm({
        _method: medication.id ? "patch" : "post",
        name: medication.name ?? null,
        concentration: medication.concentration ?? null,
        indications: medication.indications ?? null,
        contraindications: medication.contraindications ?? null,
        medication_type_id: medication.medication_type_id ?? null,
        medication_presentation_id: medication.medication_presentation_id ?? null,
        medication_administration_id: medication.medication_administration_id ?? null,
        unit_id: medication.unit_id ?? null,

        photos: medication.photos ?? [],
    });

    provide("form", form);
    provide("types", props.types);
    provide("presentations", props.presentations);
    provide("administrations", props.administrations);
    provide("units", props.units);

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
        form.post(route(`${props.routeName}update`, { medication: medication.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { medication: medication.id }));
            }
        });
    };

    const deleteForm = (medication) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, medication));
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
