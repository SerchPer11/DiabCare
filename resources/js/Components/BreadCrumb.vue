<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Slash } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';

/**
 * Este componente genera un breadcrumb automático y flexible (Dashboard / Recurso / Acción)
 * basado en la ruta actual de Inertia y un título de recurso proporcionado.
 *
 * @prop {String} resourceTitle - Título legible del recurso actual (ej. "Gestión de Medicamentos").
 */
const props = defineProps({
  resourceTitle: {
    type: String,
    required: true,
  },
});

// Traducciones para las acciones más comunes
const actionTranslations = {
  'create': 'Crear',
  'edit': 'Editar',
  'show': 'Ver detalles',
};

const generatedLinks = computed(() => {
  const currentRouteName = usePage().props.ziggy?.name;
  
  // Si no hay nombre de ruta, no generamos nada.
  if (!currentRouteName) {
    return [];
  }

  const links = [{ label: 'Dashboard', href: route('dashboard') }];

  // Si ya estamos en el dashboard, no agregamos más niveles.
  if (currentRouteName === 'dashboard') {
    return links;
  }
  
  const segments = currentRouteName.split('.');
  
  // La acción es el último segmento del nombre de la ruta (e.g., 'index', 'edit').
  const action = segments[segments.length - 1];

  // El nombre base del recurso es todo menos el último segmento.
  // Ej: Para 'doctor.medications.edit', el baseName es 'doctor.medications'.
  const resourceBaseName = segments.slice(0, -1).join('.');

  // --- Lógica principal ---
  
  // Si la acción es 'index', el breadcrumb es: Dashboard / Título del Recurso
  if (action === 'index') {
    links.push({ label: props.resourceTitle, href: null }); // La página actual no es un enlace.
    return links;
  }
  
  // Si la acción es 'create', 'edit', etc., el breadcrumb es: Dashboard / Título del Recurso / Acción
  const validActions = ['create', 'edit', 'show'];
  if (validActions.includes(action)) {
    // Construimos la ruta al 'index' del recurso a partir de su nombre base.
    const indexRouteName = `${resourceBaseName}.index`;
    
    // Verificamos que la ruta de índice exista para evitar errores.
    const resourceHref = route().has(indexRouteName) ? route(indexRouteName) : null;
    
    // Agregamos el enlace al 'index'
    links.push({ label: props.resourceTitle, href: resourceHref });
    
    // Agregamos la acción actual (traducida y sin enlace)
    const actionLabel = actionTranslations[action] || action;
    links.push({ label: actionLabel, href: null });
    
    return links;
  }
  
  // Si la ruta no coincide con los patrones anteriores (ej. 'profile.settings'),
  // mostramos solo el título del recurso como último nivel.
  links.push({ label: props.resourceTitle, href: null });
  return links;
});
</script>

<template>
  <Breadcrumb>
    <BreadcrumbList>
      <template v-for="(link, index) in generatedLinks" :key="link.label">
        <BreadcrumbItem>
          <BreadcrumbLink as-child v-if="link.href">
            <Link :href="link.href">
              {{ link.label }}
            </Link>
          </BreadcrumbLink>
          <BreadcrumbPage v-else>
            {{ link.label }}
          </BreadcrumbPage>
        </BreadcrumbItem>

        <BreadcrumbSeparator v-if="index < generatedLinks.length - 1">
          <Slash />
        </BreadcrumbSeparator>
      </template>
    </BreadcrumbList>
  </Breadcrumb>
</template>