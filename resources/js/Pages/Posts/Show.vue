<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Toast from '@/Components/Toast.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  post: Object
})

const auth = usePage().props.auth.user
const csrfToken = usePage().props.csrf_token
const selectedImage = ref(null)
const showModal = ref(false)

// Toast system
const { toasts, removeToast, warning, success, error } = useToast()

// Confirmation state
const showDeleteConfirm = ref(false)
const commentToDelete = ref(null)

// Reactive like state
const isLiked = ref(props.post.is_liked_by_user || false)
const likesCount = ref(props.post.likes_count || 0)
const isLiking = ref(false)

// Reactive comments state
const commentsCount = ref(props.post.comments_count || 0)

// Comment form
const commentForm = useForm({
  content: '',
  parent_id: null,
  _token: csrfToken
})

// Edit comment form
const editCommentForm = useForm({
  content: '',
  _token: csrfToken
})

// Reply forms - separate form for each comment
const replyForms = ref({})

// Reply states
const replyingTo = ref(null)
const editingComment = ref(null)

// Helper functions for permissions
const canEditComment = (comment) => {
  return auth && auth.id === comment.user_id
}

const canDeleteComment = (comment) => {
  if (!auth) return false
  // Comment author can delete their own comment
  if (auth.id === comment.user_id) return true
  // Post creator can delete any comment on their post
  return auth.id === props.post.user_id
}

const deletePost = () => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(`/posts/${props.post.id}`)
  }
}

const toggleLike = () => {
  if (!auth) {
    warning('Login Required', 'Please login to like posts')
    return
  }
  
  if (isLiking.value) {
    return // Prevent double-clicking
  }
  
  isLiking.value = true
  
  // Optimistically update the UI
  const wasLiked = isLiked.value
  isLiked.value = !wasLiked
  likesCount.value = isLiked.value ? likesCount.value + 1 : likesCount.value - 1
  
  router.post(`/posts/${props.post.id}/like`, {
    _token: csrfToken
  }, {
    preserveScroll: true,
    onFinish: () => {
      isLiking.value = false
    },
    onError: (errors) => {
      // Revert the optimistic update on error
      isLiked.value = wasLiked
      likesCount.value = wasLiked ? likesCount.value + 1 : likesCount.value - 1
      console.error('Error toggling like:', errors)
    }
  })
}

const submitComment = () => {
  if (!auth) {
    warning('Login Required', 'Please login to comment')
    return
  }
  
  commentForm.post(`/posts/${props.post.id}/comments`, {
    preserveScroll: true,
    onSuccess: () => {
      commentForm.reset()
      replyingTo.value = null
      commentsCount.value += 1
    }
  })
}

const submitReply = (commentId) => {
  if (!auth) {
    warning('Login Required', 'Please login to reply')
    return
  }
  
  const replyForm = replyForms.value[commentId]
  if (!replyForm || !replyForm.content.trim()) {
    return
  }
  
  // Create a form submission with reply data
  const form = useForm({
    content: replyForm.content,
    parent_id: commentId,
    _token: csrfToken
  })
  
  form.post(`/posts/${props.post.id}/comments`, {
    preserveScroll: true,
    onSuccess: () => {
      // Reset the specific reply form
      replyForms.value[commentId] = { content: '', processing: false }
      replyingTo.value = null
      commentsCount.value += 1
    },
    onStart: () => {
      replyForm.processing = true
    },
    onFinish: () => {
      replyForm.processing = false
    }
  })
}

const startReply = (commentId) => {
  replyingTo.value = commentId
  // Initialize reply form for this comment if it doesn't exist
  if (!replyForms.value[commentId]) {
    replyForms.value[commentId] = { content: '', processing: false }
  }
}

const cancelReply = () => {
  const currentReplyId = replyingTo.value
  replyingTo.value = null
  
  // Clear the reply form content
  if (currentReplyId && replyForms.value[currentReplyId]) {
    replyForms.value[currentReplyId].content = ''
  }
}

const startEdit = (comment) => {
  editingComment.value = comment.id
  editCommentForm.content = comment.content
}

const cancelEdit = () => {
  editingComment.value = null
  editCommentForm.content = ''
}

const updateComment = (commentId) => {
  editCommentForm.put(`/comments/${commentId}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingComment.value = null
      editCommentForm.reset()
    }
  })
}

const deleteComment = (commentId) => {
  commentToDelete.value = commentId
  showDeleteConfirm.value = true
}

const confirmDelete = () => {
  if (commentToDelete.value) {
    router.delete(`/comments/${commentToDelete.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        commentsCount.value -= 1
        success('Comment Deleted', 'The comment has been successfully deleted.')
        showDeleteConfirm.value = false
        commentToDelete.value = null
      },
      onError: () => {
        error('Delete Failed', 'Failed to delete the comment. Please try again.')
        showDeleteConfirm.value = false
        commentToDelete.value = null
      }
    })
  }
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  commentToDelete.value = null
}

const openImageModal = (image) => {
  selectedImage.value = image
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedImage.value = null
}

const goBack = () => {
  // Use router.visit with forceReload to ensure fresh data
  router.visit('/', {
    preserveState: false,
    preserveScroll: false,
    replace: false
  })
}
</script>

<template>
  <Head :title="post.title" />
  
  <AppLayout>
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <button 
          @click="goBack" 
          class="text-blue-600 hover:underline flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to Posts
        </button>
      </div>

      <!-- Post Content -->
      <article class="bg-white rounded-lg shadow-sm border p-8 hover:p-11 transition-all duration-300 ease-in-out">
        <header class="mb-6">
          <h1 class="text-4xl font-bold text-gray-900 mb-4 ">{{ post.title }}</h1>
          <div class="flex items-center text-gray-600 text-sm">
            <span>By 
              <Link 
                :href="`/profile/${post.user.id}`" 
                class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
              >
                {{ post.user.name }}
              </Link>
            </span>
            <span class="mx-2">•</span>
            <time>{{ new Date(post.created_at).toLocaleDateString() }}</time>
            <span class="mx-2">•</span>
            <time>{{ new Date(post.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</time>
          </div>
        </header>

        <!-- Image Section -->
        <div v-if="post.image" class="mb-6">
          <img 
            :src="`/storage/${post.image}`" 
            :alt="post.title" 
            class="w-full max-w-lg mx-auto rounded-lg shadow-md"
          />
        </div>

        <!-- Multiple Images Section -->
        <div v-if="post.images && post.images.length > 0" class="mb-6">
         
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <div 
              v-for="(image, index) in post.images" 
              :key="index"
              class="group cursor-pointer"
              @click="openImageModal(image)"
            >
              <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-indigo-300 transition-colors">
                <img 
                  :src="`/storage/${image}`" 
                  :alt="`${post.title} image ${index + 1}`"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="prose max-w-none">
          <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ post.content }}</p>
        </div>

        <!-- Like and Comment Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <!-- Like Button -->
          <div class="flex items-center gap-4 mb-6">
            <button 
              @click="toggleLike"
              :disabled="isLiking"
              :class="[
                'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors',
                isLiked 
                  ? 'bg-red-100 text-red-700 hover:bg-red-200' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                isLiking ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              <svg 
                class="w-5 h-5" 
                :fill="isLiked ? 'currentColor' : 'none'"
                :stroke="isLiked ? 'none' : 'currentColor'"
                viewBox="0 0 24 24"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
              {{ isLiked ? 'Liked' : 'Like' }} ({{ likesCount }})
            </button>
          </div>

          <!-- Comments Section -->
          <div class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">
              Comments ({{ commentsCount }})
            </h3>

            <!-- Comment Form -->
            <div v-if="auth" class="bg-gray-50 rounded-lg p-4">
              <form @submit.prevent="submitComment">
                <textarea
                  v-model="commentForm.content"
                  placeholder="Write a comment..."
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  rows="3"
                  required
                ></textarea>
                <div class="flex justify-between items-center mt-3">
                  <span v-if="commentForm.errors.content" class="text-red-500 text-sm">
                    {{ commentForm.errors.content }}
                  </span>
                  <button 
                    type="submit"
                    :disabled="commentForm.processing || !commentForm.content.trim()"
                    class="ml-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ commentForm.processing ? 'Posting...' : 'Post Comment' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Login prompt for non-authenticated users -->
            <div v-else class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-gray-600">
                <Link href="/login" class="text-blue-600 hover:underline">Login</Link> 
                to post a comment
              </p>
            </div>

            <!-- Comments List -->
            <div class="space-y-4">
              <!-- Comment Component -->
              <div 
                v-for="comment in post.comments" 
                :key="comment.id"
                class="bg-white border border-gray-200 rounded-lg p-4 hover:p-7 transition-all duration-300 ease-in-out"
              >
                <!-- Main Comment -->
                <div class="flex justify-between items-start mb-3">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <Link 
                        :href="`/profile/${comment.user.id}`" 
                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline"
                      >
                        {{ comment.user.name }}
                      </Link>
                      <span class="text-gray-500 text-sm">
                        {{ new Date(comment.created_at).toLocaleDateString() }}
                        {{ new Date(comment.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                      </span>
                      <span v-if="comment.edited_at" class="text-gray-400 text-xs">(edited)</span>
                    </div>
                    
                    <!-- Comment Content or Edit Form -->
                    <div v-if="editingComment === comment.id">
                      <form @submit.prevent="updateComment(comment.id)">
                        <textarea
                          v-model="editCommentForm.content"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                          rows="3"
                          required
                        ></textarea>
                        <div class="flex gap-2 mt-2">
                          <button 
                            type="submit"
                            :disabled="editCommentForm.processing || !editCommentForm.content.trim()"
                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 disabled:opacity-50"
                          >
                            {{ editCommentForm.processing ? 'Saving...' : 'Save' }}
                          </button>
                          <button 
                            type="button"
                            @click="cancelEdit"
                            class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600"
                          >
                            Cancel
                          </button>
                        </div>
                      </form>
                    </div>
                    <div v-else>
                      <p class="text-gray-700 whitespace-pre-line">{{ comment.content }}</p>
                    </div>
                  </div>
                  
                  <!-- Comment Actions -->
                  <div v-if="(canEditComment(comment) || canDeleteComment(comment)) && editingComment !== comment.id" class="flex gap-2 ml-4">
                    <button 
                      v-if="canEditComment(comment)"
                      @click="startEdit(comment)"
                      class="text-blue-600 hover:text-blue-800"
                      title="Edit comment"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                      </svg>
                    </button>
                    <button 
                      v-if="canDeleteComment(comment)"
                      @click="deleteComment(comment.id)"
                      class="text-red-600 hover:text-red-800"
                      title="Delete comment"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Comment Actions Bar -->
                <div v-if="editingComment !== comment.id" class="flex items-center gap-4 text-sm">
                  <button 
                    v-if="auth"
                    @click="startReply(comment.id)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    Reply
                  </button>
                  <span v-if="comment.replies && comment.replies.length > 0" class="text-gray-500">
                    {{ comment.replies.length }} {{ comment.replies.length === 1 ? 'reply' : 'replies' }}
                  </span>
                </div>

                <!-- Reply Form -->
                <div v-if="replyingTo === comment.id" class="mt-4 ml-6 bg-gray-50 rounded-lg p-3">
                  <form @submit.prevent="submitReply(comment.id)">
                    <textarea
                      v-model="replyForms[comment.id].content"
                      placeholder="Write a reply..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                      rows="2"
                      required
                    ></textarea>
                    <div class="flex gap-2 mt-2">
                      <button 
                        type="submit"
                        :disabled="replyForms[comment.id].processing || !replyForms[comment.id].content.trim()"
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 disabled:opacity-50"
                      >
                        {{ replyForms[comment.id].processing ? 'Posting...' : 'Reply' }}
                      </button>
                      <button 
                        type="button"
                        @click="cancelReply"
                        class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600"
                      >
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Replies -->
                <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 ml-6 space-y-3">
                  <div 
                    v-for="reply in comment.replies" 
                    :key="reply.id"
                    class="bg-gray-50 border border-gray-200 rounded-lg p-3"
                  >
                    <div class="flex justify-between items-start">
                      <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                          <Link 
                            :href="`/profile/${reply.user.id}`" 
                            class="font-medium text-blue-600 hover:text-blue-800 hover:underline"
                          >
                            {{ reply.user.name }}
                          </Link>
                          <span class="text-gray-500 text-sm">
                            {{ new Date(reply.created_at).toLocaleDateString() }}
                            {{ new Date(reply.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                          </span>
                          <span v-if="reply.edited_at" class="text-gray-400 text-xs">(edited)</span>
                        </div>
                        
                        <!-- Reply Content or Edit Form -->
                        <div v-if="editingComment === reply.id">
                          <form @submit.prevent="updateComment(reply.id)">
                            <textarea
                              v-model="editCommentForm.content"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              rows="2"
                              required
                            ></textarea>
                            <div class="flex gap-2 mt-2">
                              <button 
                                type="submit"
                                :disabled="editCommentForm.processing || !editCommentForm.content.trim()"
                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 disabled:opacity-50"
                              >
                                {{ editCommentForm.processing ? 'Saving...' : 'Save' }}
                              </button>
                              <button 
                                type="button"
                                @click="cancelEdit"
                                class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600"
                              >
                                Cancel
                              </button>
                            </div>
                          </form>
                        </div>
                        <div v-else>
                          <p class="text-gray-700 whitespace-pre-line">{{ reply.content }}</p>
                        </div>
                      </div>
                      
                      <!-- Reply Actions -->
                      <div v-if="(canEditComment(reply) || canDeleteComment(reply)) && editingComment !== reply.id" class="flex gap-2 ml-4">
                        <button 
                          v-if="canEditComment(reply)"
                          @click="startEdit(reply)"
                          class="text-blue-600 hover:text-blue-800"
                          title="Edit reply"
                        >
                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                          </svg>
                        </button>
                        <button 
                          v-if="canDeleteComment(reply)"
                          @click="deleteComment(reply.id)"
                          class="text-red-600 hover:text-red-800"
                          title="Delete reply"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- No comments message -->
              <div v-if="!post.comments || post.comments.length === 0" class="text-center text-gray-500 py-8">
                No comments yet. Be the first to comment!
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons (if user owns the post) -->
        <div v-if="auth && auth.id === post.user_id" class="mt-8 pt-6 border-t border-gray-200">
          <div class="flex gap-4">
            <Link 
              :href="`/posts/${post.id}/edit`" 
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Edit Post
            </Link>
            <button 
              @click="deletePost"
              class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Delete Post
            </button>
          </div>
        </div>
      </article>

      <!-- Image Modal -->
      <div 
        v-if="showModal" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
        @click="closeModal"
      >
        <div class="max-w-sm max-h-full p-4">
          <img 
            :src="`/storage/${selectedImage}`" 
            :alt="post.title"
            class="max-w-full max-h-full object-contain rounded-lg"
          />
          <button
            @click="closeModal"
            class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300"
          >
            ×
          </button>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div 
        v-if="showDeleteConfirm" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click="cancelDelete"
      >
        <div 
          class="bg-white rounded-lg p-6 max-w-md w-full mx-4"
          @click.stop
        >
          <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5C2.962 18.333 3.924 20 5.464 20z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">Delete Comment</h3>
          </div>
          <p class="text-gray-600 mb-6">
            Are you sure you want to delete this comment? This action cannot be undone.
          </p>
          <div class="flex justify-end gap-3">
            <button 
              @click="cancelDelete"
              class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="confirmDelete"
              class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Toast Container -->
      <div class="fixed top-4 right-4 z-50 space-y-2">
        <Toast
          v-for="toast in toasts"
          :key="toast.id"
          :type="toast.type"
          :title="toast.title"
          :message="toast.message"
          :duration="toast.duration"
          @close="removeToast(toast.id)"
        />
      </div>
    </div>
  </AppLayout>
</template>
