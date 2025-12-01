<script setup>
import { ref, watch } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiFilter , mdiFilterRemove } from '@mdi/js';

const props = defineProps({
    filters: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(['filter']);

const filterValues = ref(
    props.filters.reduce((acc, filter) => {
        acc[filter.name] = filter.value;
        return acc;
    }, {})
);


const formatOptionsForSelect = (optionsObject) => {
    if (!optionsObject || Array.isArray(optionsObject)) {
        return optionsObject;
    }

    return Object.keys(optionsObject).map(key => ({
        id: key,
        name: optionsObject[key]
    }));
};

const applyFilters = () => {
    emit('filter', filterValues.value);
};

watch(filterValues, (newValues) => {
    emit('filter', newValues);
}, { deep: true }); 

const clearFilters = () => {
    for (const key in filterValues.value) {
        filterValues.value[key] = null;
    }

    emit('filter', filterValues.value);
};

</script>

<template>
    <CardBox>
        <div
            :class="`grid grid-cols-1 md:grid-cols-${props.filters.length - 1} lg:grid-cols-${props.filters.length + 1} gap-4 items-end w-full`">
            <div v-for="filter in props.filters" :key="filter.name">
                <BaseFormField :type="filter.type" :label="filter.label" :placeholder="filter.label"
                    :options="formatOptionsForSelect(filter.options)" v-model="filterValues[filter.name]"
                    :valueSelect="'id'" :valueOption="'name'" />
            </div>

            <div class="justify-self-center mt-auto mb-auto flex flex-col md:flex-row gap-2">
                <BaseButton label="Filtrar" color="info" @click="applyFilters" class="w-full md:w-auto"
                    :icon="mdiFilter " />
                <BaseButton label="Limpiar" color="lightDark" variant="outline" :icon="mdiFilterRemove"
                    @click="clearFilters" class="w-full md:w-auto" />
            </div>
        </div>
    </CardBox>
</template>
