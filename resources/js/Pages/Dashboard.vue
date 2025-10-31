<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    auth: Object,
    surveys: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
});

const userRole = computed(() => {
    return props.auth.roles?.[0]?.name || 'user'
});

const isDoctor = computed(() => userRole.value === 'doctor');
const isPatient = computed(() => userRole.value === 'patient');
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard - {{ userRole === 'doctor' ? 'Doctor' : userRole === 'patient' ? 'Paciente' : 'Usuario' }}
                </h2>
                <div v-if="isDoctor" class="flex gap-2">
                    <Link
                        :href="route('doctor.surveys.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                    >
                        + Nueva Encuesta
                    </Link>
                    <Link
                        :href="route('doctor.surveys.index')"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                    >
                        Ver Encuestas
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Bienvenida personalizada -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            ¡Bienvenido, {{ auth.user.name }}!
                        </h3>
                        <p class="text-gray-600">
                            <span v-if="isDoctor">
                                Desde aquí puedes gestionar tus encuestas, revisar respuestas de pacientes y crear nuevas evaluaciones.
                            </span>
                            <span v-else-if="isPatient">
                                Aquí puedes ver las encuestas disponibles, completar evaluaciones y revisar tu historial.
                            </span>
                            <span v-else>
                                Panel de control principal de DiabCare.
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Estadísticas rápidas para doctores -->
                <div v-if="isDoctor" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ stats.total || 0 }}</div>
                            <div class="text-sm text-gray-600">Total Encuestas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ stats.active || 0 }}</div>
                            <div class="text-sm text-gray-600">Activas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ stats.total_responses || 0 }}</div>
                            <div class="text-sm text-gray-600">Total Respuestas</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-center">
                            <div class="text-3xl font-bold text-amber-600">{{ Math.round(stats.average_response_rate || 0) }}%</div>
                            <div class="text-sm text-gray-600">Tasa Respuesta</div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Para Doctores -->
                    <template v-if="isDoctor">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="font-medium text-gray-900 mb-2">Gestionar Encuestas</h4>
                                <p class="text-gray-600 text-sm mb-4">Crea, edita y administra tus encuestas</p>
                                <Link
                                    :href="route('doctor.surveys.index')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Ver Encuestas
                                </Link>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="font-medium text-gray-900 mb-2">Resultados</h4>
                                <p class="text-gray-600 text-sm mb-4">Analiza las respuestas de tus pacientes</p>
                                <Link
                                    :href="route('doctor.surveys.results')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Ver Resultados
                                </Link>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="font-medium text-gray-900 mb-2">Nueva Encuesta</h4>
                                <p class="text-gray-600 text-sm mb-4">Crea una nueva encuesta para tus pacientes</p>
                                <Link
                                    :href="route('doctor.surveys.create')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Crear Encuesta
                                </Link>
                            </div>
                        </div>
                    </template>

                    <!-- Para Pacientes -->
                    <template v-else-if="isPatient">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="font-medium text-gray-900 mb-2">Encuestas Disponibles</h4>
                                <p class="text-gray-600 text-sm mb-4">Completa las evaluaciones asignadas</p>
                                <Link
                                    :href="route('patient.surveys.index')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Ver Encuestas
                                </Link>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="font-medium text-gray-900 mb-2">Mis Respuestas</h4>
                                <p class="text-gray-600 text-sm mb-4">Revisa tus respuestas anteriores</p>
                                <Link
                                    :href="route('patient.surveys.my-responses')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Ver Historial
                                </Link>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
