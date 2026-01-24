<template>
  <div class="grid gap-8 lg:grid-cols-12">
    <section class="lg:col-span-5">
      <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/60 px-3 py-1 text-xs font-semibold text-slate-700 backdrop-blur dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200">
        <UploadCloudIcon class="h-4 w-4 text-violet-500" />
        Upload & share zip link
      </div>

      <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 sm:text-4xl">
        Upload your files
      </h1>
      <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
        Select multiple files, we zip them, and you get a download page link.
      </p>

      <div class="mt-6 rounded-2xl border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-950/40">
        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Limits</div>
        <ul class="mt-2 space-y-1 text-sm text-slate-600 dark:text-slate-300">
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
            Max file size: 100MB
          </li>
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
            Max total size: 500MB
          </li>
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
            Most common file types supported
          </li>
        </ul>
      </div>

      <div v-if="downloadUrl" class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/40 dark:bg-emerald-950/20">
        <div class="flex items-start gap-3">
          <span class="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-600">
            <CheckCircleIcon class="h-5 w-5 text-white" />
          </span>
          <div class="flex-1">
            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Upload complete</div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
              Your download page is ready.
            </div>
            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
              <RouterLink
                :to="{ name: 'download', params: { filename: downloadFilename } }"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
              >
                <DownloadIcon class="h-4 w-4" />
                Open download page
              </RouterLink>
              <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 dark:hover:bg-slate-900"
                @click="copyToClipboard(downloadUrl)"
              >
                <ClipboardIcon class="h-4 w-4" />
                Copy link
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="lg:col-span-7">
      <div class="rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-soft dark:border-slate-800 dark:bg-slate-950/40">
        <div
          class="group relative overflow-hidden rounded-3xl border-2 border-dashed p-7 transition"
          :class="isDragging ? 'border-violet-500 bg-violet-500/5' : 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950'"
          @dragenter.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @dragover.prevent
          @drop.prevent="handleDrop"
          @click="pickFiles"
          role="button"
          tabindex="0"
        >
          <input ref="fileInput" class="hidden" type="file" multiple @change="handleFileInput" />

          <div class="flex flex-col items-center text-center">
            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600">
              <UploadCloudIcon class="h-7 w-7 text-white" />
            </div>
            <div class="mt-4 text-base font-semibold text-slate-900 dark:text-slate-100">
              Drag and drop files here
            </div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
              or click to select files
            </div>
          </div>
        </div>

        <div v-if="files.length" class="mt-6">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Selected files</div>
            <div class="text-sm text-slate-600 dark:text-slate-300">
              {{ files.length }} file(s) • {{ formatBytes(totalSize) }}
            </div>
          </div>

          <div class="mt-3 space-y-2">
            <div
              v-for="(file, idx) in files"
              :key="fileKey(file)"
              class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-950"
            >
              <div class="flex min-w-0 items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 dark:bg-slate-900">
                  <FileIcon class="h-4 w-4 text-slate-700 dark:text-slate-200" />
                </span>
                <div class="min-w-0">
                  <div class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ file.name }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">{{ formatBytes(file.size) }}</div>
                </div>
              </div>
              <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-900"
                @click="removeFile(idx)"
                aria-label="Remove file"
              >
                <XIcon class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>

        <div v-if="isUploading" class="mt-6">
          <div class="mb-2 flex items-center justify-between text-sm text-slate-600 dark:text-slate-300">
            <span>Uploading…</span>
            <span>{{ uploadProgress }}%</span>
          </div>
          <div class="h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
            <div
              class="h-full rounded-full bg-gradient-to-r from-violet-600 to-blue-600 transition-all"
              :style="{ width: `${uploadProgress}%` }"
            />
          </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100 dark:hover:bg-slate-900"
            :disabled="!files.length || isUploading"
            @click="clearFiles"
          >
            Clear
          </button>
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="!canUpload"
            @click="uploadFiles"
          >
            <LoaderIcon v-if="isUploading" class="h-4 w-4 animate-spin" />
            <UploadIcon v-else class="h-4 w-4" />
            {{ isUploading ? 'Uploading' : 'Upload files' }}
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import {
  CheckCircleIcon,
  ClipboardIcon,
  DownloadIcon,
  FileIcon,
  LoaderIcon,
  UploadCloudIcon,
  UploadIcon,
  XIcon,
} from 'lucide-vue-next'
import { alerts } from '../lib/alerts'
import { http } from '../lib/http'

const MAX_FILE_SIZE = 100 * 1024 * 1024 // 100MB
const MAX_TOTAL_SIZE = 500 * 1024 * 1024 // 500MB

const fileInput = ref(null)
const isDragging = ref(false)
const isUploading = ref(false)
const uploadProgress = ref(0)
const files = ref([])
const downloadUrl = ref('')

const totalSize = computed(() => files.value.reduce((sum, f) => sum + f.size, 0))
const canUpload = computed(() => files.value.length > 0 && !isUploading.value)
const downloadFilename = computed(() => (downloadUrl.value ? downloadUrl.value.split('/').pop() : ''))

function fileKey(file) {
  return `${file.name}-${file.size}-${file.lastModified}`
}

function pickFiles() {
  fileInput.value?.click()
}

function handleDrop(e) {
  isDragging.value = false
  addFiles(Array.from(e.dataTransfer.files || []))
}

function handleFileInput(e) {
  addFiles(Array.from(e.target.files || []))
  // allow selecting same file again
  e.target.value = ''
}

function addFiles(newFiles) {
  let runningTotal = totalSize.value
  const next = [...files.value]

  for (const file of newFiles) {
    const duplicate = next.some((f) => fileKey(f) === fileKey(file))
    if (duplicate) continue

    if (file.size > MAX_FILE_SIZE) {
      alerts.error('File too large', `${file.name} exceeds the 100MB file size limit.`)
      continue
    }

    if (runningTotal + file.size > MAX_TOTAL_SIZE) {
      alerts.error('Total too large', 'Adding this file would exceed the 500MB total upload limit.')
      break
    }

    next.push(file)
    runningTotal += file.size
  }

  files.value = next
}

function removeFile(index) {
  files.value.splice(index, 1)
}

function clearFiles() {
  files.value = []
}

function formatBytes(bytes) {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`
}

async function copyToClipboard(text) {
  try {
    await navigator.clipboard.writeText(text)
    alerts.success('Copied', 'Download link copied to clipboard.')
  } catch (e) {
    console.error(e)
    alerts.error('Copy failed', 'Could not copy the link.')
  }
}

async function uploadFiles() {
  if (!files.value.length) return alerts.error('No files', 'Please select files to upload.')

  isUploading.value = true
  uploadProgress.value = 0

  const formData = new FormData()
  files.value.forEach((file) => formData.append('files[]', file))

  try {
    const response = await http.post('/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (progressEvent) => {
        if (!progressEvent.total) return
        uploadProgress.value = Math.min(
          100,
          Math.round((progressEvent.loaded * 100) / progressEvent.total)
        )
      },
    })

    downloadUrl.value = response.data?.download_url || ''
    if (!downloadUrl.value) {
      alerts.error('Upload failed', 'Upload succeeded but no download URL was returned.')
      return
    }

    alerts.success('Upload complete', 'Your link is ready.')
    files.value = []
  } catch (error) {
    console.error('Error uploading files:', error)

    let errorMessage = 'There was an error uploading your files. Please try again.'
    const status = error?.response?.status
    const apiError = error?.response?.data?.error

    if (status === 413) errorMessage = 'Total file size exceeds server limit.'
    if (apiError) errorMessage = apiError

    alerts.error('Upload failed', errorMessage)
  } finally {
    isUploading.value = false
  }
}
</script>