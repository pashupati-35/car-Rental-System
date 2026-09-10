<script setup lang="ts">
defineProps<{
  show: boolean
  url: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const copyToClipboard = () => {
  navigator.clipboard.writeText(props.url)
  alert('Password setup link copied to clipboard!')
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-2">
        <i class="ri-mail-check-line" />
      </div>

      <div class="text-center">
        <h3 class="font-bold text-base text-slate-900 dark:text-white">
          Owner Registered Successfully!
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          An invitation email has been dispatched with their onboarding link.
        </p>
      </div>

      <div
        v-if="url"
        class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2"
      >
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">One-time Password Setup URL</span>
        <div class="p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 font-mono text-[11px] break-all text-indigo-600 dark:text-indigo-400 select-all">
          {{ url }}
        </div>
        <button
          type="button"
          class="w-full py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer"
          @click="copyToClipboard"
        >
          <i class="ri-file-copy-line" />
          <span>Copy Link</span>
        </button>
      </div>

      <div class="pt-2">
        <button
          type="button"
          class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs cursor-pointer"
          @click="emit('close')"
        >
          Done
        </button>
      </div>
    </div>
  </div>
</template>
