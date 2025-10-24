// resources/js/Composables/navigation.js
import {
    mdiViewDashboard,
    mdiAccountGroup,
    mdiCardAccountDetails,
    mdiSecurity,
    mdiViewModule,
    mdiKey,
    mdiMedicalBag,
    mdiMedication,
    mdiGymnastics,
    mdiClipboardTextClock
} from '@mdi/js';

const navigation = [
    {
        type: 'single',
        title: "Dashboard",
        route: 'dashboard',
        icon: mdiViewDashboard,
    },

    // Seguridad
    {
        type: 'group',
        title: "Seguridad",
        icon: mdiSecurity,
        permission: 'security.view', // Si no tiene este permiso, no ve el grupo
        roles: ['admin'], // Solo estos roles pueden ver pacientes
        items: [
            {
                title: "Modulos",
                route: 'modules.index',
                icon: mdiViewModule,
                permission: 'modules.index',
                roles: ['admin']
            },
            {
                title: "Permisos",
                route: 'permissions.index',
                icon: mdiKey,
                permission: 'permissions.index',
                roles: ['admin']
            },
            {
                title: "Roles",
                route: 'roles.index',
                icon: mdiCardAccountDetails,
                permission: 'roles.index',
                roles: ['admin']
            },
        ]

    },
    {
        type: 'single',
        title: "Usuarios",
        route: 'users.index',
        icon: mdiAccountGroup,
        permission: 'users.index',
        roles: ['admin']
    },
    // Doctor
    {
        type: 'group',
        title: "Catalogo Medico",
        icon: mdiMedicalBag ,
        permission: 'medic.view',
        items: [
            {
                title: "Ejercicios",
                route: 'doctor.catalogs.exercises.index',
                icon: mdiGymnastics,
                permission: 'doctor.catalogs.exercises.index',
            },
            {
                title: "Medicamentos",
                route: 'doctor.catalogs.medications.index',
                icon: mdiMedication,
                permission: 'doctor.catalogs.medications.index',
            },
        ], 
        /*type: 'single',
        title: "Citas Médicas",
        route: 'doctor.appointments.index',
        icon: mdiClipboardTextClock,
        permission: 'doctor.appointments.index',
        roles: ['doctor']*/
    },

    // Paciente
    {
        type: 'single',
        title: "Historial Médico",
        route: 'patient.medical-history.index',
        icon: mdiClipboardTextClock,
        roles: ['patient']
    },
];

export default navigation;