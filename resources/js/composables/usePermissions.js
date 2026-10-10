import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
    const page = usePage()

    const permissions = computed(() => page.props.auth?.user?.permissions ?? [])
    const roles       = computed(() => page.props.auth?.user?.roles ?? [])

    const can     = (permission) => permissions.value.includes(permission)
    const hasRole = (role) => roles.value.includes(role)
    const isAdmin = () => hasRole('admin')

    // canAny: at least one of the given permissions
    const canAny = (...perms) => perms.some(p => permissions.value.includes(p))

    return { can, canAny, hasRole, isAdmin, permissions, roles }
}
