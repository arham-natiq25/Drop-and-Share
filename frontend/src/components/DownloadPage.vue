<template>
    <div :class="[isDark ? 'bg-gray-900' : 'bg-gray-100', 'min-h-screen flex flex-col transition-colors duration-300']">
      <!-- Navbar -->
      <nav :class="[isDark ? 'bg-gray-800' : 'bg-white', 'p-4 shadow-md']">
        <div class="container mx-auto flex justify-between items-center">
          <div class="flex items-center">
            <DropletIcon :class="[isDark ? 'text-blue-500' : 'text-blue-600', 'w-8 h-8 mr-2']" />
            <span :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-bold']">
              <router-link to="/">DropNShare</router-link>
            </span>
          </div>
          <button @click="toggleTheme"
            class="focus:outline-none transition-shadow duration-100 transform hover:scale-110">
            <SunIcon v-if="isDark" class="w-6 h-6 text-yellow-400" />
            <MoonIcon v-else class="w-6 h-6 text-gray-600" />
          </button>
        </div>
      </nav>
  
      <!-- Main Content -->
      <div class="flex-grow flex items-center justify-center p-4">
        <div
          :class="[isDark ? 'bg-gray-800' : 'bg-white', 'w-full max-w-3xl h-[60vh] rounded-xl shadow-2xl overflow-hidden flex flex-col justify-center']">
          <div class="p-10 text-center">
            <h1 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-4xl font-bold mb-8']">
              Download Files
            </h1>
  
            <p :class="[isDark ? 'text-gray-300' : 'text-gray-600', 'mb-8 text-xl']">
              Your files have been zipped and are ready for download.
            </p>
  
            <button @click="downloadFiles" :class="[
              'px-8 py-4 text-lg bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-all duration-300 ease-in-out',
              { 'opacity-50 cursor-not-allowed': isDownloading }
            ]" :disabled="isDownloading">
              {{ isDownloading ? 'Downloading...' : 'Download Zip File' }}
            </button>
  
            <div v-if="error" class="mt-8 text-red-500 text-lg">
              {{ error }}
            </div>
          </div>
        </div>
      </div>
  
      <!-- Footer -->
      <footer :class="[isDark ? 'bg-gray-800' : 'bg-gray-200', 'p-4']">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
          <div :class="[isDark ? 'text-gray-400' : 'text-gray-600', 'mb-4 md:mb-0 text-center md:text-left']">
            © 2024 DropNShare. All rights reserved.
          </div>
          <div :class="[isDark ? 'text-gray-400' : 'text-gray-600', 'mb-4 md:mb-0 text-center w-full md:w-auto']">
            Developed by Arham Natiq
          </div>
          <div class="flex space-x-4">
            <a href="https://arhamnatiq.com/" target="_blank"
              :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
              <BriefcaseIcon class="w-6 h-6" />
            </a>
            <a href="https://www.facebook.com/arham.natiq" target="_blank"
              :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
              <FacebookIcon class="w-6 h-6" />
            </a>
            <a href="https://www.instagram.com/sh_arham25" target="_blank"
              :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
              <InstagramIcon class="w-6 h-6" />
            </a>
            <a href="mailto:arhamnatiq25@gmail.com" target="_blank"
              :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
              <MailIcon class="w-6 h-6" />
            </a>
            <a href="https://www.linkedin.com/in/arham-natiq25" target="_blank"
              :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
              <LinkedinIcon class="w-6 h-6" />
            </a>
          </div>
        </div>
      </footer>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue'
  import { DropletIcon, BriefcaseIcon, FacebookIcon, InstagramIcon, MailIcon, LinkedinIcon, SunIcon, MoonIcon } from 'lucide-vue-next'
  import axios from 'axios'
  
  export default {
    name: 'DownloadPage',
    components: {
      DropletIcon,
      BriefcaseIcon,
      FacebookIcon,
      InstagramIcon,
      MailIcon,
      LinkedinIcon,
      SunIcon,
      MoonIcon
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
  
      const downloadFiles = async () => {
        isDownloading.value = true
        error.value = null
  
        try {
          const response = await axios.get(`http://localhost:8000/api/download/${props.filename}`, {
            responseType: 'blob'
          })
  
          const url = window.URL.createObjectURL(new Blob([response.data]))
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', props.filename)
          document.body.appendChild(link)
          link.click()
          link.remove()
        } catch (err) {
          console.error('Error downloading file:', err)
          error.value = 'An error occurred while downloading the file. Please try again.'
        } finally {
          isDownloading.value = false
        }
      }
  
      return {
        isDark,
        isDownloading,
        error,
        toggleTheme,
        downloadFiles
      }
    }
  }
  </script>
  
  <style scoped>
  /* Add any additional styles here if needed */
  </style>
  
  