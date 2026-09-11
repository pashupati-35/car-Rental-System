<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue?: string | null
    placeholder?: string
    minHeight?: string
    disabled?: boolean
    label?: string
  }>(),
  {
    modelValue: '',
    placeholder: 'Write content here...',
    minHeight: '180px',
    disabled: false,
    label: '',
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const editableRef = ref<HTMLDivElement | null>(null)
const isSourceMode = ref(false)
const sourceContent = ref(props.modelValue || '')
const isFocused = ref(false)

const activeFormats = ref({
  bold: false,
  italic: false,
  underline: false,
  strikeThrough: false,
  insertOrderedList: false,
  insertUnorderedList: false,
})

const updateActiveFormats = () => {
  if (typeof document === 'undefined') return
  try {
    activeFormats.value = {
      bold: document.queryCommandState('bold'),
      italic: document.queryCommandState('italic'),
      underline: document.queryCommandState('underline'),
      strikeThrough: document.queryCommandState('strikeThrough'),
      insertOrderedList: document.queryCommandState('insertOrderedList'),
      insertUnorderedList: document.queryCommandState('insertUnorderedList'),
    }
  } catch {
    // Ignore if selection is outside
  }
}

const syncToModel = () => {
  if (isSourceMode.value) {
    emit('update:modelValue', sourceContent.value)
  } else if (editableRef.value) {
    const html = editableRef.value.innerHTML

    // Check if effectively empty
    if (html === '<br>' || html === '<p><br></p>' || html.trim() === '') {
      emit('update:modelValue', '')
    } else {
      emit('update:modelValue', html)
    }
  }
  updateActiveFormats()
}

const onContentInput = () => {
  syncToModel()
}

const format = (command: string, value: string | null = null) => {
  if (props.disabled || isSourceMode.value) return
  if (!editableRef.value) return

  editableRef.value.focus()
  document.execCommand(command, false, value ?? undefined)
  syncToModel()
}

const insertLink = () => {
  if (props.disabled || isSourceMode.value) return
  const url = prompt('Enter link URL (e.g. https://example.com):', 'https://')
  if (url && url.trim() && url !== 'https://') {
    format('createLink', url.trim())
  }
}

const removeLink = () => {
  if (props.disabled || isSourceMode.value) return
  format('unlink')
}

const setHeading = (level: string) => {
  if (props.disabled || isSourceMode.value) return
  if (level === 'p') {
    format('formatBlock', '<p>')
  } else {
    format('formatBlock', `<${level}>`)
  }
}

const toggleSourceMode = async () => {
  if (isSourceMode.value) {
    // Switching from Source to WYSIWYG
    isSourceMode.value = false
    await nextTick()
    if (editableRef.value) {
      editableRef.value.innerHTML = sourceContent.value || ''
    }
    syncToModel()
  } else {
    // Switching from WYSIWYG to Source
    sourceContent.value = editableRef.value?.innerHTML || props.modelValue || ''
    isSourceMode.value = true
    syncToModel()
  }
}

const onSourceInput = () => {
  emit('update:modelValue', sourceContent.value)
}

watch(
  () => props.modelValue,
  (newVal?: string | null) => {
    const val = newVal || ''
    if (isSourceMode.value) {
      if (sourceContent.value !== val) {
        sourceContent.value = val
      }
    } else if (editableRef.value) {
      if (editableRef.value.innerHTML !== val) {
        editableRef.value.innerHTML = val
      }
    }
  },
  { immediate: true },
)

onMounted(() => {
  if (editableRef.value) {
    editableRef.value.innerHTML = props.modelValue || ''
  }
})
</script>

<template>
  <div class="ck-editor-custom-wrapper">
    <label
      v-if="label"
      class="block font-bold mb-1.5 text-xs text-slate-700 dark:text-slate-300"
    >
      {{ label }}
    </label>

    <div
      class="ck-editor-box border rounded-xl overflow-hidden transition-all duration-200"
      :class="[
        isFocused ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700',
        disabled ? 'opacity-60 bg-slate-100 dark:bg-slate-800 pointer-events-none' : 'bg-white dark:bg-slate-900',
      ]"
    >
      <!-- Clean WYSIWYG Toolbar (matching Image 4) -->
      <div class="ck-toolbar-bar flex flex-wrap items-center gap-1 px-3 py-2 border-b border-slate-200 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-xs select-none">
        <!-- Bold -->
        <button
          type="button"
          :class="activeFormats.bold ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-black' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700'"
          class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-sm cursor-pointer transition-colors"
          title="Bold (Ctrl+B)"
          @mousedown.prevent="format('bold')"
        >
          <span class="font-extrabold text-xs">B</span>
        </button>

        <!-- Italic -->
        <button
          type="button"
          :class="activeFormats.italic ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-black' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700'"
          class="w-7 h-7 rounded-lg flex items-center justify-center italic text-sm cursor-pointer transition-colors"
          title="Italic (Ctrl+I)"
          @mousedown.prevent="format('italic')"
        >
          <span class="italic font-serif text-xs">I</span>
        </button>

        <!-- Underline -->
        <button
          type="button"
          :class="activeFormats.underline ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700'"
          class="w-7 h-7 rounded-lg flex items-center justify-center underline text-sm cursor-pointer transition-colors"
          title="Underline (Ctrl+U)"
          @mousedown.prevent="format('underline')"
        >
          <span class="underline text-xs">U</span>
        </button>

        <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-0.5" />

        <!-- Numbered List -->
        <button
          type="button"
          :class="activeFormats.insertOrderedList ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700'"
          class="w-7 h-7 rounded-lg flex items-center justify-center text-sm cursor-pointer transition-colors"
          title="Numbered List"
          @mousedown.prevent="format('insertOrderedList')"
        >
          <i class="ri-list-ordered text-sm" />
        </button>

        <!-- Bulleted List -->
        <button
          type="button"
          :class="activeFormats.insertUnorderedList ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700'"
          class="w-7 h-7 rounded-lg flex items-center justify-center text-sm cursor-pointer transition-colors"
          title="Bulleted List"
          @mousedown.prevent="format('insertUnorderedList')"
        >
          <i class="ri-list-unordered text-sm" />
        </button>

        <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-0.5" />

        <!-- Link -->
        <button
          type="button"
          class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-200/70 dark:hover:bg-slate-700 text-sm cursor-pointer transition-colors"
          title="Insert Link"
          @mousedown.prevent="insertLink"
        >
          <i class="ri-link text-sm" />
        </button>

        <!-- Unlink -->
        <button
          type="button"
          class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-200/70 dark:hover:bg-slate-700 text-sm cursor-pointer transition-colors"
          title="Remove Link"
          @mousedown.prevent="removeLink"
        >
          <i class="ri-link-unlink-m text-sm" />
        </button>

        <!-- Blockquote -->
        <button
          type="button"
          class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-200/70 dark:hover:bg-slate-700 text-sm cursor-pointer transition-colors"
          title="Blockquote"
          @mousedown.prevent="format('formatBlock', '<blockquote>')"
        >
          <i class="ri-double-quotes-l text-sm" />
        </button>

        <!-- Heading Selector -->
        <select
          class="h-7 px-2 text-[11px] rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none cursor-pointer"
          @change="(e) => setHeading((e.target as HTMLSelectElement).value)"
        >
          <option value="p">
            Paragraph
          </option>
          <option value="h2">
            Heading 2
          </option>
          <option value="h3">
            Heading 3
          </option>
          <option value="h4">
            Heading 4
          </option>
        </select>

        <!-- Clear Format -->
        <button
          type="button"
          class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-slate-200/70 dark:hover:bg-slate-700 text-sm cursor-pointer transition-colors"
          title="Clear Formatting"
          @mousedown.prevent="format('removeFormat')"
        >
          <i class="ri-format-clear text-sm" />
        </button>

        <span class="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-0.5" />

        <!-- Source Mode Button (matches Image 4) -->
        <button
          type="button"
          :class="isSourceMode ? 'bg-indigo-600 text-white shadow-xs' : 'hover:bg-slate-200/70 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'"
          class="px-2.5 h-7 rounded-lg flex items-center gap-1 text-[11px] font-bold cursor-pointer transition-all ms-auto"
          title="Toggle HTML Source Code View"
          @click="toggleSourceMode"
        >
          <i class="ri-code-s-slash-line text-xs" />
          <span>Source</span>
        </button>
      </div>

      <!-- Editor Editable Area (WYSIWYG Mode) -->
      <div
        v-show="!isSourceMode"
        ref="editableRef"
        contenteditable="true"
        class="ck-editable-area p-3.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none overflow-y-auto leading-relaxed"
        :style="{ minHeight: minHeight, maxHeight: '420px' }"
        :data-placeholder="placeholder"
        @input="onContentInput"
        @focus="isFocused = true; updateActiveFormats()"
        @blur="isFocused = false; syncToModel()"
        @keyup="updateActiveFormats"
        @mouseup="updateActiveFormats"
      />

      <!-- Editor Source Code Area (HTML Source Mode) -->
      <textarea
        v-if="isSourceMode"
        v-model="sourceContent"
        class="w-full p-3.5 text-xs font-mono bg-slate-900 text-emerald-400 border-none focus:outline-none resize-y leading-relaxed"
        :style="{ minHeight: minHeight, maxHeight: '420px' }"
        placeholder="Edit raw HTML code..."
        @input="onSourceInput"
        @focus="isFocused = true"
        @blur="isFocused = false"
      />

      <!-- Bottom Bar with Resize Handle -->
      <div class="px-3 py-1 bg-slate-50/50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-[10px] text-slate-400 select-none">
        <span class="flex items-center gap-1 font-mono">
          <i class="ri-edit-line text-[11px]" />
          {{ isSourceMode ? 'HTML Source Mode' : 'WYSIWYG Rich Text' }}
        </span>
        <div class="flex items-center gap-2">
          <span>Rich Text Editor</span>
          <i class="ri-draggable text-xs text-slate-300 dark:text-slate-600" />
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.ck-editable-area {
  word-break: break-word;
}

.ck-editable-area[contenteditable="true"]:empty:before {
  content: attr(data-placeholder);
  color: #94a3b8;
  pointer-events: none;
  display: block;
}

.dark .ck-editable-area[contenteditable="true"]:empty:before {
  color: #64748b;
}

.ck-editable-area p {
  margin-bottom: 0.5rem;
}

.ck-editable-area p:last-child {
  margin-bottom: 0;
}

.ck-editable-area h2 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-top: 0.75rem;
  margin-bottom: 0.5rem;
  color: inherit;
}

.ck-editable-area h3 {
  font-size: 1rem;
  font-weight: 700;
  margin-top: 0.5rem;
  margin-bottom: 0.25rem;
  color: inherit;
}

.ck-editable-area h4 {
  font-size: 0.875rem;
  font-weight: 600;
  margin-top: 0.5rem;
  margin-bottom: 0.25rem;
  color: inherit;
}

.ck-editable-area ul {
  list-style-type: disc;
  padding-left: 1.25rem;
  margin-bottom: 0.5rem;
}

.ck-editable-area ol {
  list-style-type: decimal;
  padding-left: 1.25rem;
  margin-bottom: 0.5rem;
}

.ck-editable-area li {
  margin-bottom: 0.25rem;
}

.ck-editable-area blockquote {
  border-left: 3px solid #6366f1;
  padding-left: 0.75rem;
  margin: 0.5rem 0;
  font-style: italic;
  color: #64748b;
}

.dark .ck-editable-area blockquote {
  color: #94a3b8;
  border-left-color: #818cf8;
}

.ck-editable-area a {
  color: #4f46e5;
  text-decoration: underline;
  cursor: pointer;
}

.dark .ck-editable-area a {
  color: #818cf8;
}
</style>
