<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  post: Object
})

const form = useForm({
  title: props.post.title,
  content: props.post.content,
  image: null,
  images: []
})

const singleImagePreview = ref(null)
const imagePreviews = ref([])
const activeTab = ref('single') // 'single' or 'multiple'

const validateFileSize = (file) => {
  const maxSize = 5 * 1024 * 1024 // 5MB in bytes
  return file.size <= maxSize
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const handleSingleImage = (event) => {
  const file = event.target.files[0]
  if (file && !validateFileSize(file)) {
    alert(`Image size is too large (${formatFileSize(file.size)}). Please reduce image size to maximum 5MB.`)
    event.target.value = '' // Clear the input
    form.image = null
    singleImagePreview.value = null
    return
  }
  
  form.image = file
  
  // Create preview for single image
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      singleImagePreview.value = {
        url: e.target.result,
        name: file.name,
        size: (file.size / 1024 / 1024).toFixed(2) + ' MB'
      }
    }
    reader.readAsDataURL(file)
  } else {
    singleImagePreview.value = null
  }
}

const handleMultipleImages = (event) => {
  const files = Array.from(event.target.files)
  
  // Validate file sizes
  const invalidFiles = files.filter(file => !validateFileSize(file))
  if (invalidFiles.length > 0) {
    const fileList = invalidFiles.map(file => `${file.name} (${formatFileSize(file.size)})`).join(', ')
    alert(`${invalidFiles.length} file(s) are too large: ${fileList}. Please reduce image size to maximum 5MB each.`)
    event.target.value = '' // Clear the input
    return
  }

  // Check total number of files
  if (files.length > 10) {
    alert('You can upload a maximum of 10 images at once.')
    event.target.value = '' // Clear the input
    return
  }

  // Check total size (approximate limit for POST data)
  const totalSize = files.reduce((sum, file) => sum + file.size, 0)
  const maxTotalSize = 40 * 1024 * 1024 // 40MB total limit
  if (totalSize > maxTotalSize) {
    alert(`Total size of all images is too large (${formatFileSize(totalSize)}). Please select fewer or smaller images. Maximum total size: 40MB.`)
    event.target.value = '' // Clear the input
    return
  }

  form.images = files
  
  // Create preview URLs
  imagePreviews.value = []
  files.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreviews.value.push({
        url: e.target.result,
        name: file.name,
        size: (file.size / 1024 / 1024).toFixed(2) + ' MB'
      })
    }
    reader.readAsDataURL(file)
  })
}

const removeImage = (index) => {
  // Create new arrays without the removed image
  const newImages = [...form.images]
  const newPreviews = [...imagePreviews.value]
  
  newImages.splice(index, 1)
  newPreviews.splice(index, 1)
  
  form.images = newImages
  imagePreviews.value = newPreviews
}

const removeSingleImage = () => {
  form.image = null
  singleImagePreview.value = null
  // Clear the file input
  const fileInput = document.getElementById('image')
  if (fileInput) {
    fileInput.value = ''
  }
}

const switchTab = (tab) => {
  activeTab.value = tab
  // Clear the other tab's data when switching
  if (tab === 'single') {
    form.images = []
    imagePreviews.value = []
    const multipleInput = document.getElementById('images')
    if (multipleInput) {
      multipleInput.value = ''
    }
  } else {
    form.image = null
    singleImagePreview.value = null
    const singleInput = document.getElementById('image')
    if (singleInput) {
      singleInput.value = ''
    }
  }
}

const submit = () => {
  form.post(`/posts/${props.post.id}`, {
    _method: 'PUT'
  })
}
</script>

<template>
  <Head title="Edit Post" />
  
  <AppLayout>
    <div class="max-w-2xl mx-auto">
      <h1 class="text-3xl font-bold mb-6">Edit Post</h1>
      
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Title
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': form.errors.title }"
            required
          />
          <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
            {{ form.errors.title }}
          </div>
        </div>

        <div>
          <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Content
          </label>
          <textarea
            id="content"
            v-model="form.content"
            rows="6"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': form.errors.content }"
            required
          ></textarea>
          <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
            {{ form.errors.content }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-4">
            Image Upload Options
          </label>
          
          <!-- Tab Navigation -->
          <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg mb-4">
            <button
              @click="switchTab('single')"
              type="button"
              :class="[
                'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-colors',
                activeTab === 'single'
                  ? 'bg-white text-indigo-700 shadow-sm'
                  : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              Single Image
            </button>
            <button
              @click="switchTab('multiple')"
              type="button"
              :class="[
                'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-colors',
                activeTab === 'multiple'
                  ? 'bg-white text-indigo-700 shadow-sm'
                  : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              Multiple Images
            </button>
          </div>

          <!-- Single Image Tab Content -->
          <div v-if="activeTab === 'single'" class="space-y-4">
            <!-- Current Image Preview -->
            <div v-if="post.image" class="mb-3">
              <p class="text-sm text-gray-600 mb-2">Current image:</p>
              <img :src="`/storage/${post.image}`" :alt="post.title" class="w-32 h-32 object-cover rounded-md border">
            </div>
            
            <input
              id="image"
              @input="handleSingleImage"
              type="file"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': form.errors.image }"
            />
            <p class="text-sm text-gray-500">
              Upload a new image to replace the current one (JPEG, PNG, JPG, GIF). Maximum size: 5MB.
            </p>
            
            <!-- New Single Image Preview -->
            <div v-if="singleImagePreview" class="mt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">New Image Preview:</h4>
              <div class="relative inline-block">
                <div class="w-48 h-48 bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                  <img 
                    :src="singleImagePreview.url" 
                    :alt="singleImagePreview.name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <button
                  @click="removeSingleImage"
                  type="button"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors"
                >
                  ×
                </button>
                <div class="mt-2 text-xs text-gray-500 max-w-48">
                  <p class="truncate">{{ singleImagePreview.name }}</p>
                  <p class="text-gray-400">{{ singleImagePreview.size }}</p>
                </div>
              </div>
            </div>
            
            <div v-if="form.errors.image" class="text-sm text-red-600">
              {{ form.errors.image }}
            </div>
          </div>

          <!-- Multiple Images Tab Content -->
          <div v-if="activeTab === 'multiple'" class="space-y-4">
            <!-- Current Multiple Images Preview -->
            <div v-if="post.images && post.images.length > 0" class="mb-3">
              <p class="text-sm text-gray-600 mb-2">Current images:</p>
              <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
                <div 
                  v-for="(currentImage, index) in post.images" 
                  :key="index"
                  class="relative group"
                >
                  <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                    <img 
                      :src="`/storage/${currentImage}`" 
                      :alt="`${post.title} image ${index + 1}`"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>
            </div>
            
            <input
              id="images"
              @change="handleMultipleImages"
              type="file"
              accept="image/*"
              multiple
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': form.errors['images.0'] }"
            />
            <p class="text-sm text-gray-500">
              Upload new multiple images to replace current ones (JPEG, PNG, JPG, GIF). Maximum size: 5MB each, 10 images max.
            </p>
            
            <!-- New Image Previews -->
            <div v-if="imagePreviews.length > 0" class="mt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">New Preview Images:</h4>
              <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
                <div 
                  v-for="(preview, index) in imagePreviews" 
                  :key="index"
                  class="relative group"
                >
                  <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                    <img 
                      :src="preview.url" 
                      :alt="preview.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <button
                    @click="removeImage(index)"
                    type="button"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors"
                  >
                    ×
                  </button>
                  <div class="mt-1 text-xs text-gray-500">
                    <p class="truncate">{{ preview.name }}</p>
                    <p class="text-gray-400">{{ preview.size }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="form.errors['images.0']" class="text-sm text-red-600">
              {{ form.errors['images.0'] }}
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-4">
          <a
            href="/"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </a>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Updating...
            </span>
            <span v-else>Update Post</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
