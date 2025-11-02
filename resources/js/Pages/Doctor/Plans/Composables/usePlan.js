import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const usePlan = (props = {}) => {
    const { setLoading } = useLoading();

    // Manejar correctamente cuando plan es null/undefined o cuando tiene .data
    const plan = props.plan?.data || props.plan || {};
    


    const form = useForm({
        _method: plan.id ? "patch" : "post",
        patient_id: plan.patient_id ?? null,
        plan_type_id: plan.plan_type_id ?? null,
        title: plan.title ?? '',
        description: plan.description ?? '',
        start_date: plan.start_date ?? null,
        end_date: plan.end_date ?? null,
        status: plan.status ?? 'activo',
        elements: plan.elements?.map(element => ({
            id: element.id,
            food_id: element.food_id,
            exercise_id: element.exercise_id,
            quantity: element.quantity,
            unit: element.unit,
            frequency: element.frequency,
            intensity: element.intensity,
            time_schedule: element.time_schedule,
            instructions: element.instructions,
            notes: element.notes,
            order: element.order
        })) ?? []
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
        form.post(route(`${props.routeName}update`, { plan: plan.id }), {
            onBefore: () => {
                setLoading(true);
            },
            onFinish: () => {
                setLoading(false);
            },
        });
    };

    const destroyForm = () => {
        const message = '¿Estás seguro de eliminar este plan?';

        messageConfirm(message).then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { plan: plan.id }));
            }
        });
    };

    const deleteForm = (plan) => {
        const message = '¿Estás seguro de eliminar este plan?';

        messageConfirm(message).then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, plan));
            }
        });
    };

    const duplicateForm = (plan) => {
        messageConfirm('¿Deseas crear una copia de este plan?').then((res) => {
            if (res.isConfirmed) {
                form.post(route(`${props.routeName}duplicate`, { plan: plan.id }), {
                    onBefore: () => {
                        setLoading(true);
                    },
                    onFinish: () => {
                        setLoading(false);
                    },
                });
            }
        });
    };

    // Métodos para manejar elementos del plan
    const addElement = () => {
        form.elements.push({
            food_id: null,
            exercise_id: null,
            quantity: '',
            unit: '',
            frequency: '',
            intensity: '',
            time_schedule: '',
            instructions: '',
            notes: '',
            order: form.elements.length
        });
    };

    const removeElement = (index) => {
        const element = form.elements[index];
        if (element.id) {
            // Marcar para eliminar en el backend
            element._delete = true;
        } else {
            // Elemento nuevo, eliminar directamente
            form.elements.splice(index, 1);
        }
    };

    // Provide all necessary data and functions for the form component
    provide("form", form);
    provide("patients", props.patients);
    provide("planTypes", props.planTypes);
    provide("foods", props.foods);
    provide("exercises", props.exercises);
    provide("addElement", addElement);
    provide("removeElement", removeElement);

    return {
        processing: computed(() => form.processing),
        saveForm,
        updateForm,
        destroyForm,
        deleteForm,
        duplicateForm,
        addElement,
        removeElement,
        form,
    };
};