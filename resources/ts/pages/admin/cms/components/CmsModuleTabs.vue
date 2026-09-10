<script setup lang="ts">
import { ref, computed } from 'vue'
import type { CmsModuleMeta } from '../types'

const props = defineProps<{
  modules: CmsModuleMeta[]
  activeModule: string
  stats?: Record<string, number>
}>()

const emit = defineEmits<{
  (e: 'update:activeModule', value: string): void
}>()

const isDropdownOpen = ref(false)

const currentModule = computed(() => {
  return props.modules.find((m: CmsModuleMeta) => m.id === props.activeModule) || props.modules[0]
})

const selectModule = (id: string) => {
  emit('update:activeModule', id)
  isDropdownOpen.value = false
}
</script>

<template>
  <div class="space-y-3 w-full min-w-0">
    <!-- Mobile Interactive Module Picker (Visible on xs/sm screens) -->
    <div class="sm:hidden relative">
      <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
            Active Module ({{ modules.length }})
          </span>
          <span
            v-if="stats && stats[activeModule] !== undefined"
            class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
          >
            {{ stats[activeModule] }} Records
          </span>
        </div>

        <!-- Custom Dropdown Button -->
        <button
          type="button"
          class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-all cursor-pointer"
          @click="isDropdownOpen = !isDropdownOpen"
        >
          <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
              <i :class="currentModule.icon" />
            </div>
            <div class="text-left truncate">
              <span class="text-xs font-black block truncate">{{ currentModule.label }}</span>
              <span class="text-[10px] text-slate-400 block truncate">Tap to switch CMS view</span>
            </div>
          </div>
          <i
            class="ri-arrow-down-s-line text-lg text-slate-400 transition-transform duration-200 shrink-0"
            :class="isDropdownOpen ? 'rotate-180 text-indigo-600' : ''"
          />
        </button>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div
        v-if="isDropdownOpen"
        class="absolute left-0 right-0 top-full mt-2 z-50 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-2 max-h-72 overflow-y-auto space-y-1 animate-in fade-in slide-in-from-top-2 duration-150"
      >
        <button
          v-for="mod in modules"
          :key="mod.id"
          type="button"
          class="w-full flex items-center justify-between p-2.5 rounded-xl text-xs font-bold transition-colors text-left cursor-pointer"
          :class="activeModule === mod.id ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800'"
          @click="selectModule(mod.id)"
        >
          <div class="flex items-center gap-2.5 min-w-0">
            <div
              class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0"
              :class="activeModule === mod.id ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400'"
            >
              <i :class="mod.icon" />
            </div>
            <span class="truncate">{{ mod.label }}</span>
          </div>

          <span
            v-if="stats && stats[mod.id] !== undefined"
            class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold shrink-0"
            :class="activeModule === mod.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
          >
            {{ stats[mod.id] }}
          </span>
        </button>
      </div>
    </div>

    <!-- Responsive Scrollable Tabs (Visible on sm and up) -->
    <div class="hidden sm:flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin w-full max-w-full">
      <button
        v-for="mod in modules"
        :key="mod.id"
        type="button"
        :class="activeModule === mod.id ? 'bg-indigo-600 text-white font-black shadow-md shadow-indigo-600/20' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 font-semibold hover:bg-slate-50 dark:hover:bg-slate-800'"
        class="px-3.5 py-2.5 rounded-xl text-xs flex items-center gap-2 shrink-0 transition-all cursor-pointer"
        @click="emit('update:activeModule', mod.id)"
      >
        <i
          :class="mod.icon"
          class="text-sm"
        />
        <span>{{ mod.label }}</span>
        <span
          v-if="stats && stats[mod.id] !== undefined"
          :class="activeModule === mod.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
          class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold"
        >
          {{ stats[mod.id] }}
        </span>
      </button>
    </div>
  </div>
</template>
