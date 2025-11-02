<template>
    <CardBox class="mt-2" isForm>
        <!-- Título del Plan -->
        <BaseFormField 
            type="input" 
            label="Título del Plan" 
            v-model="form.title"
            :error="form.errors.title" 
            placeholder="Ej: Plan de alimentación para control de glucosa"
            required
            :maxLength="255"
        />

        <!-- Primera fila: Paciente y Tipo de Plan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <BaseFormField 
                type="select" 
                label="Paciente" 
                v-model="form.patient_id"
                :options="patientOptions"
                :error="form.errors.patient_id" 
                placeholder="Seleccionar paciente..."
                required
                valueSelect="value"
                valueOption="label"
            />

            <BaseFormField 
                type="select" 
                label="Tipo de Plan" 
                v-model="form.plan_type_id"
                :options="planTypeOptions"
                :error="form.errors.plan_type_id" 
                placeholder="Seleccionar tipo..."
                required
                valueSelect="value"
                valueOption="label"
            />
        </div>

        <!-- Descripción -->
        <BaseFormField 
            type="textarea" 
            label="Descripción del Plan" 
            v-model="form.description"
            :error="form.errors.description" 
            placeholder="Describe los objetivos y características de este plan..."
            :maxLength="1000"
            h="h-32"
        />

        <!-- Segunda fila: Fechas y Estado -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <BaseFormField 
                type="date" 
                label="Fecha de Inicio" 
                v-model="form.start_date"
                :error="form.errors.start_date" 
                required
            />

            <BaseFormField 
                type="date" 
                label="Fecha de Fin" 
                v-model="form.end_date"
                :error="form.errors.end_date" 
                required
            />

            <BaseFormField 
                type="select" 
                label="Estado" 
                v-model="form.status"
                :options="statusOptions"
                :error="form.errors.status" 
                required
                valueSelect="value"
                valueOption="label"
            />
        </div>

        <!-- Sección de Elementos del Plan -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">Elementos del Plan</h4>
                <BaseButton
                    type="button"
                    color="info"
                    :icon="mdiPlus"
                    label="Agregar Elemento"
                    @click="addElement"
                />
            </div>

            <div v-if="form.elements.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="mt-2">No hay elementos en el plan</p>
                    <p class="text-sm">Agrega alimentos o ejercicios para completar el plan</p>
                </div>

            <div v-else class="space-y-4">
                    <CardBox
                        v-for="(element, index) in form.elements"
                        :key="index"
                        class="p-4"
                        :class="{ 'opacity-50': element._delete }"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                Elemento #{{ index + 1 }}
                                <span v-if="element.id" class="text-xs text-gray-500">(Existente)</span>
                                <span v-if="element._delete" class="text-xs text-red-500">(Marcado para eliminar)</span>
                            </h4>
                            <BaseButton
                                type="button"
                                color="danger"
                                :icon="mdiDelete"
                                small
                                :label="element.id ? 'Marcar para eliminar' : 'Eliminar'"
                                @click="removeElement(index)"
                            />
                        </div>

                        <div v-if="!element._delete" class="space-y-4">
                            <!-- Selector de Alimento/Ejercicio -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-if="isNutritionPlan">
                                    <BaseFormField 
                                        type="select"
                                        label="Alimento" 
                                        v-model="element.food_id"
                                        @update:modelValue="(value) => onFoodSelected(element, value)"
                                        :options="foodOptions"
                                        :error="form.errors[`elements.${index}.food_id`]" 
                                        placeholder="Seleccionar alimento..."
                                        required
                                        valueSelect="value"
                                        valueOption="label"
                                    />
                                    <!-- Información nutricional -->
                                    <div v-if="element.food_id && getSelectedFood(element.food_id)" 
                                         class="mt-3 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                                        <div class="font-semibold text-green-800 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                            </svg>
                                            Información Nutricional
                                        </div>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border">
                                                <div class="text-orange-600 font-bold text-lg">{{ getSelectedFood(element.food_id).calories || 0 }}</div>
                                                <div class="text-xs text-gray-600">Calorías</div>
                                            </div>
                                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border">
                                                <div class="text-red-600 font-bold text-lg">{{ getSelectedFood(element.food_id).protein || 0 }}</div>
                                                <div class="text-xs text-gray-600">Proteínas (g)</div>
                                            </div>
                                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border">
                                                <div class="text-yellow-600 font-bold text-lg">{{ getSelectedFood(element.food_id).carbohydrates || 0 }}</div>
                                                <div class="text-xs text-gray-600">Carbohidratos (g)</div>
                                            </div>
                                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border">
                                                <div class="text-purple-600 font-bold text-lg">{{ getSelectedFood(element.food_id).fats || 0 }}</div>
                                                <div class="text-xs text-gray-600">Grasas (g)</div>
                                            </div>
                                        </div>
                                        <div class="text-center p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                            <span class="text-blue-800 font-medium">Porción sugerida:</span>
                                            <span class="font-bold text-blue-900 ml-1">
                                                {{ getSelectedFood(element.food_id).portion_size || 100 }} 
                                                {{ getSelectedFood(element.food_id).unit?.name || 'g' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="isActivityPlan">
                                    <BaseFormField 
                                        type="select"
                                        label="Ejercicio" 
                                        v-model="element.exercise_id"
                                        @update:modelValue="(value) => onExerciseSelected(element, value)"
                                        :options="exerciseOptions"
                                        :error="form.errors[`elements.${index}.exercise_id`]" 
                                        placeholder="Seleccionar ejercicio..."
                                        required
                                        valueSelect="value"
                                        valueOption="label"
                                    />
                                    <!-- Información del ejercicio -->
                                    <div v-if="element.exercise_id && getSelectedExercise(element.exercise_id)" 
                                         class="mt-3 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg">
                                        <div class="font-semibold text-blue-800 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                            </svg>
                                            Detalles del Ejercicio
                                        </div>
                                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm border">
                                                <div class="text-blue-600 font-bold text-lg">{{ getSelectedExercise(element.exercise_id).duration_minutes || 0 }}</div>
                                                <div class="text-xs text-gray-600">Duración (min)</div>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-lg shadow-sm border">
                                                <div class="text-orange-600 font-bold text-lg">{{ getSelectedExercise(element.exercise_id).calories_burned || 0 }}</div>
                                                <div class="text-xs text-gray-600">Calorías quemadas</div>
                                            </div>
                                            <div v-if="getSelectedExercise(element.exercise_id).sets || getSelectedExercise(element.exercise_id).repetitions" 
                                                 class="text-center p-3 bg-white rounded-lg shadow-sm border">
                                                <div class="text-green-600 font-bold text-lg">
                                                    {{ getSelectedExercise(element.exercise_id).sets || 0 }} x {{ getSelectedExercise(element.exercise_id).repetitions || 0 }}
                                                </div>
                                                <div class="text-xs text-gray-600">Series x Reps</div>
                                            </div>
                                        </div>
                                        <div v-if="getSelectedExercise(element.exercise_id).equipment" 
                                             class="text-center p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <span class="text-yellow-800 font-medium">Equipo necesario:</span>
                                            <span class="font-bold text-yellow-900 ml-1">{{ getSelectedExercise(element.exercise_id).equipment }}</span>
                                        </div>
                                        <div v-if="getSelectedExercise(element.exercise_id).instructions" 
                                             class="mt-2 text-center p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                            <span class="text-gray-700 text-sm">{{ getSelectedExercise(element.exercise_id).instructions }}</span>
                                        </div>
                                    </div>
                                </div>

                                <BaseFormField 
                                    type="input"
                                    label="Frecuencia" 
                                    v-model="element.frequency"
                                    :error="form.errors[`elements.${index}.frequency`]" 
                                    placeholder="Ej: Diaria, 3 veces/semana"
                                    required
                                />
                            </div>

                            <!-- Cantidad y Detalles -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <BaseFormField 
                                    type="input"
                                    label="Cantidad" 
                                    v-model="element.quantity"
                                    :error="form.errors[`elements.${index}.quantity`]" 
                                    placeholder="200"
                                    required
                                />

                                <BaseFormField 
                                    type="input"
                                    label="Unidad" 
                                    v-model="element.unit"
                                    :error="form.errors[`elements.${index}.unit`]" 
                                    placeholder="gramos, minutos, repeticiones"
                                    required
                                />

                                <BaseFormField 
                                    v-if="isActivityPlan"
                                    type="select"
                                    label="Intensidad" 
                                    v-model="element.intensity"
                                    :options="intensityOptions"
                                    :error="form.errors[`elements.${index}.intensity`]"
                                    valueSelect="value"
                                    valueOption="label"
                                />

                                <BaseFormField 
                                    type="input"
                                    label="Horario" 
                                    v-model="element.time_schedule"
                                    :error="form.errors[`elements.${index}.time_schedule`]" 
                                    placeholder="07:00 AM, Desayuno"
                                    required
                                />
                            </div>
                        </div>

                        <div v-if="!element._delete" class="mt-4">
                            <BaseFormField 
                                type="textarea"
                                label="Instrucciones" 
                                v-model="element.instructions"
                                :error="form.errors[`elements.${index}.instructions`]"
                                placeholder="Instrucciones específicas para este elemento..."
                                h="h-16"
                            />
                        </div>

                        <div v-if="!element._delete" class="mt-4">
                            <BaseFormField 
                                type="textarea"
                                label="Notas Adicionales" 
                                v-model="element.notes"
                                :error="form.errors[`elements.${index}.notes`]"
                                placeholder="Notas adicionales, precauciones o comentarios..."
                                h="h-12"
                            />
                        </div>
                    </CardBox>
                </div>
            </div>
    </CardBox>
</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPlus, mdiDelete } from '@mdi/js';
import { computed, inject, onMounted } from 'vue';



const form = inject('form');
const patients = inject('patients');
const planTypes = inject('planTypes');
const foods = inject('foods');
const exercises = inject('exercises');
const addElement = inject('addElement');
const removeElement = inject('removeElement');

const isEditing = computed(() => !!form.id);

// Opciones para selects - BaseFormField espera un formato específico
const patientOptions = computed(() => [
    ...patients.map(patient => ({
        value: patient.id,
        label: `${getFullName(patient)} - ${patient.email}`
    }))
]);

const planTypeOptions = computed(() => [
    ...planTypes.map(type => ({
        value: type.id,
        label: type.name === 'alimentacion' ? 'Alimentación' : 'Actividad Física'
    }))
]);

const statusOptions = [
    { value: 'activo', label: 'Activo' },
    { value: 'finalizado', label: 'Finalizado' },
    { value: 'cancelado', label: 'Cancelado' }
];

const intensityOptions = [
    { value: 'baja', label: 'Baja' },
    { value: 'media', label: 'Media' },
    { value: 'alta', label: 'Alta' }
];

const selectedPlanType = computed(() => {
    return planTypes.find(type => type.id == form.plan_type_id);
});

// Opciones para Foods y Exercises
const foodOptions = computed(() => [
    ...foods.map(food => ({
        value: food.id,
        label: `${food.name} - ${food.calories || 0} kcal (${food.food_group?.name || 'Sin grupo'})`
    }))
]);

const exerciseOptions = computed(() => [
    ...exercises.map(exercise => ({
        value: exercise.id,
        label: `${exercise.name} - ${exercise.duration_minutes || 0} min (${exercise.exercise_type?.name || 'Sin tipo'})`
    }))
]);

const isNutritionPlan = computed(() => {
    return selectedPlanType.value?.name === 'alimentacion';
});

const isActivityPlan = computed(() => {
    return selectedPlanType.value?.name === 'actividad_fisica';
});

const getElementPlaceholder = () => {
    return isNutritionPlan.value 
        ? 'Selecciona un alimento del catálogo'
        : 'Selecciona un ejercicio del catálogo';
};

// Métodos de utilidad
const getFullName = (patient) => {
    const parts = [patient.name, patient.last_name, patient.second_last_name]
        .filter(part => part && part.trim() !== ''); // Filtrar partes vacías o null
    return parts.join(' ');
};

// Métodos para obtener información de alimentos y ejercicios seleccionados
const getSelectedFood = (foodId) => {
    return foods.find(food => food.id == foodId);
};

const getSelectedExercise = (exerciseId) => {
    return exercises.find(exercise => exercise.id == exerciseId);
};

// Auto-completado inteligente cuando se selecciona un alimento
const onFoodSelected = (element, foodId) => {
    const food = getSelectedFood(foodId);
    if (food) {
        // Auto-completar cantidad y unidad con valores por defecto
        if (!element.quantity) {
            element.quantity = food.portion_size || 100;
        }
        if (!element.unit) {
            element.unit = food.unit?.name || 'gramos';
        }
        
        // Sugerir frecuencia basada en tipo de alimento
        if (!element.frequency) {
            const foodGroup = food.food_group?.name?.toLowerCase() || '';
            if (foodGroup.includes('fruta')) {
                element.frequency = '2-3 veces al día';
            } else if (foodGroup.includes('vegetal') || foodGroup.includes('verdura')) {
                element.frequency = 'En cada comida principal';
            } else if (foodGroup.includes('proteína') || foodGroup.includes('carne')) {
                element.frequency = '1-2 veces al día';
            } else if (foodGroup.includes('cereal') || foodGroup.includes('carbohidrato')) {
                element.frequency = 'En desayuno, almuerzo y cena';
            } else if (foodGroup.includes('lácteo')) {
                element.frequency = '2-3 veces al día';
            } else {
                element.frequency = 'Según indicación nutricional';
            }
        }
        
        // Generar notas automáticas con información nutricional relevante
        if (!element.notes) {
            const notes = [];
            if (food.calories && food.calories > 300) notes.push('Alto contenido calórico');
            if (food.protein && food.protein > 15) notes.push('Rica en proteínas');
            if (food.carbohydrates && food.carbohydrates > 30) notes.push('Alta en carbohidratos');
            if (food.fats && food.fats < 3) notes.push('Baja en grasas');
            if (food.fats && food.fats > 15) notes.push('Alta en grasas');
            
            if (notes.length === 0) {
                notes.push('Alimento recomendado en la dieta');
            }
            element.notes = notes.join(', ');
        }
        
        // Sugerir horario basado en tipo de alimento
        if (!element.time_schedule) {
            const foodGroup = food.food_group?.name?.toLowerCase() || '';
            if (foodGroup.includes('fruta')) {
                element.time_schedule = 'Entre comidas (merienda)';
            } else if (foodGroup.includes('cereal') || foodGroup.includes('pan')) {
                element.time_schedule = 'Desayuno y cena';
            } else if (foodGroup.includes('proteína')) {
                element.time_schedule = 'Almuerzo y cena';
            } else {
                element.time_schedule = 'Comidas principales';
            }
        }
    }
};

// Auto-completado inteligente cuando se selecciona un ejercicio
const onExerciseSelected = (element, exerciseId) => {
    const exercise = getSelectedExercise(exerciseId);
    if (exercise) {
        // Auto-completar duración y unidad
        if (!element.quantity) {
            element.quantity = exercise.duration_minutes || 30;
        }
        if (!element.unit) {
            element.unit = exercise.sets && exercise.repetitions ? 'series' : 'minutos';
        }
        if (!element.intensity) {
            element.intensity = exercise.intensity || 'media';
        }
        
        // Sugerir frecuencia basada en tipo de ejercicio
        if (!element.frequency) {
            const exerciseType = exercise.exercise_type?.name?.toLowerCase() || '';
            if (exerciseType.includes('cardio') || exerciseType.includes('aeróbico')) {
                element.frequency = '4-5 veces por semana';
            } else if (exerciseType.includes('fuerza') || exerciseType.includes('resistencia')) {
                element.frequency = '2-3 veces por semana (días alternos)';
            } else if (exerciseType.includes('flexibilidad') || exerciseType.includes('estiramiento')) {
                element.frequency = 'Diariamente';
            } else if (exerciseType.includes('equilibrio')) {
                element.frequency = '3-4 veces por semana';
            } else {
                element.frequency = '3 veces por semana';
            }
        }
        
        // Generar notas con recomendaciones específicas
        if (!element.notes) {
            const notes = [];
            
            if (exercise.equipment) {
                notes.push(`Requiere: ${exercise.equipment}`);
            }
            
            if (exercise.calories_burned) {
                if (exercise.calories_burned > 300) {
                    notes.push('Alto gasto calórico');
                } else if (exercise.calories_burned > 150) {
                    notes.push('Gasto calórico moderado');
                } else {
                    notes.push('Gasto calórico bajo');
                }
            }
            
            if (exercise.sets && exercise.repetitions) {
                notes.push(`${exercise.sets} series de ${exercise.repetitions} repeticiones`);
            }
            
            const exerciseType = exercise.exercise_type?.name?.toLowerCase() || '';
            if (exerciseType.includes('cardio')) {
                notes.push('Mejora resistencia cardiovascular');
            } else if (exerciseType.includes('fuerza')) {
                notes.push('Fortalece músculos');
            } else if (exerciseType.includes('flexibilidad')) {
                notes.push('Mejora flexibilidad y movilidad');
            }
            
            if (notes.length === 0) {
                notes.push('Ejercicio recomendado');
            }
            element.notes = notes.join(', ');
        }
        
        // Sugerir horario basado en tipo de ejercicio
        if (!element.time_schedule) {
            const exerciseType = exercise.exercise_type?.name?.toLowerCase() || '';
            if (exerciseType.includes('cardio')) {
                element.time_schedule = 'Mañana (antes del desayuno) o tarde';
            } else if (exerciseType.includes('fuerza')) {
                element.time_schedule = 'Tarde (después del almuerzo)';
            } else if (exerciseType.includes('flexibilidad')) {
                element.time_schedule = 'Mañana al despertar o noche antes de dormir';
            } else {
                element.time_schedule = 'Según disponibilidad y preferencia';
            }
        }
    }
};



// Inicializar fechas por defecto si es creación
onMounted(() => {
    if (!isEditing.value) {
        const today = new Date().toISOString().split('T')[0];
        const nextMonth = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        
        if (!form.start_date) form.start_date = today;
        if (!form.end_date) form.end_date = nextMonth;
    }
});
</script>