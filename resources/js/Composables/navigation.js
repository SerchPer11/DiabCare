// resources/js/navigation.js
import { mdiViewDashboard, mdiPill, mdiCog, mdiShieldAccount } from '@mdi/js';

const navigation = [
  // --- Este es un link individual, sin grupo ---
  {
    isGroup: false, // Propiedad para identificarlo
    title: "Dashboard",
    url: () => route('dashboard'),
    icon: mdiViewDashboard,
  },
  
  // --- Este es un grupo colapsable ---
  {
    isGroup: true, // Propiedad para identificarlo
    groupName: "Aplicación",
    links: [
      {
        title: "Tratamientos",
        url: () => "#",
        icon: mdiPill,
        /*meta: {
          permissions: ['ver tratamientos', 'editar tratamientos']
        }*/
      },
    ]
  },

  // --- Otro grupo colapsable ---
  {
    isGroup: true,
    groupName: "Ajustes",
    links: [
      {
        title: "Administración",
        url: () => "#",
        icon: mdiShieldAccount,
        /*meta: {
          permissions: ['administrar usuarios']
        }*/
      },
      {
        title: "Configuración",
        url: () => "#",
        icon: mdiCog,
      },
    ]
  },
];

export default navigation;