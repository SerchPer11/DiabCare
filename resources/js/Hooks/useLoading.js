import { ref } from "vue";

export const useLoading = () => {
    const isLoading = ref(false);

    const setLoading = (value) => {
        isLoading.value = value;
    };

    return {
        isLoading,
        setLoading
    }
};