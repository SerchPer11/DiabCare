// resources/js/Composables/useNavigation.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import navigation from './navigation.js'

export function useNavigation() {
  const page = usePage()
  const user = computed(() => page.props.auth?.user?.data || page.props.auth?.user)
  const userRoles = computed(() => page.props.auth?.roles || [])
  const userPermissions = computed(() => page.props.auth?.can || [])
  
  // Helpers para verificar permisos
  const hasPermission = (permission) => {
    if (!permission) return true // Sin permiso = visible para todos
    return userPermissions.value.includes(permission)
  }
  
  const hasRole = (role) => {
    if (!role) return true
    return userRoles.value.includes(role)
  }
  
  const hasAnyRole = (roles) => {
    if (!roles || roles.length === 0) return true // Sin roles = visible para todos
    return roles.some(role => hasRole(role))
  }
  
  // Filtrar navegación según permisos del usuario
  const filteredNavigation = computed(() => {
    return navigation.map(item => {
      // Verificar roles del grupo/item
      if (!hasAnyRole(item.roles)) return null
      
      // Verificar permiso del grupo/item
      if (!hasPermission(item.permission)) return null
      
      // Si es un grupo, filtrar sus items
      if (item.type === 'group' && item.items) {
        const filteredItems = item.items.filter(subItem => {
          return hasAnyRole(subItem.roles) && hasPermission(subItem.permission)
        })
        
        // Si no quedan items después del filtro, ocultar el grupo
        if (filteredItems.length === 0) return null
        
        // Retornar grupo con items filtrados
        return { ...item, items: filteredItems }
      }
      
      return item
    }).filter(Boolean) // Eliminar items null
  })
  
  // Información del usuario
  const userName = computed(() => {
    if (!user.value) return 'Usuario'
    return `${user.value.name} ${user.value.last_name}` || 'Usuario'
  })
  
  const userInitials = computed(() => {
    if (!user.value) return 'U'
    const name = user.value.name || ''
    const words = name.trim().split(' ')
    if (words.length >= 2) {
      return `${words[0].charAt(0)}${words[1].charAt(0)}`.toUpperCase()
    }
    return name.charAt(0).toUpperCase() || 'U'
  })
  
  const userRoleLabel = computed(() => {
    const roleLabels = {
      'super_admin': 'Super Administrador',
      'admin': 'Administrador', 
      'doctor': 'Doctor',
      'patient': 'Paciente'
    }
    const currentRole = userRoles.value[0] // Primer rol como rol actual
    return roleLabels[currentRole] || 'Usuario'
  })
  
  const currentRole = computed(() => userRoles.value[0] || null)
  
  const userEmail = computed(() => {
    if (!user.value) return ''
    return user.value.email || ''
  })
  
  return {
    navigation: filteredNavigation,
    hasPermission,
    hasRole,
    hasAnyRole,
    user,
    userName,
    userInitials,
    userRoleLabel,
    currentRole,
    userEmail
  }
}