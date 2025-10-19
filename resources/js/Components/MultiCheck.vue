<script setup>
import { Checkbox } from '@/Components/ui/checkbox';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    // v-model para el array de los valores seleccionados
    modelValue: {
        type: Array,
        required: true,
    },
    // El array de objetos con todas las opciones disponibles
    options: {
        type: Array,
        required: true,
    },
    // Etiqueta principal para todo el grupo
    label: {
        type: String,
        default: '',
    },
    // Mensaje de error de validación
    error: {
        type: String,
        default: '',
    },
    // Clave del objeto 'option' que se usará como el valor a guardar
    optionValue: {
        type: String,
        default: 'id',
    },
    // Clave del objeto 'option' que se mostrará como etiqueta
    optionLabel: {
        type: String,
        default: 'name',
    },
    // Clave única para el :key del v-for (generalmente 'id')
    optionKey: {
        type: String,
        default: 'id',
    }
});

const emit = defineEmits(['update:modelValue']);

// Función que maneja la lógica de añadir/quitar ítems del array
const handleCheckboxChange = (value, checked = null) => {
    const updatedSelection = [...props.modelValue];
    const index = updatedSelection.indexOf(value);

    // Si recibimos el estado del checkbox, lo usamos; si no, hacemos toggle
    const shouldAdd = checked !== null ? checked : index === -1;

    if (shouldAdd && index === -1) {
        // Agregar si no está en el array
        updatedSelection.push(value);
    } else if (!shouldAdd && index > -1) {
        // Quitar si está en el array
        updatedSelection.splice(index, 1);
    }

    // Notificar al padre del cambio
    emit('update:modelValue', updatedSelection);
};
</script>

<template>
    <div class="space-y-2">
        <Label v-if="label">{{ label }}</Label>

        <div class="border p-4 rounded-md grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div v-for="option in options" :key="option[optionKey]" class="flex items-center space-x-3">

                <Checkbox :id="`checkbox-${option[optionKey]}`" 
                    :model-value="modelValue.includes(option[optionValue])"
                    @update:model-value="(checked) => handleCheckboxChange(option[optionValue], checked)" 
                    class="data-[state=checked]:bg-medic-500 data-[state=checked]:border-medic-500 data-[state=unchecked]:border-medic-300" />

                <Label :for="`checkbox-${option[optionKey]}`" class="font-normal cursor-pointer">
                    {{ option[optionLabel] }}
                </Label>

            </div>
        </div>

        <p v-if="error" class="text-sm font-medium text-destructive">
            {{ error }}
        </p>
    </div>
</template>