<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

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
  form.put(`/admin/email-templates/${props.template.id}`)
}

const getPlaceholder = (tag: string) => {
  return `{{$${tag.trim()}}}`
}
</script>

<template>
  <AppLayout>
    <Head :title="`Edit ${template.title}`" />

    <template #header>
      Edit Email Template
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            {{ template.title }}
          </h3>
          <p class="text-xs text-gray-500 font-mono mt-0.5">
            Identifier: {{ template.identifier }}
          </p>
        </div>
        <Link
          href="/admin/email-templates"
          class="px-3.5 py-2 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          &larr; Back to Templates
        </Link>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
        <form
          class="space-y-5"
          @submit.prevent="submit"
        >
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Title</label>
            <input
              v-model="form.title"
              type="text"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Subject</label>
            <input
              v-model="form.subject"
              type="text"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
          </div>

          <div
            v-if="template.accepted_inputs"
            class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 text-xs"
          >
            <span class="font-semibold text-blue-800 dark:text-blue-300">Available Placeholders:</span>
            <div class="flex flex-wrap gap-2 mt-1.5">
              <span
                v-for="tag in template.accepted_inputs.split(',')"
                :key="tag"
                class="px-2 py-0.5 rounded bg-white dark:bg-gray-800 font-mono text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800"
              >
                {{ getPlaceholder(tag) }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">HTML Content (Description)</label>
            <textarea
              v-model="form.description"
              rows="8"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 font-mono text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>

          <div class="flex items-center gap-2">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            >
            <label
              for="is_active"
              class="text-sm font-medium text-gray-700 dark:text-gray-300"
            >Enable this template</label>
          </div>

          <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all"
            >
              Save Template Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
