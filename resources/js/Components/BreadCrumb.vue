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
 * Este componente genera un breadcrumb automático de 3 niveles (Dashboard / Recurso / Acción)
 * basado en la ruta actual de Inertia y un título de recurso proporcionado.
 *
 * @prop {String} resourceTitle - Título legible del recurso actual (ej. "Gestión de Módulos").
 */

const props = defineProps({
  resourceTitle: {
    type: String,
    required: true,
  },
});

const actionTranslations = {
  'create': 'Crear',
  'edit': 'Editar',
};

const generatedLinks = computed(() => {
  const currentRouteName = usePage().props.ziggy.name;
  const segments = currentRouteName.split('.');

  const links = [{ label: 'Dashboard', href: route('dashboard') }];

  if (currentRouteName === 'dashboard') {
    return links;
  }
  
  const resource = segments[0];
  const action = segments.length > 1 ? segments[1] : 'index';

  const resourceLabel = props.resourceTitle;

  if (action === 'index') {
    links.push({ label: resourceLabel, href: null });
    return links;
  }

  const validActions = ['create', 'edit'];
  if (validActions.includes(action)) {
    const resourceHref = route().has(`${resource}.index`) ? route(`${resource}.index`) : null;
    links.push({ label: resourceLabel, href: resourceHref });

    const actionLabel = actionTranslations[action] || action;
    links.push({ label: actionLabel, href: null });
    return links;
  }

  links.push({ label: resourceLabel, href: null });
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