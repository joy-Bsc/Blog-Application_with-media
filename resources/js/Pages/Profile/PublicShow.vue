<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  profileUser: Object,
  posts: Array,
  isOwnProfile: Boolean
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}
</script>

<template>
  <Head :title="`${profileUser.name}'s Profile`" />
  
  <AppLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <Link href="/" class="text-blue-600 hover:underline">← Back to Posts</Link>
      </div>

      <!-- Profile Header -->
      <div class="bg-white rounded-lg shadow-sm border p-8 mb-8">
        <div class="flex items-start gap-6">
          <!-- Profile Image -->
          <div class="flex-shrink-0">
            <div class="w-24 h-24 bg-gray-300 rounded-full overflow-hidden">
              <img 
                v-if="profileUser.profile_image" 
                :src="`/storage/${profileUser.profile_image}`" 
                :alt="profileUser.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-500 text-2xl font-bold">
                {{ profileUser.name.charAt(0).toUpperCase() }}
              </div>
            </div>
          </div>

          <!-- Profile Info -->
          <div class="flex-1">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-3xl font-bold text-gray-900">{{ profileUser.name }}</h1>
              <Link 
                v-if="isOwnProfile"
                href="/profile/edit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Edit Profile
              </Link>
            </div>

            <!-- Bio -->
            <p v-if="profileUser.bio" class="text-gray-700 mb-4 whitespace-pre-line">
              {{ profileUser.bio }}
            </p>
            <p v-else class="text-gray-500 italic mb-4">
              {{ isOwnProfile ? 'Add a bio to tell others about yourself.' : 'This user hasn\'t added a bio yet.' }}
            </p>

            <!-- Stats -->
            <div class="flex gap-6 text-sm text-gray-600">
              <span class="font-medium">
                {{ profileUser.posts_count }} {{ profileUser.posts_count === 1 ? 'Post' : 'Posts' }}
              </span>
              <span class="font-medium">
                {{ profileUser.total_likes || 0 }} {{ (profileUser.total_likes || 0) === 1 ? 'Like' : 'Likes' }}
              </span>
              <span class="font-medium">
                Joined {{ formatDate(profileUser.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Posts Section -->
      <div class="bg-white rounded-lg shadow-sm border p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          {{ isOwnProfile ? 'Your Posts' : `${profileUser.name}'s Posts` }}
        </h2>

        <!-- Posts Grid -->
        <div v-if="posts.length > 0" class="space-y-6">
          <div 
            v-for="post in posts" 
            :key="post.id"
            class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-4 p-5">
              <div class="flex-1">
                <Link 
                  :href="`/posts/${post.id}`" 
                  class="text-xl font-semibold text-gray-900 hover:text-blue-600 block mb-2"
                >
                  {{ post.title }}
                </Link>
                <div class="text-sm text-gray-600 mb-3">
                  {{ formatDate(post.created_at) }}
                </div>
                <p class="text-gray-700 line-clamp-3">
                  {{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}
                </p>
              </div>
              
              <!-- Post Image Preview -->
              <div v-if="post.image" class="ml-4 flex-shrink-0">
                <img 
                  :src="`/storage/${post.image}`" 
                  :alt="post.title"
                  class="w-20 h-20 object-cover rounded-lg"
                />
              </div>
            </div>

            <!-- Post Stats -->
            <div class="flex items-center gap-4 text-sm text-gray-500 pt-4 border-t border-gray-100">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ post.likes_count }} {{ post.likes_count === 1 ? 'Like' : 'Likes' }}
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.418 8-9 8a9.013 9.013 0 01-5.314-1.732l-3.054 1.261a.961.961 0 01-.461-.152.959.959 0 01-.494-1.344l1.238-3.024C3.042 15.267 3 14.642 3 12c0-4.418 4.418-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ post.comments_count }} {{ post.comments_count === 1 ? 'Comment' : 'Comments' }}
              </span>
              <Link 
                :href="`/posts/${post.id}`" 
                class="text-blue-600 hover:text-blue-800 ml-auto"
              >
                Read more →
              </Link>
            </div>
          </div>
        </div>

        <!-- No Posts Message -->
        <div v-else class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            {{ isOwnProfile ? 'You haven\'t posted anything yet' : `${profileUser.name} hasn't posted anything yet` }}
          </h3>
          <p class="text-gray-600">
            {{ isOwnProfile ? 'Create your first post to get started!' : 'Check back later for new posts.' }}
          </p>
          <Link 
            v-if="isOwnProfile"
            href="/posts/create" 
            class="inline-block mt-4 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Create Your First Post
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
