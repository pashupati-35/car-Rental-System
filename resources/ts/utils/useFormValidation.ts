import { useVuelidate } from '@vuelidate/core'

export function useFormValidation<T extends Record<string, any>>(
  rules: any,
  form: Ref<T> | T,
) {
  const v$ = useVuelidate(rules, form)

  const hasError = computed(() => v$.value.$error)
  const isValid = computed(() => !v$.value.$invalid)

  const touch = () => v$.value.$touch()
  const resetValidation = () => v$.value.$reset()

  const touchField = (field: keyof T) => {
    v$.value[field as string]?.$touch()
  }

  const validationErrors = (field: keyof T) => {
    const fieldValidation = v$.value[field as string]

    return fieldValidation?.$dirty
      ? fieldValidation?.$errors.map((e: { $message: string }) => {
        return e.$message === "Value is required" ? "This field is required" : e.$message
      }) ?? []
      : []
  }

  return {
    v$,
    hasError,
    isValid,
    touch,
    resetValidation,
    touchField,
    validationErrors,
  }
}
