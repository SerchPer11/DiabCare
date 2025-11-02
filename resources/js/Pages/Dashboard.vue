<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminDashboard from './Dashboard/Components/AdminDashboard.vue';
import DoctorDashboard from './Dashboard/Components/DoctorDashboard.vue';
import PatientDashboard from './Dashboard/Components/PatientDashboard.vue';

const page = usePage();

const props = defineProps({
    dashboardData: {
        type: Object,
        default: () => ({})
    },
    userRole: {
        type: String,
        default: 'user'
    }
});

// Acceder a datos de autenticación global
const user = computed(() => page.props.auth?.user?.data || page.props.auth?.user);
const userRoles = computed(() => page.props.auth?.roles || []);
const currentRole = computed(() => userRoles.value[0] || props.userRole || 'user');

const isAdmin = computed(() => currentRole.value === 'admin');
const isDoctor = computed(() => currentRole.value === 'doctor');
const isPatient = computed(() => currentRole.value === 'patient');

const dashboardTitle = computed(() => {
    const roleMap = {
        admin: 'Administrador',
        doctor: 'Doctor',
        patient: 'Paciente'
    };
    return `Dashboard - ${roleMap[currentRole.value] || 'Usuario'}`;
});

const userName = computed(() => {
    if (!user.value) return 'Usuario';
    return user.value.name || 'Usuario';
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ dashboardTitle }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Bienvenida personalizada -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            ¡Bienvenido, {{ userName }}!
                        </h3>
                        <p class="text-gray-600">
                            <span v-if="isAdmin">
                                Panel de control administrativo del sistema DiabCare.
                            </span>
                            <span v-else-if="isDoctor">
                                Gestiona tus pacientes, citas y planes médicos desde aquí.
                            </span>
                            <span v-else-if="isPatient">
                                Controla tu progreso, citas y planes de salud.
                            </span>
                            <span v-else>
                                Panel de control principal de DiabCare.
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Dashboard específico por rol -->
                <AdminDashboard 
                    v-if="isAdmin && dashboardData" 
                    :dashboard-data="dashboardData" 
                />
                
                <DoctorDashboard 
                    v-else-if="isDoctor && dashboardData" 
                    :dashboard-data="dashboardData" 
                />
                
                <PatientDashboard 
                    v-else-if="isPatient && dashboardData" 
                    :dashboard-data="dashboardData" 
                />

                <!-- Fallback si no hay datos -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Dashboard en construcción
                        </h3>
                        <p class="text-gray-600">
                            Los datos del dashboard se están cargando...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
