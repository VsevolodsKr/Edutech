<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <div v-else-if="!content" class="bg-base rounded-lg p-[1rem] text-center">
                <h3 class="text-[1.5rem] text-text_dark">Content not found</h3>
            </div>

            <div v-else class="bg-base rounded-lg p-[1rem]">
                <!-- Video Player -->
                <div class="relative w-full pt-[56.25%] mb-4">
                    <video
                        :src="content.video"
                        :poster="content.thumb"
                        class="absolute top-0 left-0 w-full h-full object-cover rounded-lg"
                        controls
                        controlsList="nodownload"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Content Info -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl text-text_dark">{{ content.title }}</h2>
                    <div class="flex gap-4">
                        <span class="text-text_light">
                            <i class="fas fa-heart text-button4"></i>
                            {{ content.likes || 0 }} likes
                        </span>
                        <span class="text-text_light">
                            <i class="fas fa-comments text-button"></i>
                            {{ content.commentsCount || 0 }} comments
                        </span>
                    </div>
                </div>

                <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
                    <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-calendar text-button"></i>
                        <span class="text-text_light">{{ content.date }}</span>
                    </p>
                </div>

                <!-- Description -->
                <p class="leading-1.5 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-1">
                    {{ content.description }}
                </p>

                <!-- Action Buttons -->
                <div class="flex justify-between w-full gap-[1rem] my-[1rem]">
                    <button 
                        @click="router.push(`/admin_contents/update/${content.id}`)"
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Update
                    </button>
                    <button 
                        @click="handleDelete(content.id)"
                        class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </section>

        <!-- Comments Section -->
        <section v-if="content" :class="sectionClasses">
            <div class="grid gap-[1rem] bg-base p-[1rem] rounded-lg [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                    User Comments
                </h1>    

                <div v-if="!content.comments?.length" class="text-center text-text_light py-4">
                    No comments yet
                </div>

                <template v-else>
                    <div v-for="comment in content.comments" 
                         :key="comment.id" 
                         class="mb-[5rem] [@media(max-width:550px)]:mb-[3rem]">
                        <!-- Comment Header -->
                        <div class="flex items-center gap-[1rem] mb-[1rem]">
                            <img :src="comment.user_image || defaultAvatar" 
                                 :alt="comment.user_name"
                                 class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                            <div>
                                <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ comment.user_name }}
                                </h3>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ formatDate(comment.created_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-1.5 relative z-0 before:content-none before:absolute before:top-[-1rem] before:left-[1.5rem] before:bg-base before:h-[1rem] before:w-[2rem] before:[clip-path:polygon(50%_0%,_0%_100%,_100%_100%)] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">
                            {{ comment.content }}
                        </div>

                        <!-- Comment Actions -->
                        <div class="flex gap-[1rem] mt-[.5rem]">
                            <button 
                                @click="handleDeleteComment(comment.id)"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]"
                            >
                                Delete Comment
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const route = useRoute();
const { width } = useWindowSize();

// Constants
const defaultAvatar = '/images/default-avatar.png';
const defaultThumb = '/images/default-thumb.png';

// State
const content = ref(null);
const isLoading = ref(true);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const getVideoUrl = (video) => {
    if (!video) return '';
    if (video.startsWith('data:')) return video;
    if (video.startsWith('http')) return video;

    // Clean up the path by removing any leading slashes and 'storage/app/public'
    let cleanPath = video;
    if (cleanPath.includes('storage/app/public/')) {
        cleanPath = cleanPath.replace('storage/app/public/', '');
    }
    if (cleanPath.startsWith('/')) {
        cleanPath = cleanPath.substring(1);
    }

    return `${window.location.origin}/storage/${cleanPath}`;
};

const getThumbUrl = (thumb) => {
    if (!thumb) return defaultThumb;
    if (thumb.startsWith('data:')) return thumb;
    if (thumb.startsWith('http')) return thumb;

    // Clean up the path by removing any leading slashes and 'storage/app/public'
    let cleanPath = thumb;
    if (cleanPath.includes('storage/app/public/')) {
        cleanPath = cleanPath.replace('storage/app/public/', '');
    }
    if (cleanPath.startsWith('/')) {
        cleanPath = cleanPath.substring(1);
    }

    return `${window.location.origin}/storage/${cleanPath}`;
};

const loadContent = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        // Load content details with authorization header
        const response = await axios.get(`/api/contents/find/${route.params.id}`, {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.data.content) {
            // Log the response for debugging
            console.log('Content response:', response.data.content);

            // Process video and thumbnail URLs
            const processedContent = {
                ...response.data.content,
                video: getVideoUrl(response.data.content.video),
                thumb: getThumbUrl(response.data.content.thumb)
            };

            // Log the processed URLs for debugging
            console.log('Processed video URL:', processedContent.video);
            console.log('Processed thumbnail URL:', processedContent.thumb);

            // Get comments if they exist
            if (response.data.comments) {
                processedContent.comments = response.data.comments.map(comment => ({
                    ...comment,
                    user_image: comment.user_image ? getThumbUrl(comment.user_image) : defaultAvatar
                }));
                processedContent.commentsCount = response.data.comments.length;
            } else {
                processedContent.comments = [];
                processedContent.commentsCount = 0;
            }

            // Get likes count if it exists
            processedContent.likes = response.data.likes_count || 0;

            content.value = processedContent;
        }
    } catch (error) {
        console.error('Error loading content:', error);
        if (error.response?.status === 401) {
            router.push('/');
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load content',
                icon: 'error'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

const handleDelete = async (contentId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

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
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/contents/delete/${contentId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your content has been deleted.',
                icon: 'success'
            });

            router.push('/admin_contents');
        }
    } catch (error) {
        console.error('Error deleting content:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete content',
            icon: 'error'
        });
    }
};

const handleDeleteComment = async (commentId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Comment will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/comments/delete/${commentId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Comment has been deleted.',
                icon: 'success'
            });

            // Reload content to refresh comments
            await loadContent();
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete comment',
            icon: 'error'
        });
    }
};

// Lifecycle
onMounted(() => {
    loadContent();
});
</script>