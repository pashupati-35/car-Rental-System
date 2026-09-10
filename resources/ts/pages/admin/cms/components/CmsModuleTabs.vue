<script setup lang="ts">
import type { CmsModuleMeta } from '../types'

const props = defineProps<{
  modules: CmsModuleMeta[]
  activeModule: string
  stats?: Record<string, number>
}>()

const emit = defineEmits<{
  (e: 'update:activeModule', value: string): void
}>()
</script>

<template>
  <div class="space-y-3">
    <!-- Mobile Module Selector (Visible on xs/sm screens) -->
    <div class="sm:hidden bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
        Select CMS Module ({{ modules.length }})
      </label>
      <div class="relative">
        <select
          :value="activeModule"
          class="w-full py-2.5 pl-3 pr-8 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-white appearance-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
          @change="emit('update:activeModule', ($event.target as HTMLSelectElement).value)"
        >
          <option v-for="mod in modules" :key="mod.id" :value="mod.id">
            {{ mod.label }} {{ stats && stats[mod.id] !== undefined ? `(${stats[mod.id]})` : '' }}
          </option>
        </select>
        <i class="ri-arrow-down-s-line absolute right-3 top-3 text-slate-400 pointer-events-none" />
      </div>
    </div>

    <!-- Module Scrollable Tabs (Visible on sm and up) -->
    <div class="hidden sm:flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
      <button
        v-for="mod in modules"
        :key="mod.id"
        type="button"
        :class="activeModule === mod.id ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 font-semibold hover:bg-slate-50 dark:hover:bg-slate-800'"
        class="px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 shrink-0 transition-all cursor-pointer"
        @click="emit('update:activeModule', mod.id)"
      >
        <i :class="mod.icon" />
        <span>{{ mod.label }}</span>
        <span
          v-if="stats && stats[mod.id] !== undefined"
          :class="activeModule === mod.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
          class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
        >
          {{ stats[mod.id] }}
        </span>
      </button>
    </div>
  </div>
</template>
