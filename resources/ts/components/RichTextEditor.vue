<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue?: string | null
    placeholder?: string
    minHeight?: string
    disabled?: boolean
  }>(),
  {
    modelValue: '',
    placeholder: 'Write content here...',
    minHeight: '160px',
    disabled: false,
  }
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const editorRef = ref<HTMLElement | null>(null)
let editorInstance: any = null
const isReady = ref(false)

onMounted(async () => {
  if (!editorRef.value) return

  try {
    // Import CKEditor Classic Build dynamically on client
    // @ts-ignore
    const ClassicEditorModule = await import('@ckeditor/ckeditor5-build-classic')
    const ClassicEditor = ClassicEditorModule.default || ClassicEditorModule

    editorInstance = await ClassicEditor.create(editorRef.value, {
      placeholder: props.placeholder,
      toolbar: [
        'heading',
        '|',
        'bold',
        'italic',
        'underline',
        'link',
        '|',
        'bulletedList',
        'numberedList',
        'blockQuote',
        '|',
        'undo',
        'redo',
      ],
    })

    if (props.modelValue) {
      editorInstance.setData(props.modelValue)
    }

    if (props.disabled) {
      editorInstance.enableReadOnlyMode('cms-readonly')
    }

    editorInstance.model.document.on('change:data', () => {
      const data = editorInstance.getData()
      if (data !== (props.modelValue || '')) {
        emit('update:modelValue', data)
      }
    })

    isReady.value = true
  } catch (err) {
    console.error('Failed to initialize CKEditor:', err)
  }
})

watch(
  () => props.modelValue,
  (newVal) => {
    if (editorInstance && isReady.value) {
      const currentData = editorInstance.getData()
      if (newVal !== currentData) {
        editorInstance.setData(newVal || '')
      }
    }
  }
)

watch(
  () => props.disabled,
  (newVal) => {
    if (editorInstance && isReady.value) {
      if (newVal) {
        editorInstance.enableReadOnlyMode('cms-readonly')
      } else {
        editorInstance.disableReadOnlyMode('cms-readonly')
      }
    }
  }
)

onBeforeUnmount(() => {
  if (editorInstance) {
    editorInstance.destroy().catch((err: any) => console.error(err))
    editorInstance = null
  }
})
</script>

<template>
  <div class="rich-text-editor-wrapper" :style="{ '--ck-min-height': minHeight }">
    <div ref="editorRef" class="ckeditor-container"></div>
  </div>
</template>

<style>
.rich-text-editor-wrapper {
  border-radius: 0.75rem;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.dark .rich-text-editor-wrapper {
  border-color: #334155;
}

/* CKEditor Custom Styling */
.rich-text-editor-wrapper .ck-editor__editable_inline {
  min-height: var(--ck-min-height, 160px);
  padding: 0.875rem 1rem !important;
  font-size: 0.8125rem;
  line-height: 1.5;
  color: #1e293b;
  background-color: #ffffff;
}

.dark .rich-text-editor-wrapper .ck-editor__editable_inline {
  color: #f1f5f9;
  background-color: #0f172a;
}

.rich-text-editor-wrapper .ck.ck-toolbar {
  background: #f8fafc;
  border: none;
  border-bottom: 1px solid #e2e8f0;
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.dark .rich-text-editor-wrapper .ck.ck-toolbar {
  background: #1e293b;
  border-bottom-color: #334155;
}

.rich-text-editor-wrapper .ck.ck-editor__main > .ck-editor__editable {
  border: none;
  border-bottom-left-radius: 0.75rem;
  border-bottom-right-radius: 0.75rem;
}

.rich-text-editor-wrapper .ck.ck-editor__main > .ck-editor__editable:focus {
  border: none;
  outline: none;
  box-shadow: inset 0 0 0 2px rgba(99, 102, 241, 0.4);
}

.rich-text-editor-wrapper .ck-rounded-corners .ck.ck-editor__top .ck-sticky-panel .ck-toolbar,
.rich-text-editor-wrapper .ck.ck-toolbar {
  border-radius: 0.75rem 0.75rem 0 0;
}

.dark .rich-text-editor-wrapper .ck.ck-button {
  color: #cbd5e1;
}

.dark .rich-text-editor-wrapper .ck.ck-button:hover:not(.ck-disabled) {
  background: #334155;
}

.dark .rich-text-editor-wrapper .ck.ck-button.ck-on {
  background: #4f46e5;
  color: #ffffff;
}

.dark .rich-text-editor-wrapper .ck.ck-dropdown__panel {
  background: #1e293b;
  border-color: #334155;
}

.dark .rich-text-editor-wrapper .ck.ck-list__item .ck-button:hover:not(.ck-disabled) {
  background: #334155;
}
</style>
