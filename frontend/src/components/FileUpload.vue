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
      <div :class="[isDark ? 'bg-gray-800/50 backdrop-blur-md' : 'bg-white/70 backdrop-blur-md', 'w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden relative']">
        <!-- Decorative elements -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl"></div>
        
        <div class="p-8 relative z-10">
          <div class="flex justify-center mb-6">
            <div class="relative w-16 h-16">
              <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full"></div>
              <div :class="[isDark ? 'bg-gray-800' : 'bg-white', 'absolute inset-1 rounded-full flex items-center justify-center']">
                <UploadCloudIcon :class="[isDark ? 'text-purple-400' : 'text-purple-600', 'w-8 h-8']" />
              </div>
            </div>
          </div>
          
          <h1 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-3xl font-bold mb-6 text-center']">File Upload</h1>
          
          <div 
            :class="[
              'border-2 rounded-xl p-8 text-center cursor-pointer transition-all duration-300 ease-in-out relative overflow-hidden',
              isDragging ? 
                (isDark ? 'border-purple-500 bg-purple-500/10' : 'border-purple-500 bg-purple-100/50') : 
                (isDark ? 'border-gray-600 bg-gray-700/30' : 'border-gray-300 bg-gray-100/30')
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
            
            <!-- Animated gradient border on hover/drag -->
            <div v-if="isDragging" class="absolute inset-0 bg-gradient-to-r from-purple-500 to-blue-500 animate-pulse opacity-20"></div>
            
            <div class="relative z-10">
              <div class="flex justify-center mb-4">
                <UploadCloudIcon :class="[
                  isDark ? 'text-purple-400' : 'text-purple-600', 
                  'w-16 h-16 transition-transform duration-500 ease-in-out',
                  isDragging ? 'transform scale-110' : ''
                ]" />
              </div>
              <p class="text-lg mb-2" :class="isDark ? 'text-gray-200' : 'text-gray-700'">
                Drag and drop your files here, or click to select files
              </p>
              <p :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'text-sm']">
                Supports multiple file uploads
              </p>
            </div>
          </div>
  
          <div v-if="files.length > 0 && !downloadUrl" class="mt-8">
            <h2 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-semibold mb-4']">Selected Files:</h2>
            <ul class="space-y-3">
              <li 
                v-for="(file, index) in files" 
                :key="index"
                :class="[
                  'flex items-center justify-between rounded-xl p-4 transition-all duration-300 ease-in-out transform hover:scale-[1.01]',
                  isDark ? 'bg-gray-700/70 hover:bg-gray-700' : 'bg-gray-100/70 hover:bg-gray-200/70'
                ]"
              >
                <div class="flex items-center">
                  <div class="p-2 rounded-lg mr-3" :class="isDark ? 'bg-gray-800' : 'bg-white'">
                    <FileIcon :class="[isDark ? 'text-purple-400' : 'text-purple-600', 'w-5 h-5']" />
                  </div>
                  <span :class="[isDark ? 'text-gray-200' : 'text-gray-700', 'font-medium']">{{ file.name }}</span>
                </div>
                <div class="flex items-center">
                  <span :class="[isDark ? 'text-gray-400' : 'text-gray-500', 'text-sm mr-4']">{{ formatFileSize(file.size) }}</span>
                  <button 
                    @click.stop="removeFile(index)" 
                    class="p-1.5 rounded-full transition-colors duration-300"
                    :class="isDark ? 'bg-gray-800 hover:bg-red-900 text-gray-400 hover:text-red-400' : 'bg-gray-200 hover:bg-red-100 text-gray-500 hover:text-red-500'"
                  >
                    <XIcon class="w-4 h-4" />
                  </button>
                </div>
              </li>
            </ul>
          </div>
  
          <div class="mt-8 flex justify-end">
            <button 
              @click="uploadFiles" 
              :class="[
                'px-6 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-opacity-50 transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center',
                files.length === 0 || isUploading ? 
                  (isDark ? 'bg-gray-700 cursor-not-allowed text-gray-400' : 'bg-gray-300 cursor-not-allowed text-gray-500') : 
                  'bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white focus:ring-purple-500'
              ]"
              :disabled="files.length === 0 || isUploading"
            >
              <UploadIcon v-if="!isUploading" class="w-5 h-5 mr-2" />
              <LoaderIcon v-else class="w-5 h-5 mr-2 animate-spin" />
              {{ isUploading ? 'Uploading...' : 'Upload Files' }}
            </button>
          </div>
  
          <div v-if="downloadUrl" class="mt-8">
            <div class="flex justify-center mb-4">
              <div class="relative w-16 h-16">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-teal-500 rounded-full animate-pulse"></div>
                <div :class="[isDark ? 'bg-gray-800' : 'bg-white', 'absolute inset-1 rounded-full flex items-center justify-center']">
                  <CheckCircleIcon :class="[isDark ? 'text-green-400' : 'text-green-600', 'w-8 h-8']" />
                </div>
              </div>
            </div>
            
            <h2 :class="[isDark ? 'text-white' : 'text-gray-800', 'text-xl font-semibold mb-4 text-center']">
              Upload Complete!
            </h2>
            
            <div 
              :class="[
                'rounded-xl px-6 py-5 relative overflow-hidden',
                isDark ? 'bg-gray-700/70' : 'bg-gray-100/70'
              ]"
            >
              <div class="absolute inset-0 border-2 rounded-xl" :class="isDark ? 'border-green-500/30' : 'border-green-500/20'"></div>
              <div class="relative z-10">
                <p :class="[isDark ? 'text-gray-200' : 'text-gray-700', 'font-medium text-center']">
                  Your files have been uploaded and zipped successfully.
                </p>
                <div class="mt-4 flex flex-col items-center">
                  <router-link 
                    :to="{ name: 'download', params: { filename: extractFilename(downloadUrl) } }" 
                    class="inline-flex items-center px-5 py-2.5 rounded-lg mb-4 transition-all duration-300 transform hover:scale-105 shadow-md"
                    :class="isDark ? 'bg-gradient-to-r from-green-600 to-teal-600 text-white' : 'bg-gradient-to-r from-green-500 to-teal-500 text-white'"
                  >
                    <DownloadIcon class="w-5 h-5 mr-2" />
                    Go to Download Page
                  </router-link>
                  
                  <button 
                    @click="copyToClipboard(downloadUrl)" 
                    class="inline-flex items-center px-5 py-2.5 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md"
                    :class="isDark ? 'bg-gray-800 text-gray-200 hover:bg-gray-700' : 'bg-white text-gray-700 hover:bg-gray-50'"
                  >
                    <ClipboardIcon class="w-5 h-5 mr-2" />
                    Copy Download URL
                  </button>
                </div>
              </div>
            </div>
          </div>
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
          <a href="https://arhamnatiq.com/" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-purple-400' : 'text-gray-600 hover:text-purple-600', 'transition-colors duration-300 transform hover:scale-110']">
            <BriefcaseIcon class="w-5 h-5" />
          </a>
          <a href="https://www.facebook.com/arham.natiq" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-blue-400' : 'text-gray-600 hover:text-blue-600', 'transition-colors duration-300 transform hover:scale-110']">
            <FacebookIcon class="w-5 h-5" />
          </a>
          <a href="https://www.instagram.com/sh_arham25" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-pink-400' : 'text-gray-600 hover:text-pink-600', 'transition-colors duration-300 transform hover:scale-110']">
            <InstagramIcon class="w-5 h-5" />
          </a>
          <a href="mailto:arhamnatiq25@gmail.com" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-red-400' : 'text-gray-600 hover:text-red-600', 'transition-colors duration-300 transform hover:scale-110']">
            <MailIcon class="w-5 h-5" />
          </a>
          <a href="https://www.linkedin.com/in/arham-natiq25" target="_blank" :class="[isDark ? 'text-gray-400 hover:text-blue-400' : 'text-gray-600 hover:text-blue-600', 'transition-colors duration-300 transform hover:scale-110']">
            <LinkedinIcon class="w-5 h-5" />
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref } from 'vue'
import { 
  SendIcon,
  UploadCloudIcon, 
  FileIcon, 
  XIcon, 
  BriefcaseIcon, 
  FacebookIcon, 
  InstagramIcon, 
  MailIcon, 
  LinkedinIcon, 
  SunIcon, 
  MoonIcon,
  UploadIcon,
  LoaderIcon,
  CheckCircleIcon,
  DownloadIcon,
  ClipboardIcon
} from 'lucide-vue-next'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'FileUpload',
  components: {
    SendIcon,
    UploadCloudIcon,
    FileIcon,
    XIcon,
    BriefcaseIcon,
    FacebookIcon,
    InstagramIcon,
    MailIcon,
    LinkedinIcon,
    SunIcon,
    MoonIcon,
    UploadIcon,
    LoaderIcon,
    CheckCircleIcon,
    DownloadIcon,
    ClipboardIcon
  },
  setup() {
    // Inside your <script> section, add these at the beginning of your setup() function
const maxFileSize = 100 * 1024 * 1024; // 100MB
const maxTotalSize = 500 * 1024 * 1024; // 500MB
const allowedFileTypes = [
  // Images
  'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
  // Documents
  'application/pdf', 'application/msword', 
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel',
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'application/vnd.ms-powerpoint',
  'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  'text/plain',
  // Archives
  'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
  // Audio/Video
  'audio/mpeg', 'audio/wav', 'video/mp4', 'video/webm',
  // Code
  'text/html', 'text/css', 'text/javascript', 'application/json'
];

const showError = (message) => {
  Swal.fire({
    title: 'Error',
    text: message,
    icon: 'error',
    confirmButtonText: 'OK',
    background: isDark.value ? '#1f2937' : '#ffffff',
    color: isDark.value ? '#ffffff' : '#000000'
  });
};
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
            showConfirmButton: false,
            background: isDark.value ? '#1f2937' : '#ffffff',
            color: isDark.value ? '#ffffff' : '#000000'
          });
        },
        (err) => {
          console.error('Failed to copy URL:', err);
          Swal.fire({
            title: 'Error',
            text: 'Failed to copy URL',
            icon: 'error',
            confirmButtonText: 'OK',
            background: isDark.value ? '#1f2937' : '#ffffff',
            color: isDark.value ? '#ffffff' : '#000000'
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
  let totalSize = files.value.reduce((sum, file) => sum + file.size, 0);
  const validFiles = [];
  
  for (const file of newFiles) {
    // Check for duplicate files
    const isDuplicate = files.value.some(
      f => f.name === file.name && 
           f.size === file.size && 
           f.lastModified === file.lastModified
    );
    
    if (isDuplicate) continue;
    
    // Check file type
    if (!allowedFileTypes.includes(file.type) && !allowedFileTypes.includes(`.${file.name.split('.').pop()}`)) {
      showError(`File type not supported: ${file.name}`);
      continue;
    }
    
    // Check file size
    if (file.size > maxFileSize) {
      showError(`${file.name} exceeds the 100MB file size limit.`);
      continue;
    }
    
    // Check total size
    if (totalSize + file.size > maxTotalSize) {
      showError('Adding this file would exceed the 500MB total upload limit.');
      break;
    }
    
    validFiles.push(file);
    totalSize += file.size;
  }
  
  files.value = [...files.value, ...validFiles];
};

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
  if (files.value.length === 0) {
    showError('Please select files to upload.');
    return;
  }

  isUploading.value = true;
  const formData = new FormData();
  
  files.value.forEach((file, index) => {
    formData.append(`files[]`, file);
  });

  try {
    const baseUrl = 'https://dropnsharee.arhamnatiq.com/api';
    const response = await axios.post(`${baseUrl}/upload`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round(
          (progressEvent.loaded * 100) / progressEvent.total
        );
        // You can add a progress bar here if you want
        console.log(`Upload progress: ${percentCompleted}%`);
      }
    });
    
    downloadUrl.value = response.data.download_url;

    Swal.fire({
      title: 'Upload Complete!',
      text: 'Your files have been successfully uploaded.',
      icon: 'success',
      confirmButtonText: 'OK',
      background: isDark.value ? '#1f2937' : '#ffffff',
      color: isDark.value ? '#ffffff' : '#000000'
    });

    files.value = [];
  } catch (error) {
    console.error('Error uploading files:', error);
    
    let errorMessage = 'There was an error uploading your files. Please try again.';
    if (error.response) {
      if (error.response.status === 413) {
        errorMessage = 'Total file size exceeds server limit.';
      } else if (error.response.data?.error) {
        errorMessage = error.response.data.error;
      }
    }
    
    showError(errorMessage);
  } finally {
    isUploading.value = false;
  }
};

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
@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>