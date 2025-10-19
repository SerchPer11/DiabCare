<script setup>
import { computed } from 'vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import { Checkbox } from '@/Components/ui/checkbox';

const props = defineProps({
  modelValue: [String, Number, Boolean, null],
  type: {
    type: String,
    default: 'input',
  },
  label: String,
  error: String,
  placeholder: String,
  required: {
    type: Boolean,
    default: false,
  },
  options: {
    type: Array,
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
  // --- INICIO DE LA LÓGICA ADAPTADA ---
  valueSelect: {
    type: String,
    default: 'id',
  },
  valueOption: {
    type: String,
    default: 'name',
  },
  // --- FIN DE LA LÓGICA ADAPTADA ---
});

const emit = defineEmits(['update:modelValue']);

const computedValue = computed({
  get() {
    return props.modelValue;
  },
  set(newValue) {
    emit('update:modelValue', newValue);
  },
});
</script>

<template>
  <div class="space-y-2">
    <Label v-if="type !== 'switch' && type !== 'checkbox'" :for="label"
      :class="[{ 'text-destructive': error }, 'text-md text-gray-700']">
      {{ label }}
      <span v-if="required" class="text-destructive"> *</span>
    </Label>

    <Input v-if="['input', 'text', 'email', 'password', 'number', 'date'].includes(type)" :id="label"
      :type="type === 'input' ? 'text' : type" v-model="computedValue" :placeholder="placeholder"
      :class="[{ 'border-destructive': error }, h]" :maxlength="maxLength" />

    <Textarea v-if="type === 'textarea'" :id="label" v-model="computedValue" :placeholder="placeholder"
      :class="[{ 'border-destructive': error }, h]" :maxlength="maxLength" />

    <Select v-if="type === 'select'" v-model="computedValue">
      <SelectTrigger :id="label" :class="{ 'border-destructive': error }" class="h-12">
        <SelectValue :placeholder="placeholder" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem v-for="option in options" :key="option[valueSelect] ?? option"
          :value="option[valueSelect] ?? option">
          {{ option[valueOption] ?? option }}
        </SelectItem>
      </SelectContent>
    </Select>
    
    <div v-if="type === 'switch'" class="flex items-center space-x-2 pt-2">
      <Switch :id="label" v-model:checked="computedValue" :class="{ 'border border-destructive': error }" />
      <Label :for="label" :class="{ 'text-destructive': error }">{{ label }}</Label>
    </div>

    <div v-if="type === 'checkbox'" class="flex items-start space-x-3 pt-2">
      <Checkbox :id="label" v-model:checked="computedValue" :class="{ 'border-destructive': error }" />
      <div class="grid gap-1.5 leading-none">
        <Label :for="label" :class="{ 'text-destructive': error }">
          {{ label }}
          <span v-if="required" class="text-destructive"> *</span>
        </Label>
        <p v-if="description" class="text-sm text-muted-foreground">
          {{ description }}
        </p>
      </div>
    </div>

    <div v-if="maxLength && type === 'textarea'" class="text-right text-sm text-muted-foreground">
      <span>{{ modelValue?.length || 0 }}</span> / <span>{{ maxLength }}</span>
    </div>

    <p v-if="error" class="text-sm font-medium text-destructive">
      {{ error }}
    </p>
  </div>
</template>