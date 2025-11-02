import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed, watch } from 'vue';

export const useHistory = (props = {}) => {
    const { setLoading } = useLoading();

    const history = props.history?.data || {};


    const form = useForm({
        _method: "put",
        diabetes: history.diabetes === 1 ? true : false,
        diabetes_type: history.diabetes_type ?? null,
        diabetes_diagnosis_date: history.diabetes_diagnosis_date ?? null,
        hypertension: history.hypertension === 1 ? true : false,
        hypertension_type: history.hypertension_type ?? null,
        hypertension_diagnosis_date: history.hypertension_diagnosis_date ?? null,
        obesity: history.obesity === 1 ? true : false,
        obesity_type: history.obesity_type ?? null,
        allergies: history.allergies === 1 ? true : false,
        allergies_details: history.allergies_details ?? null,

        
    });

    watch(() => props.user?.data?.profile, (profile) => {
        if (!profile?.weight || !profile?.height) {
            return;
        }

        const alturaEnMetros = profile.height / 100;
        const imc = profile.weight / (alturaEnMetros * alturaEnMetros);

        if (imc >= 25) {
            form.obesity = true;
            if (imc >= 50) form.obesity_type = 'IV';
            else if (imc >= 40) form.obesity_type = 'III';
            else if (imc >= 35) form.obesity_type = 'II';
            else if (imc >= 30) form.obesity_type = 'I';
            else form.obesity_type = 'N';
        } else {
            form.obesity = false;
            form.obesity_type = null;
        } 

    }, { 
        immediate: true 
    });

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
