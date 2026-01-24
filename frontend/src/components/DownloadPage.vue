<template>
  <div class="mx-auto max-w-2xl">
    <div class="rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-soft dark:border-slate-800 dark:bg-slate-950/40 sm:p-8">
      <div class="flex items-start gap-4">
        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600">
          <DownloadCloudIcon class="h-6 w-6 text-white" />
        </span>
        <div class="min-w-0 flex-1">
          <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Download</h1>
          <p class="mt-1 truncate text-sm text-slate-600 dark:text-slate-300">
            File: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ filename }}</span>
          </p>
        </div>
      </div>

      <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
        <div class="flex items-start gap-3">
          <InfoIcon class="mt-0.5 h-5 w-5 text-slate-500 dark:text-slate-400" />
          <div class="text-sm text-slate-600 dark:text-slate-300">
            If the download doesn’t start, try again. This page only handles downloading the already-zipped file.
          </div>
        </div>
      </div>

      <div v-if="error" class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200">
        <div class="flex items-start gap-2">
          <AlertCircleIcon class="mt-0.5 h-5 w-5" />
          <div>{{ error }}</div>
        </div>
      </div>

      <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
        <RouterLink
          to="/upload"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 dark:hover:bg-slate-900"
        >
          <UploadIcon class="h-4 w-4" />
          Upload more
        </RouterLink>

        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
          :disabled="isDownloading"
          @click="downloadFiles"
        >
          <LoaderIcon v-if="isDownloading" class="h-4 w-4 animate-spin" />
          <DownloadIcon v-else class="h-4 w-4" />
          {{ isDownloading ? 'Downloading' : 'Download zip' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, toRefs } from 'vue'
import { RouterLink } from 'vue-router'
import { AlertCircleIcon, DownloadCloudIcon, DownloadIcon, InfoIcon, LoaderIcon, UploadIcon } from 'lucide-vue-next'
import { alerts } from '../lib/alerts'
import { http } from '../lib/http'

const props = defineProps({
  filename: { type: String, required: true },
})
const { filename } = toRefs(props)

const isDownloading = ref(false)
const error = ref('')

async function downloadFiles() {
  isDownloading.value = true
  error.value = ''

  try {
    const response = await http.get(`/download/${props.filename}`, {
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', props.filename)
    document.body.appendChild(link)
    link.click()
    link.remove()

    alerts.success('Download started', 'Your zip file is downloading.')
  } catch (err) {
    console.error('Error downloading file:', err)
    error.value = 'An error occurred while downloading the file. Please try again.'
    alerts.error('Download failed', error.value)
  } finally {
    isDownloading.value = false
  }
}
</script>