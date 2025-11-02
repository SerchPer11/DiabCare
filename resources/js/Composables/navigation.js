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
    mdiClipboardTextClock,
    mdiFoodApple,
    mdiHandHeart,
    mdiClipboardPulse,
    mdiAccountInjury,
    mdiDatabase,
    mdiCalendarCheck,
    mdiFileChart,
} from '@mdi/js';

const navigation = [
    {
        type: 'single',
        title: "Dashboard",
        route: 'dashboard',
        icon: mdiViewDashboard,
        permission: 'dashboard.view',
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
    // Administración
    {
        type: 'group',
        title: "Administración",
        icon: mdiViewModule,
        permission: 'administration.view',
        roles: ['admin'],
        items: [
            {
                title: "Usuarios",
                route: 'users.index',
                icon: mdiAccountGroup,
                permission: 'users.index',
                roles: ['admin']
            },
            {
                title: "Respaldos",
                route: 'backups.index',
                icon: mdiDatabase,
                permission: 'backups.index',
                roles: ['admin']
            }
        ]
    },
    // Doctor
    {
        type: 'group',
        title: "Catalogo Medico",
        icon: mdiMedicalBag ,
        permission: 'medic.view',
        roles: ['doctor', 'admin'],
        items: [
            {
                title: "Ejercicios",
                route: 'doctor.catalogs.exercises.index',
                icon: mdiGymnastics,
                permission: 'doctor.catalogs.exercises.index',
                roles: ['doctor', 'admin']
            },
            {
                title: "Medicamentos",
                route: 'doctor.catalogs.medications.index',
                icon: mdiMedication,
                permission: 'doctor.catalogs.medications.index',
                roles: ['doctor', 'admin']
            },
            {
                title: "Alimentos",
                route: 'doctor.catalogs.foods.index',
                icon: mdiFoodApple,
                permission: 'doctor.catalogs.foods.index',
                roles: ['doctor', 'admin']
            },
        ], 
        /*type: 'single',
        title: "Citas Médicas",
        route: 'doctor.appointments.index',
        icon: mdiClipboardTextClock,
        permission: 'doctor.appointments.index',
        roles: ['doctor']*/
    },
    {
        type: 'single',
        title: "Citas Médicas",
        route: 'doctor.appointments.index',
        icon: mdiClipboardTextClock,
        permission: 'doctor.appointments.index',
        roles: ['doctor', 'admin']
    },
    {
        type: 'single',
        title: "Recomendaciones",
        route: 'doctor.recomendations.index',
        icon: mdiHandHeart,
        permission: 'doctor.recomendations.index',
        roles: ['doctor', 'admin']
    },
    {
        type: 'single',
        title: "Planes de Pacientes",
        route: 'doctor.plans.index',
        icon: mdiCalendarCheck,
        permission: 'doctor.plans.index',
        roles: ['doctor']
    },

    {
        type: 'group',
        title: "Encuestas",
        icon: mdiClipboardTextClock,
        permission: 'surveys.index',
        roles: ['doctor'],
        items: [
            {
                title: "Gestionar Encuestas",
                route: 'doctor.surveys.index',
                icon: mdiClipboardTextClock,
                permission: 'surveys.index',
                roles: ['doctor']
            },
            {
                title: "Resultados",
                route: 'doctor.surveys.results',
                icon: mdiClipboardPulse,
                permission: 'surveys.results',
                roles: ['doctor']
            },
        ]
    },

    {
        type: 'group',
        title: "Encuestas",
        icon: mdiClipboardTextClock,
        permission: 'patient.surveys.index',
        roles: ['patient'],
        items: [
            {
                title: "Encuestas Disponibles",
                route: 'patient.surveys.index',
                icon: mdiClipboardTextClock,
                permission: 'patient.surveys.index',
                roles: ['patient']
            },
            {
                title: "Mis Respuestas",
                route: 'patient.surveys.my-responses',
                icon: mdiClipboardPulse,
                permission: 'patient.surveys.submit',
                roles: ['patient']
            },
        ]
    },
    {
        type: 'single',
        title: "Reportes",
        route: 'reports.index',
        icon: mdiFileChart,
        roles: ['doctor', 'admin'],
        //permission: 'reports.index',
    },
    {
        type: 'single',
        title: "Pacientes",
        route: 'patients.index',
        icon: mdiAccountInjury,
        permission: 'patients.index',
        roles: ['doctor', 'admin']
    },
    // Paciente
    {
        type: 'single',
        title: "Historial Médico",
        route: 'patient.medical-history.index',
        icon: mdiClipboardTextClock,
        permission: 'patient.medical-history.index',
        roles: ['patient']
    },
    {
        type: 'single',
        title: "Mis Planes",
        route: 'patient.plans.index',
        icon: mdiCalendarCheck,
        permission: 'patient.plans.index',
        roles: ['patient']
    },
    {
        type: 'single',
        title: "Mediciones",
        route: 'measures.index',
        icon: mdiClipboardPulse,
        permission: 'measures.index',
        roles: ['patient', 'doctor', 'admin']
    },
];

export default navigation;