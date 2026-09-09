import { ref, computed, onMounted, onUnmounted, watch } from "vue"
import { required, minLength } from "@vuelidate/validators"
import { useFormValidation } from "@/utils/useFormValidation"
import { showErrorMsg, showSuccess } from "@/composable/useSnotify"
import { VerifyEmailCredentials } from "@/types/auth/UserCredential"
import EmployeeUserLoginService from "@/services/employee/EmployeeLoginService"

type UseOtpVerificationOptions = {
    identifier: string; // email/phone used for storage key uniqueness
    initialCode?: string;
    token?: string;

    otpLength?: number; // default 6
    countdownSeconds?: number; // default 60
    autoSubmit?: boolean;

    useRecaptcha?: boolean;
    recaptchaSiteKey?: string; // default import.meta.env.VITE_SITE_KEY

    storageKey?: string;
};

export const useOtpVerification = (opts: UseOtpVerificationOptions) => {
  const otpLength = opts.otpLength ?? 6
  const countdownSeconds = opts.countdownSeconds ?? 60

  const storageKey =
        opts.storageKey ?? `otp_resend_available_at:${opts.identifier}:${opts.token ?? ""}`

  const verificationCode = ref(opts.initialCode ?? "")
  const resendLoading = ref(false)
  const verifying = ref(false)

  const recaptchaSiteKey = ref(opts.recaptchaSiteKey ?? import.meta.env.VITE_SITE_KEY)
  const invisibleRecaptchaRef = ref<any>(null)

  const resendAvailableAtMs = ref<number>(0)
  const nowMs = ref<number>(Date.now())
  let tickInterval: number | null = null


  const employeeLoginService = new EmployeeUserLoginService()

  const countdown = computed(() => {
    const diff = Math.ceil((resendAvailableAtMs.value - nowMs.value) / 1000)

    return diff > 0 ? diff : 0
  })

  const rules = {
    verification_code: { required, minLength: minLength(otpLength) },
  }

  // your validator expects an object with the same key used in rules
  const formProxy = { verification_code: verificationCode }
  const { validationErrors, touch, hasError } = useFormValidation(rules, formProxy)

  const persistTimestamp = (ms: number) => {
    resendAvailableAtMs.value = ms
    localStorage.setItem(storageKey, String(ms))
  }

  const loadTimestamp = () => {
    const raw = localStorage.getItem(storageKey)
    const ms = raw ? Number(raw) : 0

    resendAvailableAtMs.value = Number.isFinite(ms) ? ms : 0
  }

  const startTicking = () => {
    if (tickInterval) window.clearInterval(tickInterval)

    nowMs.value = Date.now()
    tickInterval = window.setInterval(() => {
      nowMs.value = Date.now()

      // cleanup storage when expired
      if (countdown.value === 0 && resendAvailableAtMs.value !== 0) {
        resendAvailableAtMs.value = 0
        localStorage.removeItem(storageKey)
      }
    }, 1000)
  }

  const stopTicking = () => {
    if (tickInterval) {
      window.clearInterval(tickInterval)
      tickInterval = null
    }
  }

  const startCountdown = () => {
    persistTimestamp(Date.now() + countdownSeconds * 1000)
  }

  const clearCountdown = () => {
    resendAvailableAtMs.value = 0
    localStorage.removeItem(storageKey)
  }

  const executeVerification = async (extraPayload: VerifyEmailCredentials) => {
    verifying.value = true
    try {
      const { status, message, url } = await employeeLoginService.verifyUser(extraPayload)

      if (status == "OK") {
        showSuccess("Verification successful")
        clearCountdown()
        setTimeout(() => {
          window.location.href = url || route("employee.dashboard")
        }, 1000)
      } else {
        showErrorMsg(message || "Failed to verify email. Please try again.")
      }
    } catch (err: any) {
      const errorMessage =
      err?.response?.data?.message ||
      err?.response?.data?.error ||
      err?.message ||
      "An unexpected error occurred. Please try again."

      showErrorMsg(errorMessage)
    } finally {
      verifying.value = false
    }
  }

  const submitVerification = async () => {
    touch()

    if (hasError.value) {
      showErrorMsg(`Please enter a valid ${otpLength}-digit code`)

      return
    }

    if (opts.useRecaptcha) {
      invisibleRecaptchaRef.value?.execute?.()

      return
    }
  }

  const handleCaptchaVerification = async (recaptchaToken: string) => {
    try {
      await executeVerification({
        token: opts.token || "",
        verification_code: verificationCode.value,
        recaptcha_response: recaptchaToken,
        is_used: 0,
      })
    } catch (err: any) {
      const errorMessage =
      err?.response?.data?.message ||
      err?.response?.data?.error ||
      err?.message ||
      "Captcha verification failed."

      showErrorMsg(errorMessage)
    } finally {
      invisibleRecaptchaRef.value?.reset?.()
    }
  }

  const resendOtp = async () => {
    if (countdown.value > 0 || resendLoading.value) return

    resendLoading.value = true
    try {
      const { status, message } = await employeeLoginService.resendVerificationEmail({
        token: opts.token || "",
      })

      if (status == "OK") {
        showSuccess(message || "Verification code resent successfully!")
      } else {
        showErrorMsg(message || "Failed to resend verification code")
      }
    } catch (err: any) {
      const errorMessage =
      err?.response?.data?.message ||
      err?.response?.data?.error ||
      err?.message ||
      "Failed to resend verification code"

      showErrorMsg(errorMessage)
    } finally {
      resendLoading.value = false
    }
  }

  if (opts.autoSubmit) {
    watch(verificationCode, (val: string | any[]) => {
      if (val.length === otpLength && !verifying.value) {
        submitVerification()
      }
    })
  }

  onMounted(() => {
    loadTimestamp()
    if (resendAvailableAtMs.value === 0) startCountdown()
    startTicking()
  })

  onUnmounted(() => {
    stopTicking()
  })

  return {
    verificationCode,
    resendLoading,
    verifying,
    countdown,
    validationErrors,
    recaptchaSiteKey,
    invisibleRecaptchaRef,
    submitVerification,
    handleCaptchaVerification,
    resendOtp,
  }
}
