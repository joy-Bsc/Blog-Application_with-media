<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  posts: Object, // Changed from Array to Object to receive paginated data
  search: String
})

const auth = usePage().props.auth.user
const searchQuery = ref(props.search || '')
const loading = ref(false)
const allPosts = ref([])

// Initialize posts from first page
onMounted(() => {
  allPosts.value = props.posts.data || []
})

// Watch for props.posts changes (useful for search)
watch(() => props.posts, (newPosts) => {
  if (newPosts && newPosts.data) {
    // If it's page 1 (current_page === 1), replace all posts
    if (newPosts.current_page === 1) {
      allPosts.value = newPosts.data
    }
  }
}, { deep: true })

// Watch for search input changes and perform search with debouncing
let searchTimeout = null
watch(searchQuery, (newQuery) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    router.get('/', { search: newQuery }, {
      preserveState: false,
      preserveScroll: false,
      replace: true,
      only: ['posts'],
      onSuccess: (page) => {
        if (page.props.posts && page.props.posts.data) {
          allPosts.value = page.props.posts.data
        }
      }
    })
  }, 500) // 500ms debounce
})

const clearSearch = () => {
  searchQuery.value = ''
}

// Load more posts function
const loadMorePosts = async () => {
  if (loading.value || !props.posts.next_page_url) return
  
  loading.value = true
  
  try {
    const url = new URL(props.posts.next_page_url)
    const page = url.searchParams.get('page')
    
    const params = new URLSearchParams({ page })
    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }
    
    const response = await fetch(`/api/posts?${params.toString()}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    
    if (data.posts && data.posts.data) {
      // Append new posts to existing ones
      allPosts.value = [...allPosts.value, ...data.posts.data]
      
      // Update the posts object with new pagination info
      Object.assign(props.posts, data.posts)
    }
  } catch (error) {
    console.error('Error loading more posts:', error)
  } finally {
    loading.value = false
  }
}

// Infinite scroll functionality
const handleScroll = () => {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  const windowHeight = window.innerHeight
  const documentHeight = document.documentElement.offsetHeight
  
  // Load more when user is 200px from bottom
  if (scrollTop + windowHeight >= documentHeight - 200) {
    loadMorePosts()
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  
  // Handle page visibility change (when user comes back to the page)
  const handleVisibilityChange = () => {
    if (!document.hidden) {
      // Page is now visible, refresh the first page of posts to get updated like/comment counts
      const currentSearch = searchQuery.value
      router.reload({
        only: ['posts'],
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          if (page.props.posts && page.props.posts.data && page.props.posts.current_page === 1) {
            // Update the first page with fresh data
            const freshPosts = page.props.posts.data
            const existingPosts = allPosts.value
            
            // Replace the first page posts with fresh data, keep the rest
            if (existingPosts.length > freshPosts.length) {
              allPosts.value = [...freshPosts, ...existingPosts.slice(freshPosts.length)]
            } else {
              allPosts.value = freshPosts
            }
          }
        }
      })
    }
  }
  
  document.addEventListener('visibilitychange', handleVisibilityChange)
  
  // Also handle when the user navigates back via browser history
  const handlePopState = () => {
    // Small delay to ensure the page has loaded
    setTimeout(() => {
      router.reload({
        only: ['posts'],
        preserveState: true,
        preserveScroll: true
      })
    }, 100)
  }
  
  window.addEventListener('popstate', handlePopState)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('visibilitychange', () => {})
  window.removeEventListener('popstate', () => {})
})
</script>

<template>
  <AppLayout>
    <div class="mb-6">
      <div class="flex justify-between items-start mb-4">
        <h1 class="text-3xl font-bold">All Blog Posts</h1>
        
        <!-- Search Bar -->
        <div class="relative max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search posts and authors..."
            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center"
          >
            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Search Results Info -->
      <div v-if="searchQuery" class="text-sm text-gray-600">
        {{ allPosts.length }} result{{ allPosts.length !== 1 ? 's' : '' }} found for "{{ searchQuery }}"
        <span v-if="props.posts.total && props.posts.total > allPosts.length">
          (showing {{ allPosts.length }} of {{ props.posts.total }})
        </span>
      </div>
    </div>

    <!-- Posts List -->
    <div v-if="allPosts.length > 0" class="space-y-4">
      <div v-for="post in allPosts" :key="post.id" class="border rounded-lg p-4 shadow-sm">
        <Link :href="`/posts/${post.id}`" class="block hover:bg-gray-50 hover:p-11 hover:rounded-lg transition-all duration-500 ease-in-out">
          <!-- Single Image Section -->
          <div v-if="post.image" class="mb-4">
            <img 
              :src="`/storage/${post.image}`" 
              :alt="post.title" 
              class="w-full h-48 object-cover rounded-md"
            />
          </div>
          
          <!-- Multiple Images Section -->
          <div v-if="post.images && post.images.length > 0" class="mb-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
              <div 
                v-for="(image, index) in post.images.slice(0, 4)" 
                :key="index"
                class="relative"
              >
                <div class="aspect-square bg-gray-100 rounded-md overflow-hidden">
                  <img 
                    :src="`/storage/${image}`" 
                    :alt="`${post.title} image ${index + 1}`"
                    class="w-full h-full object-cover"
                  />
                </div>
                <!-- Show count if more than 4 images -->
                <div 
                  v-if="index === 3 && post.images.length > 4" 
                  class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white text-sm font-medium rounded-md"
                >
                  +{{ post.images.length - 4 }} more
                </div>
              </div>
            </div>
          </div>
          
          <h2 class="text-xl font-semibold hover:text-blue-600">{{ post.title }}</h2>
          <p class="text-gray-700 mt-2 line-clamp-3">{{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}</p>
          
          <!-- Author and Timestamp -->
          <div class="flex justify-between items-center mt-3 text-sm text-gray-500">
            <span>by {{ post.user.name }}</span>
            <div class="flex flex-col items-end">
              <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
              <span>{{ new Date(post.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
            </div>
          </div>
          
          <!-- Like and Comment Counts -->
          <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
              </svg>
              {{ post.likes_count || 0 }} likes
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>
              {{ post.comments_count || 0 }} comments
            </span>
          </div>
        </Link>
      </div>
    </div>
    
    <!-- Loading Spinner -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="flex items-center space-x-2">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-600">Loading more posts...</span>
      </div>
    </div>
    
    <!-- Load More Button (fallback for manual loading) -->
    <div v-if="!loading && allPosts.length > 0 && props.posts.next_page_url" class="flex justify-center py-8">
      <button
        @click="loadMorePosts"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
      >
        Load More Posts
      </button>
    </div>
    
    <!-- End of Posts Indicator -->
    <div v-if="!loading && allPosts.length > 0 && !props.posts.next_page_url" class="text-center py-8">
      <p class="text-gray-500">You've reached the end of all posts!</p>
    </div>
    
    <!-- No Results Message -->
    <div v-else-if="allPosts.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.239 0-4.236-.18-6.235-.955C3.166 13.747 1 11.906 1 9.71V6a3 3 0 013-3h16a3 3 0 013 3v3.71c0 2.196-2.166 4.037-4.765 4.335A7.962 7.962 0 0112 15z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">
        {{ searchQuery ? 'No posts found' : 'No posts yet' }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchQuery ? `No posts or authors match "${searchQuery}"` : 'Get started by creating a new post.' }}
      </p>
      <div v-if="!searchQuery && auth" class="mt-6">
        <Link
          href="/posts/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          New Post
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
