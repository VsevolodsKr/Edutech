<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
            </div>

            <div v-else-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg">
                <p class="[@media(max-width:550px)]:text-[.7rem]">
                    <i class="fa fa-warning"></i> {{ error }}
                </p>
            </div>

            <div v-else>
                <!-- Content Section -->
                <div class="bg-base rounded-lg p-[1rem] shadow-lg transition-all duration-300 hover:shadow-xl">
                    <!-- Video Player -->
                    <div class="relative mb-[1rem] group">
                        <video 
                            v-if="content" 
                            :src="content.video" 
                            controls 
                            :poster="content.thumb" 
                            class="rounded-lg w-full object-contain bg-[#000] transition-transform duration-300 group-hover:scale-[1.01]"
                        ></video>
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-lg"></div>
                    </div>

                    <!-- Content Details -->
                    <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem] font-semibold">
                        {{ content?.title }}
                    </h3>

                    <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
                        <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem] flex items-center gap-2">
                            <i class="fas fa-calendar text-button"></i> 
                            <span class="text-text_light">{{ formatDate(content?.date) }}</span>
                        </p>
                    </div>

                    <!-- Engagement Stats -->
                    <div class="flex items-center justify-between gap-[1rem]">
                        <p class="bg-background px-[1rem] py-[.5rem] text-[1rem] rounded-lg [@media(max-width:550px)]:text-[.7rem] flex items-center gap-2 transition-all duration-300 hover:bg-opacity-80">
                            <i class="fas fa-thumbs-up text-button"></i> 
                            <span class="text-text_light">{{ content?.likes || 0 }}</span>
                        </p>
                        <p class="bg-background px-[1rem] py-[.5rem] text-[1rem] rounded-lg [@media(max-width:550px)]:text-[.7rem] flex items-center gap-2 transition-all duration-300 hover:bg-opacity-80">
                            <i class="fas fa-comment text-button"></i> 
                            <span class="text-text_light">{{ content?.comments?.length || 0 }}</span>
                        </p>
                    </div>

                    <!-- Description -->
                    <p class="leading-1.5 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-1">
                        {{ content?.description }}
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex justify-between w-full gap-[1rem] my-[1rem]">
                        <button 
                            @click="handleUpdate"
                            :disabled="isDeleting"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition-all duration-300 hover:bg-transparent hover:text-button2 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] focus:outline-none focus:ring-2 focus:ring-button2 focus:ring-opacity-50"
                        >
                            Update
                        </button>
                        <button 
                            @click="handleDelete"
                            :disabled="isDeleting"
                            class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition-all duration-300 hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] focus:outline-none focus:ring-2 focus:ring-button4 focus:ring-opacity-50"
                        >
                            <span v-if="isDeleting" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                            <span v-else>Delete</span>
                        </button>
                    </div>
                </div>

                <!-- Comments Section -->
                <section class="mt-8">
                    <div class="grid gap-[1rem] bg-base p-[1rem] rounded-lg [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col shadow-lg">
                        <h2 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem] font-semibold">
                            Comments ({{ content?.comments?.length || 0 }})
                        </h2>

                        <TransitionGroup 
                            name="comment-list" 
                            tag="div" 
                            class="space-y-6"
                        >
                            <div 
                                v-for="comment in content?.comments" 
                                :key="comment.id"
                                class="comment-item transition-all duration-300 hover:translate-x-2"
                            >
                                <!-- Comment Header -->
                                <div class="flex items-center gap-[1rem] mb-[1rem]">
                                    <img 
                                        :src="comment.user_image || '../images/default-avatar.png'" 
                                        :alt="comment.username"
                                        class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem] transition-transform duration-300 hover:scale-105"
                                    >
                                    <div>
                                        <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem] font-medium">
                                            {{ comment.username }}
                                        </h3>
                                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                            {{ formatDate(comment.created_at) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Comment Content -->
                                <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-1.5 relative z-0 before:content-none before:absolute before:top-[-1rem] before:left-[1.5rem] before:bg-base before:h-[1rem] before:w-[2rem] before:[clip-path:polygon(50%_0%,_0%_100%,_100%_100%)] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem] transition-all duration-300 hover:bg-opacity-80">
                                    {{ comment.content }}
                                </div>

                                <!-- Comment Actions -->
                                <div class="flex gap-[1rem] mt-[.5rem]">
                                    <button 
                                        @click="() => handleDeleteComment(comment.id)"
                                        :disabled="isDeletingComment === comment.id"
                                        class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-[10rem] transition-all duration-300 hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem] focus:outline-none focus:ring-2 focus:ring-button4 focus:ring-opacity-50"
                                    >
                                        <span v-if="isDeletingComment === comment.id" class="inline-flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Deleting...
                                        </span>
                                        <span v-else>Delete Comment</span>
                                    </button>
                                </div>
                            </div>
                        </TransitionGroup>

                        <div v-if="!content?.comments?.length" class="text-center text-text_light py-4">
                            No comments yet
                        </div>
                    </div>
                </section>
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useWindowSize } from '@vueuse/core'
import Swal from 'sweetalert2'
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'
import store from '../store/store'

const router = useRouter()
const route = useRoute()
const { width } = useWindowSize()

// State
const content = ref(null)
const error = ref(null)
const isLoading = ref(true)
const isDeleting = ref(false)
const isDeletingComment = ref(null)

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
])

// Methods
const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

const fetchContent = async () => {
    try {
        isLoading.value = true
        error.value = null
        
        const response = await axios.get(`/api/contents/find/${route.params.id}`)
        content.value = response.data.content
        
        if (content.value) {
            content.value.thumb = new URL(content.value.thumb, import.meta.url)
            content.value.video = new URL(content.value.video, import.meta.url)
        }
    } catch (err) {
        console.error('Error fetching content:', err)
        error.value = 'Failed to load content. Please try again.'
    } finally {
        isLoading.value = false
    }
}

const handleUpdate = () => {
    router.push(`/admin_contents/update/${content.value.id}`)
}

const handleDelete = async () => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')

    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Content will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        })

        if (result.isConfirmed) {
            isDeleting.value = true
            await axios.delete(`/api/contents/delete/${content.value.id}`)
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Content has been deleted successfully.',
                icon: 'success'
            })

            router.push('/admin_contents')
        }
    } catch (err) {
        console.error('Error deleting content:', err)
        await Swal.fire({
            title: 'Error',
            text: 'Failed to delete content. Please try again.',
            icon: 'error'
        })
    } finally {
        isDeleting.value = false
    }
}

const handleDeleteComment = async (commentId) => {
    try {
        const result = await Swal.fire({
            title: 'Delete Comment?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        })

        if (result.isConfirmed) {
            isDeletingComment.value = commentId
            await axios.delete(`/api/comments/delete/${commentId}`)
            
            // Refresh content to get updated comments
            await fetchContent()

            await Swal.fire({
                title: 'Success',
                text: 'Comment deleted successfully',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            })
        }
    } catch (err) {
        console.error('Error deleting comment:', err)
        await Swal.fire({
            title: 'Error',
            text: 'Failed to delete comment. Please try again.',
            icon: 'error'
        })
    } finally {
        isDeletingComment.value = null
    }
}

// Lifecycle
onMounted(async () => {
    if (!localStorage.getItem('token')) {
        router.push('/')
        return
    }
    await fetchContent()
})
</script>

<style scoped>
.comment-list-move,
.comment-list-enter-active,
.comment-list-leave-active {
    transition: all 0.5s ease;
}

.comment-list-enter-from,
.comment-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.comment-list-leave-active {
    position: absolute;
}

.comment-item {
    transition: all 0.3s ease;
}

.comment-item:hover {
    transform: translateX(5px);
}
</style>