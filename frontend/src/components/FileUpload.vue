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
        <button @click="toggleTheme" class="focus:outline-none transition-shadow duration-100 transform hover:scale-110">
          <SunIcon v-if="isDark" class="w-6 h-6 text-yellow-400" />
          <MoonIcon v-else class="w-6 h-6 text-gray-600" />
        </button>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow flex items-center justify-center p-4">
      <div :class="[isDark ? 'bg-gray-800' : 'bg-white', 'w-full max-w-2xl rounded-lg shadow-xl overflow-hidden']">
        <div class="p-8">
          <h1 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-3xl font-bold mb-6 text-center']">File Upload</h1>
          
          <div 
            :class="[
              isDark ? 'border-gray-600 text-gray-300' : 'border-gray-300 text-gray-600',
              'border-dashed border-2 rounded-lg p-8 text-center cursor-pointer transition-colors duration-300 ease-in-out',
              { 'border-blue-500 bg-blue-100 bg-opacity-20': isDragging }
            ]"
            @dragenter.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="$refs.fileInput.click()"
          >
            <input 
              type="file" 
              ref="fileInput" 
              @change="handleFileInput" 
              multiple 
              class="hidden" 
            />
            <UploadCloudIcon :class="[isDark ? 'text-blue-500' : 'text-blue-600', 'w-16 h-16 mx-auto mb-4']" />
            <p class="text-lg mb-2">
              Drag and drop your files here, or click to select files
            </p>
            <p :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'text-sm']">
              Supports multiple file uploads
            </p>
          </div>
  
          <div v-if="files.length > 0 && !downloadUrl" class="mt-8">
            <h2 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-semibold mb-4']">Selected Files:</h2>
            <ul class="space-y-3">
              <li 
                v-for="(file, index) in files" 
                :key="index"
                :class="[
                  isDark ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200',
                  'flex items-center justify-between rounded-lg p-4 transition-colors duration-300 ease-in-out'
                ]"
              >
                <div class="flex items-center">
                  <FileIcon :class="[isDark ? 'text-blue-500' : 'text-blue-600', 'w-6 h-6 mr-3']" />
                  <span :class="[isDark ? 'text-gray-200' : 'text-gray-700', 'font-medium']">{{ file.name }}</span>
                </div>
                <div class="flex items-center">
                  <span :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'text-sm mr-4']">{{ formatFileSize(file.size) }}</span>
                  <button 
                    @click="removeFile(index)" 
                    class="text-red-500 hover:text-red-600 focus:outline-none"
                  >
                    <XIcon class="w-5 h-5" />
                  </button>
                </div>
              </li>
            </ul>
          </div>
  
          <div class="mt-8 flex justify-end">
            <button 
              @click="uploadFiles" 
              :class="[
                'px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors duration-300 ease-in-out',
                { 'opacity-50 cursor-not-allowed': files.length === 0 || isUploading }
              ]"
              :disabled="files.length === 0 || isUploading"
            >
              {{ isUploading ? 'Uploading...' : 'Upload Files' }}
            </button>
          </div>
  
          <div v-if="downloadUrl" class="mt-8 flex flex-col items-center">
            <h2 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-semibold mb-4']">
              Download Link:
            </h2>
            <div 
              :class="[
                isDark ? 'bg-green-900 border-green-700 text-green-300' : 'bg-green-100 border-green-400 text-green-700',
                'border px-4 py-3 rounded relative text-center w-full sm:w-auto'
              ]" 
              role="alert"
            >
              <p class="font-bold">Success!</p>
              <p class="block sm:inline">Your files have been uploaded and zipped.</p>
              <p class="mt-2">
                <router-link :to="{ name: 'download', params: { filename: extractFilename(downloadUrl) } }" class="text-green-500 hover:underline">
                  Click here to view the download page
                </router-link>
              </p>
            </div>
            <button 
              @click="copyToClipboard(downloadUrl)" 
              :class="[
                isDark ? 'bg-green-800 text-gray-200 hover:bg-gray-700' : 'bg-blue-600 text-white hover:bg-blue-700',
                'mt-4 px-6 py-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500'
              ]"
            >
              Copy URL
            </button>
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
          <a href="https://arhamnatiq.com/" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
            <BriefcaseIcon class="w-6 h-6" />
          </a>
          <a href="https://www.facebook.com/arham.natiq" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
            <FacebookIcon class="w-6 h-6" />
          </a>
          <a href="https://www.instagram.com/sh_arham25" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
            <InstagramIcon class="w-6 h-6" />
          </a>
          <a href="mailto:arhamnatiq25@gmail.com" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
            <MailIcon class="w-6 h-6" />
          </a>
          <a href="https://www.linkedin.com/in/arham-natiq25" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-800', 'transition-colors duration-300']">
            <LinkedinIcon class="w-6 h-6" />
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref } from 'vue'
import { UploadCloudIcon, FileIcon, XIcon, DropletIcon, BriefcaseIcon, FacebookIcon, InstagramIcon, MailIcon, LinkedinIcon, SunIcon, MoonIcon } from 'lucide-vue-next'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'FileUpload',
  components: {
    UploadCloudIcon,
    FileIcon,
    XIcon,
    DropletIcon,
    BriefcaseIcon,
    FacebookIcon,
    InstagramIcon,
    MailIcon,
    LinkedinIcon,
    SunIcon,
    MoonIcon
  },
  setup() {
    const isDark = ref(true)
    const files = ref([])
    const isDragging = ref(false)
    const isUploading = ref(false)
    const downloadUrl = ref(null)

    const toggleTheme = () => {
      isDark.value = !isDark.value
    }

    const copyToClipboard = (text) => {
      navigator.clipboard.writeText(text).then(
        () => {
          Swal.fire({
            title: 'Copied!',
            text: 'URL copied to clipboard',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          });
        },
        (err) => {
          console.error('Failed to copy URL:', err);
          Swal.fire({
            title: 'Error',
            text: 'Failed to copy URL',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      );
    };

    const handleDrop = (e) => {
      isDragging.value = false
      const droppedFiles = Array.from(e.dataTransfer.files)
      addFiles(droppedFiles)
    }

    const handleFileInput = (e) => {
      const selectedFiles = Array.from(e.target.files)
      addFiles(selectedFiles)
    }

    const addFiles = (newFiles) => {
      const updatedFiles = [...files.value]
      newFiles.forEach(file => {
        if (!files.value.some(f => f.name === file.name)) {
          updatedFiles.push(file)
        }
      })
      files.value = updatedFiles
    }

    const removeFile = (index) => {
      files.value.splice(index, 1)
    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    const uploadFiles = async () => {
      if (files.value.length === 0) return

      isUploading.value = true
      const formData = new FormData()
      files.value.forEach((file, index) => {
        formData.append(`files[]`, file)
      })

      try {
        const response = await axios.post('http://localhost:8000/api/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        downloadUrl.value = response.data.download_url
        
        Swal.fire({
          title: 'Upload Complete!',
          text: 'Your files have been successfully uploaded.',
          icon: 'success',
          confirmButtonText: 'OK'
        })

        files.value = []
      } catch (error) {
        console.error('Error uploading files:', error)
        Swal.fire({
          title: 'Error',
          text: 'There was an error uploading your files. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        })
      } finally {
        isUploading.value = false
      }
    }

    const extractFilename = (url) => {
      const parts = url.split('/')
      return parts[parts.length - 1]
    }

    return {
      isDark,
      files,
      isDragging,
      isUploading,
      downloadUrl,
      toggleTheme,
      handleDrop,
      handleFileInput,
      removeFile,
      formatFileSize,
      uploadFiles,
      copyToClipboard,
      extractFilename
    }
  }
}
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>

