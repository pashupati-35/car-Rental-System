<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import RichTextEditor from '@/components/RichTextEditor.vue'
import FormToggle from '@/components/FormToggle.vue'

const props = defineProps<{
  template: {
    id: number
    title: string
    identifier: string
    role: string
    subject: string
    description: string
    message_content: string
    is_active: boolean
    accepted_inputs: string
  }
}>()

const form = useForm({
  title: props.template.title,
  subject: props.template.subject,
  description: props.template.description,
  message_content: props.template.message_content,
  is_active: Boolean(props.template.is_active),
})

const submit = () => {
  if (form.description && !form.message_content) {
    form.message_content = form.description
  } else if (form.message_content && !form.description) {
    form.description = form.message_content
  }
  form.put(`/admin/email-templates/${props.template.id}`)
}

const getPlaceholder = (tag: string) => {
  return `{{$${tag.trim()}}}`
}

const insertPlaceholder = (tag: string) => {
  const ph = `{{$${tag.trim()}}}`

  form.description = (form.description || '') + ' ' + ph
}
</script>

<template>
  <AdminLayout>
    <Head :title="`Edit ${template.title} - Email Templates`" />

    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200/60">
              Role: {{ template.role }}
            </span>
          </div>
          <h3 class="font-black text-2xl text-slate-900 dark:text-white">
            {{ template.title }}
          </h3>
          <p class="text-xs text-slate-400 font-mono mt-0.5">
            Identifier: {{ template.identifier }}
          </p>
        </div>
        <Link
          href="/admin/email-templates"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5"
        >
          <i class="ri-arrow-left-line" />
          <span>Back to Templates</span>
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <form
          class="space-y-5 text-xs"
          @submit.prevent="submit"
        >
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Template Title *</label>
            <input
              v-model="form.title"
              type="text"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
          </div>

          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Subject Line *</label>
            <input
              v-model="form.subject"
              type="text"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
          </div>

          <div
            v-if="template.accepted_inputs"
            class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900"
          >
            <span class="font-bold text-indigo-900 dark:text-indigo-300 block mb-1.5">Available Dynamic Variables (Click to append):</span>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="tag in template.accepted_inputs.split(',')"
                :key="tag"
                type="button"
                class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 font-mono text-[11px] text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-50 transition-colors cursor-pointer"
                @click="insertPlaceholder(tag)"
              >
                {{ getPlaceholder(tag) }}
              </button>
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">HTML Email Template Body (Rich Text)</label>
            <RichTextEditor
              v-model="form.description"
              placeholder="Design formatted email message content..."
              min-height="260px"
            />
          </div>

          <div>
            <FormToggle
              v-model="form.is_active"
              label="Template Active Status"
              description="Enable or disable this template for automated system dispatch"
              active-text="Active & Enabled"
              inactive-text="Disabled"
            />
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="py-2.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 transition-all cursor-pointer flex items-center gap-1.5"
            >
              <i
                v-if="form.processing"
                class="ri-loader-4-line animate-spin"
              />
              <i
                v-else
                class="ri-save-line"
              />
              <span>{{ form.processing ? 'Saving...' : 'Save Template Changes' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
