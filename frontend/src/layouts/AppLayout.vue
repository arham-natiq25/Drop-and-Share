<template>
  <div class="min-h-screen">
    <header
      class="sticky top-0 z-50 border-b border-slate-200/60 bg-white/75 backdrop-blur dark:border-slate-800/60 dark:bg-slate-950/55"
    >
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
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

        <nav class="hidden items-center gap-1 md:flex">
          <RouterLink
            to="/"
            class="rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900"
          >
            Home
          </RouterLink>
          <RouterLink
            to="/upload"
            class="rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900"
          >
            Upload
          </RouterLink>
        </nav>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-900"
            @click="toggleTheme"
            aria-label="Toggle theme"
          >
            <SunIcon v-if="isDark" class="h-5 w-5 text-amber-400" />
            <MoonIcon v-else class="h-5 w-5 text-blue-600" />
          </button>

          <template v-if="!isAuthed">
            <RouterLink
              to="/login"
              class="hidden rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900 sm:inline-flex"
            >
              Login
            </RouterLink>
            <RouterLink
              to="/signup"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700"
            >
              Sign up
              <ArrowRightIcon class="h-4 w-4" />
            </RouterLink>
          </template>

          <template v-else>
            <div class="hidden items-center gap-2 sm:flex">
              <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                {{ user?.name || 'Account' }}
              </span>
              <button
                type="button"
                class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-900"
                @click="logout"
              >
                Logout
              </button>
            </div>
          </template>
        </div>
      </div>
    </header>

    <main class="relative">
      <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-36 right-[-10rem] h-72 w-72 rounded-full bg-violet-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 left-[-10rem] h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
      </div>

      <div class="mx-auto max-w-6xl px-4 py-10">
        <slot />
      </div>
    </main>

    <footer class="border-t border-slate-200/60 py-8 dark:border-slate-800/60">
      <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 text-sm text-slate-600 dark:text-slate-400 md:flex-row">
        <div>© {{ new Date().getFullYear() }} DropNShare.</div>
        <div class="flex items-center gap-3">
          <a
            class="inline-flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100"
            href="https://arhamnatiq.com/"
            target="_blank"
            rel="noreferrer"
          >
            <BriefcaseIcon class="h-4 w-4" /> Portfolio
          </a>
          <a
            class="inline-flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100"
            href="mailto:arhamnatiq25@gmail.com"
            target="_blank"
            rel="noreferrer"
          >
            <MailIcon class="h-4 w-4" /> Email
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { useTheme } from '../composables/useTheme'
import { useAuth } from '../composables/useAuth'
import { http } from '../lib/http'
import { alerts } from '../lib/alerts'
import {
  ArrowRightIcon,
  BriefcaseIcon,
  MailIcon,
  MoonIcon,
  SendIcon,
  SunIcon,
} from 'lucide-vue-next'

const { isDark, toggleTheme } = useTheme()
const { isAuthed, user, logoutLocal } = useAuth()

async function logout() {
  try {
    await http.post('/auth/logout')
  } catch {
    // ignore network/auth errors; still clear locally
  } finally {
    logoutLocal()
    alerts.success('Logged out', 'See you next time.')
  }
}
</script>

