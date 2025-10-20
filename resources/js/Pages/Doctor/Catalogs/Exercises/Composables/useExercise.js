import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from '@/Hooks/useErrorsForm';
import { map } from 'lodash';

export const useExercise = (props = {}) => {
    const { setLoading } = useLoading();
    // Usar props.exercise directamente, no props.exercise.data
    const exercise = props.exercise || {};

    const form = useForm({
        _method: exercise.id ? 'patch' : 'post',
        name: exercise.name ?? '',
        exercise_type_id: exercise.exercise_type_id ?? '',
        intensity: exercise.intensity ? exercise.intensity.charAt(0).toUpperCase() + exercise.intensity.slice(1).toLowerCase() : 'Baja',
        duration_minutes: exercise.duration_minutes ?? null,
        description: exercise.description ?? null,
        calories_burned: exercise.calories_burned ?? null,
        sets: exercise.sets ?? null,
        repetitions: exercise.repetitions ?? null,
        rest_seconds: exercise.rest_seconds ?? null,
        equipment: exercise.equipment ?? null,
        contraindications: exercise.contraindications ?? null,
        notes: exercise.notes ?? null,
        is_active: exercise.is_active ? 'Activo' : 'Inactivo',
    });

    provide('form', form);
    provide('exerciseTypes', props.exerciseTypes);

    const saveForm = () => {
        // Transformar is_active de string a boolean
        const transformedData = {
            ...form.data(),
            is_active: form.is_active === 'Activo'
        };
        
        form.transform(() => transformedData).post(route(`${props.routeName}store`), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const updateForm = () => {
        const transformedData = {
            ...form.data(),
            is_active: form.is_active === 'Activo'
        };
        form.transform(() => transformedData).post(route(`${props.routeName}update`, { exercise: exercise.id }), {
            onBefore: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    };

    const destroyForm = () => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, { exercise: exercise.id }));
            }
        });
    };

    const deleteForm = (exerciseId) => {
        messageConfirm().then((res) => {
            if (res.isConfirmed) {
                form.delete(route(`${props.routeName}destroy`, exerciseId));
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
