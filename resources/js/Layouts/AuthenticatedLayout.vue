<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';

const showMobileSidebar = ref(false);

const toggleMobileSidebar = () => {
    showMobileSidebar.value = !showMobileSidebar.value;
};

const closeMobileSidebar = () => {
    showMobileSidebar.value = false;
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white hidden sm:block"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo and Home Link -->
                            <div class="flex items-center">
                                <Link href="/" class="flex items-center space-x-2 mr-8">
                                    <span class="text-xl font-bold text-gray-900">BlogApp</span>
                                </Link>
                            </div>
                            
                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:flex"
                            >
                                <NavLink
                                    :href="route('posts.index')"
                                    :active="route().current('posts.index')"
                                >
                                    Home (All Posts)
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    My Posts
                                </NavLink>
                                <NavLink
                                    :href="route('posts.create')"
                                    :active="route().current('posts.create')"
                                >
                                    Create Post
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobile Header with Hamburger Menu -->
            <div class="sm:hidden bg-white border-b border-gray-200 p-4 flex justify-between items-center">
                <Link href="/" class="text-lg font-bold text-gray-900">BlogApp</Link>
                <button
                    @click="toggleMobileSidebar"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div
                v-if="showMobileSidebar"
                class="fixed inset-0 flex z-40 sm:hidden"
            >
                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-gray-600 bg-opacity-75"
                    @click="closeMobileSidebar"
                ></div>

                <!-- Sidebar -->
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
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
                            <Link href="/" @click="closeMobileSidebar" class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors">
                                BlogApp
                            </Link>
                        </div>
                        <nav class="mt-5 px-2 space-y-1">
                            <Link
                                :href="route('profile.show')"
                                @click="closeMobileSidebar"
                                :class="[
                                    route().current('profile.show')
                                        ? 'bg-indigo-100 text-indigo-900 border-indigo-300'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group flex items-center px-2 py-2 text-base font-medium rounded-md border-l-4 border-transparent'
                                ]"
                            >
                                <div class="mr-3 w-8 h-8 rounded-full flex items-center justify-center overflow-hidden">
                                    <img 
                                        v-if="$page.props.auth.user.profile_image" 
                                        :src="`/storage/${$page.props.auth.user.profile_image}`"  
                                        :alt="$page.props.auth.user.name" 
                                        class="w-8 h-8 rounded-full object-cover"
                                        @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'"
                                    />
                                    <div 
                                        v-else
                                        class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                    >
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </div>
                                My Profile
                            </Link>
                        </nav>
                    </div>

                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="text-base font-medium text-gray-700 group-hover:text-gray-900">
                                        {{ $page.props.auth.user.name }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">
                                        {{ $page.props.auth.user.email }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1">
                                
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    @click="closeMobileSidebar"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
                                >
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
