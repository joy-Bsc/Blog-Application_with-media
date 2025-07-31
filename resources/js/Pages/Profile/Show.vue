<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="My Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Profile
                </h2>
                <Link 
                    href="/profile/edit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors"
                >
                    Edit Profile
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-900">
                        <!-- Profile Content -->
                        <div class="text-center">
                            <!-- Profile Image -->
                            <div class="mb-6">
                                <img 
                                    v-if="user.profile_image"
                                    :src="`/storage/${user.profile_image}`" 
                                    :alt="`${user.name}'s profile`"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg mx-auto"
                                >
                                <div 
                                    v-else
                                    class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center border-4 border-gray-200 shadow-lg mx-auto"
                                >
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Profile Info -->
                            <div class="space-y-4">
                                <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
                                <p class="text-lg text-gray-600">{{ user.email }}</p>
                                <p class="text-md text-gray-500">Joined {{ new Date(user.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                                <div class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span class="text-blue-800 font-medium">{{ user.posts_count || 0 }} Blog Posts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
