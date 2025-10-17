import { reactive, watch } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { useLoading } from "./useLoading";

export const useFilters = (filtersOn, routeName) => {
    const { isLoading, setLoading } = useLoading();
    const filters = reactive({});

    Object.keys(filtersOn).forEach(key => {
        filters[key] = filtersOn[key];
    });

    const applyFilters = (preserveState) => {
        setLoading(true);
        router.get(route(`${routeName}index`), filters, {
            preserveScroll: true,
            preserveState: preserveState,
            replace: true,
            onFinish: () => {
                setLoading(false);
            }
        });
    };

    const clearFilters = () => {
        router.get(route(`${routeName}index`), {
            replace: true,
        });
    };

    const sortByColumn = (order, direction) => {
        filters.order = order;
        filters.direction = direction;
        applyFilters(true);
    };

    watch(
        () => filters.search,
        debounce(function () {
            applyFilters(true);
        }, 500)
    );

    return {
        //
        filters,
        applyFilters,
        clearFilters,
        sortByColumn,
        isLoading
    };
};
