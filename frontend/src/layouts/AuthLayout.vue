<template>
  <div class="min-h-screen">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
      <div class="absolute -top-36 right-[-10rem] h-72 w-72 rounded-full bg-violet-500/20 blur-3xl"></div>
      <div class="absolute -bottom-40 left-[-10rem] h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
    </div>

    <header class="mx-auto flex max-w-6xl items-center justify-between px-4 py-6">
      <RouterLink to="/" class="group flex items-center gap-2">
        <span
          class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-600 to-blue-600 shadow-soft"
        >
          <SendIcon class="h-5 w-5 text-white" />
        </span>
        <span class="text-lg font-extrabold tracking-tight">
          <span class="bg-gradient-to-r from-violet-500 to-blue-600 bg-clip-text text-transparent">Drop</span>
          <span class="text-slate-900 dark:text-slate-100">N</span>
          <span class="bg-gradient-to-r from-blue-600 to-violet-500 bg-clip-text text-transparent">Share</span>
        </span>
      </RouterLink>

      <button
        type="button"
        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-900"
        @click="toggleTheme"
        aria-label="Toggle theme"
      >
        <SunIcon v-if="isDark" class="h-5 w-5 text-amber-400" />
        <MoonIcon v-else class="h-5 w-5 text-blue-600" />
      </button>
    </header>

    <main class="mx-auto grid max-w-6xl place-items-center px-4 pb-16">
      <div class="w-full max-w-md">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { useTheme } from '../composables/useTheme'
import { MoonIcon, SendIcon, SunIcon } from 'lucide-vue-next'

const { isDark, toggleTheme } = useTheme()
</script>

