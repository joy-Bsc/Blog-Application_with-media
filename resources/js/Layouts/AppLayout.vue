<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const auth = usePage().props.auth.user
const showMobileSidebar = ref(false)

const toggleMobileSidebar = () => {
  showMobileSidebar.value = !showMobileSidebar.value
}

const closeMobileSidebar = () => {
  showMobileSidebar.value = false
}
</script>

<template>
  <div>
    <!-- Desktop Navbar -->
    <nav class="bg-gray-900 text-white p-4 justify-between hidden md:flex">
      <Link href="/" class="font-bold text-xl">My Blog</Link>

      <div class="space-x-4">
        <template v-if="auth">
          <div class="flex items-center space-x-4">
            <!-- Profile Image -->
            <Link href="/profile" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
              <img 
                v-if="auth.profile_image"
                :src="`/storage/${auth.profile_image}`" 
                :alt="`${auth.name}'s profile`"
                class="w-8 h-8 rounded-full object-cover border-2 border-gray-300"
              >
              <div 
                v-else
                class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center"
              >
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <span>Welcome, {{ auth.name }}</span>
            </Link>
            <Link href="/dashboard" class="bg-green-600 px-3 py-1 rounded">My Posts</Link>
            <Link href="/posts/create" class="bg-blue-500 px-3 py-1 rounded">+ New Post</Link>
            <form method="POST" action="/logout" style="display: inline;">
              <input type="hidden" name="_token" v-bind:value="$page.props.csrf_token">
              <button 
                type="submit"
                class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 transition-colors"
              >
                Logout
              </button>
            </form>
          </div>
        </template>
        <template v-else>
          <Link href="/login" class="hover:underline">Login</Link>
          <Link href="/register" class="hover:underline">Register</Link>
        </template>
      </div>
    </nav>

    <!-- Mobile Navbar -->
    <nav class="bg-gray-900 text-white p-4 flex justify-between items-center md:hidden">
      <Link href="/" class="font-bold text-xl">My Blog</Link>
      <button
        @click="toggleMobileSidebar"
        class="p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
      >
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="showMobileSidebar"
      class="fixed inset-0 flex z-50 md:hidden"
    >
      <!-- Overlay -->
      <div
        class="fixed inset-0 bg-gray-600 bg-opacity-75"
        @click="closeMobileSidebar"
      ></div>

      <!-- Sidebar -->
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
          <button
            @click="closeMobileSidebar"
            class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
          >
            <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
          <div class="flex-shrink-0 flex items-center px-4">
            <Link href="/" @click="closeMobileSidebar" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
              My Blog
            </Link>
          </div>
          
          <nav class="mt-5 px-2 space-y-1">
            <template v-if="auth">
              <!-- Profile Section -->
              <Link
                href="/profile"
                @click="closeMobileSidebar"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent hover:border-blue-500"
              >
                <img 
                  v-if="auth.profile_image"
                  :src="`/storage/${auth.profile_image}`" 
                  :alt="`${auth.name}'s profile`"
                  class="mr-3 w-8 h-8 rounded-full object-cover border-2 border-gray-300"
                >
                <div 
                  v-else
                  class="mr-3 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium"
                >
                  {{ auth.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <div class="text-sm font-medium">{{ auth.name }}</div>
                  <div class="text-xs text-gray-500">View Profile</div>
                </div>
              </Link>

              <!-- Dashboard -->
              <Link
                href="/dashboard"
                @click="closeMobileSidebar"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent hover:border-green-500"
              >
                <svg class="mr-3 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 7a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10" />
                </svg>
                My Posts
              </Link>

              <!-- Create Post -->
              <Link
                href="/posts/create"
                @click="closeMobileSidebar"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent hover:border-blue-500"
              >
                <svg class="mr-3 h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Post
              </Link>
            </template>
            
            <template v-else>
              <!-- Login -->
              <Link
                href="/login"
                @click="closeMobileSidebar"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent hover:border-blue-500"
              >
                <svg class="mr-3 h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login
              </Link>

              <!-- Register -->
              <Link
                href="/register"
                @click="closeMobileSidebar"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent hover:border-green-500"
              >
                <svg class="mr-3 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Register
              </Link>
            </template>
          </nav>
        </div>

        <!-- Bottom section for authenticated users -->
        <div v-if="auth" class="flex-shrink-0 flex border-t border-gray-200 p-4">
          <form method="POST" action="/logout" class="w-full">
            <input type="hidden" name="_token" v-bind:value="$page.props.csrf_token">
            <button 
              type="submit"
              @click="closeMobileSidebar"
              class="w-full flex items-center justify-center px-4 py-2 text-sm text-white bg-red-500 rounded-md hover:bg-red-600 transition-colors"
            >
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Logout
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto p-6">
      <slot />
    </main>
  </div>
</template>