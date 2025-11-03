import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useClinicalLogBook = (props = {}) => {
    const { setLoading } = useLoading();

    // Manejar correctamente cuando entry es null/undefined o cuando tiene .data
    const entry = props.entry?.data || props.entry || {};

    const form = useForm({
        _method: entry.id ? "patch" : "post",
        patient_id: entry.patient_id ?? null,
        event_type: entry.event_type ?? 'observation',
        title: entry.title ?? '',
        description: entry.description ?? '',
        notes: entry.notes ?? '',
        related_type: entry.related_type ?? null,
        related_id: entry.related_id ?? null,
        photos: entry.photos ?? [],
        files: entry.files ?? []
    });

    const saveForm = () => {
        // Prevenir envío si el formulario ya se está procesando
        if (form.processing) {
            return;
        }
        
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
        form.post(route(`${props.routeName}update`, { clinical_logbook: entry.id }), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
        });
    };

    const destroyForm = () => {
        const message = '¿Estás seguro de eliminar esta entrada de bitácora?';

        messageConfirm(message).then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { clinical_logbook: entry.id }));
            }
        });
    };

    const deleteForm = (entry) => {
        const message = '¿Estás seguro de eliminar esta entrada de bitácora?';

        messageConfirm(message).then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, entry));
            }
        });
    };

    // Provide all necessary data and functions for the form component
    provide("form", form);
    provide("patients", props.patients);
    provide("eventTypes", props.eventTypes);
    provide("appointments", props.appointments);
    provide("plans", props.plans);
    provide("medications", props.medications);

    return {
        processing: computed(() => form.processing),
        saveForm,
        updateForm,
        destroyForm,
        deleteForm,
        form,
    };
};