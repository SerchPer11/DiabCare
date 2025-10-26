import { useLoading } from '@/Hooks/useLoading';
import { useForm } from '@inertiajs/vue3';
import { provide, computed } from 'vue';
import { messageConfirm } from "@/Hooks/useErrorsForm";

export const useFood = (props = {}) => {
    const { setLoading } = useLoading();

    const food = props.food?.data || {};


    const form = useForm({
        _method: food.id ? "patch" : "post",
        name: food.name ?? null,
        calories: food.calories ?? null,
        protein: food.protein ?? null,
        carbohydrates: food.carbohydrates ?? null,
        fats: food.fats ?? null,
        fiber: food.fiber ?? null,
        description: food.description ?? null,
        portion_size: food.portion_size ?? null,
        unit_id: food.unit_id ?? null,
        food_group_id: food.food_group_id ?? null,

        photos: food.photos ?? [],
    });

    provide("form", form);
    provide("foodGroups", props.foodGroups);
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
        form.post(route(`${props.routeName}update`, { food: food.id }), {
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
                form.delete(route(`${props.routeName}destroy`, { food: food.id }));
            }
        });
    };

    const deleteForm = (food) => {
            messageConfirm().then((res) => {
                if (res.isConfirmed) {
                    form.delete(route(`${props.routeName}destroy`, food));
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
