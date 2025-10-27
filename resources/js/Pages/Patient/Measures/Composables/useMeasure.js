// PASO 1: Importar 'router' y 'watch'
import { useLoading } from '@/Hooks/useLoading';
import { useForm, router } from '@inertiajs/vue3'; // <-- AÑADIR router
import { provide, computed, watch } from 'vue'; // <-- AÑADIR watch
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useMeasure = (props = {}) => {
    const { setLoading } = useLoading();
    const measure = props.measure?.data || {};
    const routeName = measure.id ? 'measures.update' : 'measures.create';

    const timeString = measure.hour_measured ?? '';
    const form = useForm({
        _method: measure.id ? "patch" : "post",
        patient_id: measure.measureConfig?.patient_id ?? null,
        measure_type_id: measure.measureConfig?.measure_type_id ?? null,
        measured_at: measure.measured_at ?? null,
        hour_measured: timeString ? timeString.substring(0, 5) : null,
        value: measure.value ?? null,
        notes: measure.notes ?? null,
        min_value: measure.measureConfig?.min_value ?? null,
        max_value: measure.measureConfig?.max_value ?? null,
        range: measure.measureConfig?.range ?? null,
        severity: measure.measureConfig?.severity ?? null,
        frequency: measure.measureConfig?.frequency ?? null,
    });

    provide("form", form);
    provide("patients", props.patients);
    provide("types", props.types);
    provide("frequencies", props.frequencies);
    provide("severities", props.severities);
    provide("ranges", props.ranges);

    const fetchConfig = () => {
        if (form.patient_id && form.measure_type_id) {
            router.get(route(routeName), {
                patient_id: form.patient_id,
                measure_type_id: form.measure_type_id
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['config'],
                onSuccess: (page) => {
                    if (page.props.config) {
                        const config = page.props.config;
                        form.min_value = config.min_value;
                        form.max_value = config.max_value;
                        form.range = config.range;
                        form.severity = config.severity;
                        form.frequency = config.frequency;
                    } else {
                        form.reset(
                            'min_value',
                            'max_value',
                            'range',
                            'severity',
                            'frequency'
                        );
                    }
                }
            });
        }
    }

    watch(() => form.patient_id, fetchConfig);
    watch(() => form.measure_type_id, fetchConfig);

    

    const saveForm = () => {
        form.post(route(`${props.routeName}store`), {
            onBefore: () => { setLoading(true); },
            onFinish: () => { setLoading(false); },
        });
    };
    const updateForm = () => {
        form.post(route(`${props.routeName}update`, { measure: measure.id }), {
            onBefore: () => { setLoading(true); },
            onFinish: () => { setLoading(false); },
        });
    };

    const destroyForm = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { measure: measure.id }));
            }
        });
    };

    const deleteForm = (measure) => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, measure));
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
}; // <--- FIN DE useMeasure

// PASO 2: ELIMINAR LA LLAVE '}' EXTRA QUE ESTABA AQUÍ