import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { EmployeeUserView } from '@/types/employee/EmployeeUser'

interface AuthPageProps {
  auth?: {
    employee?: EmployeeUserView | null
  }
}


export const employeeImageUrl = (employee?: EmployeeUserView | null): string => {
  const path = employee?.image_path || employee?.file_path
  if (!path || Array.isArray(path))
    return ''

  return path.thumb || path.original || ''
}

export const useAuthEmployee = () => {
  const page = usePage<AuthPageProps>()

  const employee = computed<EmployeeUserView | null>(() => page.props?.auth?.employee ?? null)

  return { employee }
}
