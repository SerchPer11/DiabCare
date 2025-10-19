<script setup>
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { mdiCheckAll } from '@mdi/js';
import BaseButton from './BaseButton.vue';

// --- Props Genéricos para la Reutilización ---
const props = defineProps({
    // v-model para el array de ítems seleccionados
    modelValue: {
        type: Array,
        required: true,
    },
    // El array de objetos para el select principal (ej. tus módulos)
    groups: {
        type: Array,
        required: true,
    },
    // El objeto con todos los ítems, agrupados por una clave (ej. tus permisos)
    items: {
        type: Object,
        required: true,
    },
    // Título para la Card
    title: {
        type: String,
        default: ''
    },
    // Placeholder para el Select
    placeholderSelect: {
        type: String,
        default: ''
    },
    // Clave del objeto 'group' que se usará como valor en el select
    groupValueKey: {
        type: String,
        default: ''
    },
    // Clave del objeto 'group' que se mostrará en el select
    groupLabelKey: {
        type: String,
        default: ''
    },
    // Clave del objeto 'item' que se guardará en el v-model
    itemValueKey: {
        type: String,
        default: ''
    },
    // Clave del objeto 'item' que se mostrará junto al checkbox
    itemLabelKey: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

// v-model bidireccional para los permisos seleccionados
const selectedItems = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

// Estado para guardar la clave del grupo seleccionado en el select
const selectedGroupKey = ref(null);

// Propiedad computada que filtra los ítems a mostrar según el grupo seleccionado
const filteredItems = computed(() => {
    if (!selectedGroupKey.value || !props.items[selectedGroupKey.value]) {
        return [];
    }
    return props.items[selectedGroupKey.value];
});

const handleCheckboxChange = (itemValue, checked = null) => {
    // Creamos una copia mutable del array actual para no modificar props
    const updatedItems = [...props.modelValue];
    const index = updatedItems.indexOf(itemValue);

    // Si recibimos el estado del checkbox, lo usamos; si no, hacemos toggle
    const shouldAdd = checked !== null ? checked : index === -1;

    if (shouldAdd && index === -1) {
        // Agregar si no está en el array
        updatedItems.push(itemValue);
    } else if (!shouldAdd && index > -1) {
        // Quitar si está en el array
        updatedItems.splice(index, 1);
    }

    // Emitimos el array completo y actualizado al componente padre
    emit('update:modelValue', updatedItems);
};

const selectAll = (event) => {
    event?.preventDefault();

    // Obtener todos los valores de los items filtrados
    const allItemValues = filteredItems.value.map(item => item[props.itemValueKey]);

    // Crear un array con los items actuales más los nuevos (sin duplicados)
    const updatedItems = [...new Set([...props.modelValue, ...allItemValues])];

    emit('update:modelValue', updatedItems);
};

const deselectAll = (event) => {
    event?.preventDefault();

    // Obtener todos los valores de los items filtrados
    const allItemValues = filteredItems.value.map(item => item[props.itemValueKey]);

    // Filtrar el array actual quitando todos los items del grupo actual
    const updatedItems = props.modelValue.filter(item => !allItemValues.includes(item));

    emit('update:modelValue', updatedItems);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <div>
                <Label>Selecciona un Módulo</Label>
                <Select v-model="selectedGroupKey">
                    <SelectTrigger>
                        <SelectValue :placeholder="placeholderSelect" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="group in groups" :key="group.id" :value="group[groupValueKey]">
                            {{ group[groupLabelKey] }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Botones de selección masiva -->
            <div v-if="selectedGroupKey && filteredItems.length > 0" class="flex gap-2 justify-end">
                <BaseButton :icon="mdiCheckAll" type="button" @click="selectAll" small
                    class="text-secondary" color="info"
                    label="Seleccionar Todo"
                />
                <BaseButton :icon="mdiCheckAll" type="button" @click="deselectAll" small
                    class="text-secondary hover:bg-red-400" color="danger"
                    label="Quitar Todo"
                />
            </div>

            <div v-if="selectedGroupKey" :key="selectedGroupKey"
                class="border p-4 rounded-md grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-72 overflow-y-auto">
                <p v-if="filteredItems.length === 0" class="text-sm text-muted-foreground">
                    Este módulo no tiene permisos definidos.
                </p>
                <div v-else v-for="item in filteredItems" :key="item.id" class="flex items-center space-x-3">
                    <Checkbox :id="`item-${item.id}`" :model-value="props.modelValue.includes(item[itemValueKey])"
                        @update:model-value="(checked) => handleCheckboxChange(item[itemValueKey], checked)"
                        class="data-[state=checked]:bg-medic-500 data-[state=checked]:border-medic-500 data-[state=unchecked]:border-medic-300" />

                    <Label :for="`item-${item.id}`" class="font-normal cursor-pointer">
                        {{ item[itemLabelKey] }} ({{ item.name }})
                    </Label>
                </div>
            </div>
        </CardContent>
    </Card>
</template>