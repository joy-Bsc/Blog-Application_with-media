<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  posts: Array
})

const deletePost = (post) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(`/posts/${post.id}`)
  }
}

// Image modal functionality
const showImageModal = ref(false)
const selectedImage = ref('')

const openImageModal = (imageSrc) => {
  selectedImage.value = imageSrc
  showImageModal.value = true
  document.body.style.overflow = 'hidden' // Prevent background scrolling
}

const closeImageModal = () => {
  showImageModal.value = false
  selectedImage.value = ''
  document.body.style.overflow = 'auto' // Restore scrolling
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Posts Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header with Create Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Your Posts</h3>
                            <Link 
                                href="/posts/create" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Create New Post
                            </Link>
                        </div>

                        <!-- Posts List -->
                        <div v-if="posts.length > 0" class="space-y-4">
                            <div v-for="post in posts" :key="post.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <!-- Single Image Preview -->
                                                <div v-if="post.image" class="mb-3">
                                                    <img 
                                                        :src="`/storage/${post.image}`" 
                                                        :alt="post.title" 
                                                        class="w-16 h-16 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity"
                                                        @click.stop="openImageModal(`/storage/${post.image}`)"
                                                    />
                                                </div>
                                                
                                                <!-- Multiple Images Preview -->
                                                <div v-if="post.images && post.images.length > 0" class="mb-3">
                                                    <div class="flex gap-2">
                                                        <div 
                                                            v-for="(image, index) in post.images.slice(0, 3)" 
                                                            :key="index"
                                                            class="relative"
                                                        >
                                                            <div class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden">
                                                                <img 
                                                                    :src="`/storage/${image}`" 
                                                                    :alt="`${post.title} image ${index + 1}`"
                                                                    class="w-full h-full object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                                                    @click.stop="openImageModal(`/storage/${image}`)"
                                                                />
                                                            </div>
                                                            <!-- Show count if more than 3 images -->
                                                            <div 
                                                                v-if="index === 2 && post.images.length > 3" 
                                                                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white text-xs font-medium rounded-md pointer-events-none"
                                                            >
                                                                +{{ post.images.length - 3 }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <Link :href="`/posts/${post.id}`" class="block">
                                            
                                            <h4 class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ post.title }}
                                            </h4>
                                            <p class="text-gray-600 mt-2 line-clamp-2">
                                                {{ post.content.substring(0, 150) }}{{ post.content.length > 150 ? '...' : '' }}
                                            </p>
                                            
                                            <!-- Like and Comment Counts -->
                                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                    </svg>
                                                    <span>{{ post.likes_count || 0 }} likes</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                    <span>{{ post.comments_count || 0 }} comments</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                                <span>Created {{ new Date(post.created_at).toLocaleDateString() }}</span>
                                                <span>{{ new Date(post.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
                                            </div>
                                        </Link>
                                    </div>
                                    
                                    <div class="flex gap-2 ml-4">
                                        <Link 
                                            :href="`/posts/${post.id}/edit`"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors"
                                        >
                                            Edit
                                        </Link>
                                        <button 
                                            @click="deletePost(post)"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No posts yet</h3>
                            <p class="text-gray-500 mb-4">Get started by creating your first blog post.</p>
                            <Link 
                                href="/posts/create" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Create Your First Post
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div 
            v-if="showImageModal" 
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
            @click="closeImageModal"
        >
            <div class="relative max-w-sm max-h-full p-4">
                <img 
                    :src="selectedImage" 
                    class="max-w-full max-h-full object-contain rounded-lg"
                    @click.stop
                />
                <button 
                    @click="closeImageModal"
                    class="absolute top-2 right-2 text-white bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
