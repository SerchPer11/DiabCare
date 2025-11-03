<script setup>
import { computed, ref } from 'vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import { Checkbox } from '@/Components/ui/checkbox';
import BaseButton from './BaseButton.vue';
import { mdiEye, mdiEyeClosed } from '@mdi/js';

const props = defineProps({
    modelValue: [String, Number, Boolean, null],
    type: {
        type: String,
        default: 'input',
    },
    label: String,
    error: String,
    placeholder: String,
    nomLabel:{
        type:Boolean,
        default:false
    },
    required: {
        type: Boolean,
        default: false,
    },
    options: {
        type: [ Array, Object ],
        default: () => [],
    },
    description: {
        type: String,
        default: ''
    },
    h: {
        type: String,
        default: 'h-12'
    },
    maxLength: {
        type: Number,
        default: null,
    },
    valueSelect: {
        type: String,
        default: 'id',
    },
    valueOption: {
        type: String,
        default: 'name',
    },
    // --- NUEVA PROP ---
    disabled: {
        type: Boolean,
        default: false,
    },
    classInput: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

// ... el resto de tu script se queda igual ...
const computedValue = computed({
    get() {
        return props.modelValue;
    },
    set(newValue) {
        emit('update:modelValue', newValue);
    },
});

const showPassword = ref(false);

const computedInputType = computed(() => {
    if (props.type === 'password') {
        return showPassword.value ? 'text' : 'password';
    }
    return props.type === 'input' ? 'text' : props.type;
});
</script>

<template>
    <div class="space-y-2">
        <Label v-if="type !== 'switch' && type !== 'checkbox' && !nomLabel" :for="label"
            :class="[{ 'text-destructive': error }, 'text-md text-gray-700']">
            {{ label }}
            <span v-if="required" class="text-destructive"> *</span>
        </Label>

    <div v-if="['input', 'text', 'email', 'password', 'number', 'date', 'time'].includes(type)" class="relative w-full">
            <Input :id="label" :type="computedInputType" v-model="computedValue" :placeholder="placeholder"
                :class="[{ 'border-destructive': error }, h, classInput]" :maxlength="maxLength"
                :autocomplete="type === 'password' ? 'new-password' : null" :disabled="disabled" />
            <BaseButton v-if="type === 'password'" type="button" small :icon="showPassword ? mdiEyeClosed : mdiEye"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 flex h-10 mt-1 mr-1 items-center" color="whiteDark"
                :disabled="disabled" />
        </div>

        <Textarea v-if="type === 'textarea'" :id="label" v-model="computedValue" :placeholder="placeholder"
            :class="[{ 'border-destructive': error }, h, classInput]" :maxlength="maxLength" :disabled="disabled" />

        <Select v-if="type === 'select'" v-model="computedValue" :disabled="disabled">
            <SelectTrigger :id="label" :class="[{ 'border-destructive': error }, classInput]" class="h-12">
                <SelectValue :placeholder="placeholder" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem v-for="option in options" :key="option[valueSelect] ?? option"
                    :value="option[valueSelect] ?? option">
                    {{ option[valueOption] ?? option }}
                </SelectItem>
            </SelectContent>
        </Select>

        <div v-if="type === 'switch'" class="flex flex-col space-x-2 space-y-2 mt-5 items-center">
            <Label :for="label" :class="{ 'text-destructive': error }">{{ label }}</Label>
            <Switch :id="label" v-model="computedValue" :class="{ 'border border-destructive': error }"
                :disabled="disabled"
                class="data-[state=checked]:bg-medic-500 data-[state=unchecked]:bg-medic-100" /> 
        </div>

        <div v-if="type === 'checkbox'" class="flex items-start space-x-3 pt-2">
            <Checkbox :id="label" v-model:checked="computedValue" :class="{ 'border-destructive': error }"
                class="data-[state=checked]:bg-medic-500 data-[state=checked]:border-medic-500 data-[state=unchecked]:border-medic-300"
                :disabled="disabled" />
            <div class="grid gap-1.5 leading-none">
            </div>
        </div>
        <p v-if="error" class="text-sm text-destructive mt-1">
      {{ error }}
    </p>
    </div>
</template>