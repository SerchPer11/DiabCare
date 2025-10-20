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
} from '@mdi/js';

const navigation = [
    {
        type: 'single',
        title: "Dashboard",
        route: 'dashboard',
        icon: mdiViewDashboard,
    },

    // Gestión de Pacientes
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
    {
        type: 'group',
        title: "Catalogo Medico",
        icon: mdiMedicalBag ,
        permission: 'medic.view', // Si no tiene este permiso, no ve el grupo
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
        ]
    }
];

export default navigation;