<template>
  <div :class="[isDark ? 'bg-gradient-to-br from-gray-900 to-gray-800' : 'bg-gradient-to-br from-blue-50 to-indigo-50', 'min-h-screen flex flex-col transition-colors duration-300']">
    <!-- Navbar -->
    <nav :class="[isDark ? 'bg-gray-800/80 backdrop-blur-md' : 'bg-white/80 backdrop-blur-md', 'p-4 shadow-lg sticky top-0 z-10']">
      <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center">
          <div class="relative w-10 h-10 mr-3">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-blue-600 rounded-lg transform rotate-6"></div>
            <div :class="[isDark ? 'bg-gray-800' : 'bg-white', 'absolute inset-0.5 rounded-md flex items-center justify-center']">
              <SendIcon :class="[isDark ? 'text-purple-400' : 'text-purple-600', 'w-6 h-6 transform -rotate-12']" />
            </div>
          </div>
          <span :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-bold']">
            <router-link to="/" class="flex items-center">
              <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-blue-600 font-extrabold">Drop</span>
              <span :class="isDark ? 'text-white' : 'text-gray-800'">N</span>
              <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-500 font-extrabold">Share</span>
            </router-link>
          </span>
        </div>
        <button @click="toggleTheme"
          class="focus:outline-none transition-all duration-300 transform hover:scale-110 p-2 rounded-full"
          :class="isDark ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'">
          <SunIcon v-if="isDark" class="w-5 h-5 text-yellow-400" />
          <MoonIcon v-else class="w-5 h-5 text-indigo-600" />
        </button>
      </div>
    </nav>
  
    <!-- Main Content -->
    <div class="flex-grow flex items-center justify-center p-6">
      <div
        :class="[isDark ? 'bg-gray-800/50 backdrop-blur-md' : 'bg-white/70 backdrop-blur-md', 'w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden flex flex-col justify-center transform transition-all duration-500 hover:scale-[1.01]']">
        
        <!-- Decorative elements -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl"></div>
        
        <div class="p-10 text-center relative z-10">
          <div class="mb-8 flex justify-center">
            <div class="relative w-20 h-20">
              <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full animate-pulse"></div>
              <div :class="[isDark ? 'bg-gray-800' : 'bg-white', 'absolute inset-1 rounded-full flex items-center justify-center']">
                <DownloadCloudIcon :class="[isDark ? 'text-purple-400' : 'text-purple-600', 'w-10 h-10']" />
              </div>
            </div>
          </div>
          
          <h1 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-4xl font-bold mb-4']">
            Download Files
          </h1>
  
          <!-- Loading the share's details -->
          <div v-if="isLoading" class="flex items-center justify-center py-6">
            <LoaderIcon :class="[isDark ? 'text-purple-400' : 'text-purple-600', 'w-6 h-6 mr-3 animate-spin']" />
            <span :class="[isDark ? 'text-gray-300' : 'text-gray-600', 'text-lg']">Loading your files…</span>
          </div>

          <template v-else-if="share">
            <p :class="[isDark ? 'text-gray-300' : 'text-gray-600', 'mb-2 text-xl']">
              {{ share.file_count }} {{ share.file_count === 1 ? 'file' : 'files' }},
              zipped and ready for download.
            </p>

            <p :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'mb-8 text-sm']">
              {{ share.total_size_human }}
              <span v-if="expiresIn && expiresIn !== 'expired'"> · link expires in {{ expiresIn }}</span>
            </p>

            <!-- What is inside the archive -->
            <ul v-if="share.files?.length" class="mb-8 space-y-2 text-left max-h-64 overflow-y-auto">
              <li
                v-for="(file, index) in share.files"
                :key="index"
                :class="[
                  'flex items-center justify-between rounded-lg px-4 py-3',
                  isDark ? 'bg-gray-700/70' : 'bg-gray-100/70'
                ]"
              >
                <span :class="[isDark ? 'text-gray-200' : 'text-gray-700', 'font-medium truncate mr-4']">
                  {{ file.name }}
                </span>
                <span :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'text-sm whitespace-nowrap']">
                  {{ file.size_human }}
                </span>
              </li>
            </ul>

            <button @click="downloadFiles" :class="[
              'px-8 py-4 text-lg text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg',
              isDownloading ?
                (isDark ? 'bg-gray-600 cursor-not-allowed' : 'bg-gray-400 cursor-not-allowed') :
                'bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:ring-purple-500'
            ]" :disabled="isDownloading">
              <div class="flex items-center justify-center">
                <DownloadIcon v-if="!isDownloading" class="w-5 h-5 mr-2" />
                <LoaderIcon v-else class="w-5 h-5 mr-2 animate-spin" />
                <span>
                  {{ isDownloading
                    ? (downloadProgress ? `Downloading… ${downloadProgress}%` : 'Downloading…')
                    : 'Download Zip File' }}
                </span>
              </div>
            </button>

            <div v-if="isDownloading && downloadProgress" class="mt-6 max-w-md mx-auto">
              <div :class="[isDark ? 'bg-gray-700' : 'bg-gray-200', 'w-full h-2 rounded-full overflow-hidden']">
                <div
                  class="h-full bg-gradient-to-r from-purple-600 to-blue-600 transition-all duration-200 ease-out"
                  :style="{ width: downloadProgress + '%' }"
                ></div>
              </div>
            </div>
          </template>

          <div v-if="error" class="mt-8 text-red-500 text-lg p-4 rounded-lg" :class="isDark ? 'bg-red-900/20' : 'bg-red-100'">
            <AlertCircleIcon class="w-5 h-5 inline-block mr-2" />
            {{ error }}
          </div>

          <router-link
            v-if="!isLoading && !share"
            to="/"
            class="mt-8 inline-flex items-center px-6 py-3 rounded-xl text-white transition-all duration-300 transform hover:scale-105 shadow-lg bg-gradient-to-r from-purple-600 to-blue-600"
          >
            Share your own files
          </router-link>
        </div>
      </div>
    </div>
  
    <!-- Footer -->
    <footer :class="[isDark ? 'bg-gray-800/80 backdrop-blur-md' : 'bg-white/80 backdrop-blur-md', 'p-6 shadow-inner']">
      <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <div :class="[isDark ? 'text-gray-400' : 'text-gray-600', 'mb-4 md:mb-0 text-center md:text-left']">
          © 2024 DropNShare. All rights reserved.
        </div>
        <div :class="[isDark ? 'text-gray-400' : 'text-gray-600', 'mb-4 md:mb-0 text-center w-full md:w-auto']">
          Developed by Arham Natiq
        </div>
        <div class="flex space-x-4">
          <a href="https://arhamnatiq.com/" target="_blank"
            :class="[isDark ? 'text-gray-400 hover:text-purple-400' : 'text-gray-600 hover:text-purple-600', 'transition-colors duration-300 transform hover:scale-110']">
            <BriefcaseIcon class="w-5 h-5" />
          </a>
          <a href="https://www.facebook.com/arham.natiq" target="_blank"
            :class="[isDark ? 'text-gray-400 hover:text-blue-400' : 'text-gray-600 hover:text-blue-600', 'transition-colors duration-300 transform hover:scale-110']">
            <FacebookIcon class="w-5 h-5" />
          </a>
          <a href="https://www.instagram.com/sh_arham25" target="_blank"
            :class="[isDark ? 'text-gray-400 hover:text-pink-400' : 'text-gray-600 hover:text-pink-600', 'transition-colors duration-300 transform hover:scale-110']">
            <InstagramIcon class="w-5 h-5" />
          </a>
          <a href="mailto:arhamnatiq25@gmail.com" target="_blank"
            :class="[isDark ? 'text-gray-400 hover:text-red-400' : 'text-gray-600 hover:text-red-600', 'transition-colors duration-300 transform hover:scale-110']">
            <MailIcon class="w-5 h-5" />
          </a>
          <a href="https://www.linkedin.com/in/arham-natiq25" target="_blank"
            :class="[isDark ? 'text-gray-400 hover:text-blue-400' : 'text-gray-600 hover:text-blue-600', 'transition-colors duration-300 transform hover:scale-110']">
            <LinkedinIcon class="w-5 h-5" />
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { 
  SendIcon, 
  BriefcaseIcon, 
  FacebookIcon, 
  InstagramIcon, 
  MailIcon, 
  LinkedinIcon, 
  SunIcon, 
  MoonIcon,
  DownloadCloudIcon,
  DownloadIcon,
  LoaderIcon,
  AlertCircleIcon
} from 'lucide-vue-next'
import api, { apiErrorMessage } from '../lib/api'

export default {
  name: 'DownloadPage',
  components: {
    SendIcon,
    BriefcaseIcon,
    FacebookIcon,
    InstagramIcon,
    MailIcon,
    LinkedinIcon,
    SunIcon,
    MoonIcon,
    DownloadCloudIcon,
    DownloadIcon,
    LoaderIcon,
    AlertCircleIcon
  },
  props: {
    filename: {
      type: String,
      required: true
    }
  },
  setup(props) {
    const isDark = ref(true)
    const isDownloading = ref(false)
    const error = ref(null)

    const toggleTheme = () => {
      isDark.value = !isDark.value
    }

    const isLoading = ref(true)
    const share = ref(null)
    const downloadProgress = ref(0)

    const formatFileSize = (bytes) => {
      if (!bytes) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    const expiresIn = computed(() => {
      if (!share.value?.expires_at) return null
      // The API sends UTC 'Y-m-d H:i:s'; make it a date the browser parses.
      const expiry = new Date(share.value.expires_at.replace(' ', 'T') + 'Z')
      const diffMs = expiry.getTime() - Date.now()
      if (diffMs <= 0) return 'expired'
      const hours = Math.floor(diffMs / 3600000)
      const minutes = Math.floor((diffMs % 3600000) / 60000)
      if (hours > 0) return `${hours}h ${minutes}m`
      return `${minutes}m`
    })

    // Pull the share's details up front so the page can show what it holds
    // and fail clearly when the link is dead.
    const loadShare = async () => {
      isLoading.value = true
      error.value = null

      try {
        const response = await api.get(`/share/${props.filename}`)
        share.value = response.data
      } catch (err) {
        console.error('Error loading share:', err)
        if (err?.response?.status === 410) {
          error.value = 'This link has expired. Ask the sender for a new one.'
        } else if (err?.response?.status === 404) {
          error.value = 'This link no longer exists. It may have expired or been removed.'
        } else {
          error.value = apiErrorMessage(err, 'Could not load this share. Please try again.')
        }
      } finally {
        isLoading.value = false
      }
    }

    const downloadFiles = async () => {
      isDownloading.value = true
      downloadProgress.value = 0
      error.value = null

      try {
        const response = await api.get(`/download/${props.filename}`, {
          responseType: 'blob',
          onDownloadProgress: (progressEvent) => {
            if (!progressEvent.total) return
            downloadProgress.value = Math.round(
              (progressEvent.loaded * 100) / progressEvent.total
            )
          }
        })

        const url = window.URL.createObjectURL(
          new Blob([response.data], { type: 'application/zip' })
        )
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', props.filename)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)

        if (share.value) {
          share.value.download_count = (share.value.download_count || 0) + 1
        }
      } catch (err) {
        console.error('Error downloading file:', err)
        error.value = apiErrorMessage(err, 'An error occurred while downloading the file. Please try again.')
      } finally {
        isDownloading.value = false
        downloadProgress.value = 0
      }
    }

    onMounted(loadShare)

    return {
      isDark,
      isDownloading,
      isLoading,
      error,
      share,
      expiresIn,
      downloadProgress,
      formatFileSize,
      toggleTheme,
      downloadFiles
    }
  }
}
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>