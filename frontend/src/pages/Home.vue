<template>
  <div class="grid items-center gap-10 lg:grid-cols-12">
    <section class="lg:col-span-6">
      <div
        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/60 px-3 py-1 text-xs font-semibold text-slate-700 backdrop-blur dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"
      >
        <SparklesIcon class="h-4 w-4 text-violet-500" />
        Fast, clean file sharing
      </div>

      <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 sm:text-5xl">
        Drop files. Share a link.
        <span class="bg-gradient-to-r from-violet-500 to-blue-600 bg-clip-text text-transparent">Done.</span>
      </h1>

      <p class="mt-4 text-lg leading-relaxed text-slate-600 dark:text-slate-300">
        Upload multiple files, we zip them, and you get a download page instantly. Designed for speed and a great user
        experience.
      </p>

      <div class="mt-7 flex flex-col gap-3 sm:flex-row">
        <RouterLink
          to="/upload"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700"
        >
          <UploadCloudIcon class="h-5 w-5" />
          Start uploading
        </RouterLink>

        <RouterLink
          to="/signup"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 dark:hover:bg-slate-900"
        >
          Create account
          <ArrowRightIcon class="h-5 w-5" />
        </RouterLink>
      </div>

      <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/40">
          <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Multi-file upload</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Drag, drop, and send.</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/40">
          <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Instant link</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Copy and share.</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/40">
          <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Modern UI</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Clean and fast.</div>
        </div>
      </div>
    </section>

    <section class="lg:col-span-6">
      <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-soft dark:border-slate-800 dark:bg-slate-950/40">
        <div class="absolute -top-20 -right-20 h-40 w-40 rounded-full bg-violet-500/20 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600">
                <ShieldCheckIcon class="h-5 w-5 text-white" />
              </span>
              <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Share safely</div>
                <div class="text-sm text-slate-600 dark:text-slate-300">Simple, predictable flow.</div>
              </div>
            </div>
            <RouterLink
              to="/upload"
              class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
            >
              Try now
              <ArrowRightIcon class="h-4 w-4" />
            </RouterLink>
          </div>

          <div class="mt-6 grid gap-3">
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
              <UploadIcon class="mt-0.5 h-5 w-5 text-violet-500" />
              <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Upload</div>
                <div class="text-sm text-slate-600 dark:text-slate-300">Select files or drag and drop.</div>
              </div>
            </div>
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
              <PackageIcon class="mt-0.5 h-5 w-5 text-blue-600" />
              <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Zip</div>
                <div class="text-sm text-slate-600 dark:text-slate-300">We package your files automatically.</div>
              </div>
            </div>
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
              <LinkIcon class="mt-0.5 h-5 w-5 text-emerald-600" />
              <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Share</div>
                <div class="text-sm text-slate-600 dark:text-slate-300">Send the download link to anyone.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import {
  ArrowRightIcon,
  LinkIcon,
  PackageIcon,
  ShieldCheckIcon,
  SparklesIcon,
  UploadCloudIcon,
  UploadIcon,
} from 'lucide-vue-next'
</script>

