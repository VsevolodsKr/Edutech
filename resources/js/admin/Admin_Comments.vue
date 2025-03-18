<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Comments on Your Content
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadComments"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Try Again
                </button>
            </div>

            <!-- No Comments -->
            <div v-else-if="!comments.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                No comments on your content yet.
            </div>

            <!-- Comments List -->
            <div v-else class="flex flex-col gap-[1rem] justify-start items-start pr-[1rem] [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="comment in comments" 
                    :key="comment.id" 
                    class="bg-base rounded-lg p-[2rem] w-full hover:shadow-lg transition-shadow duration-300"
                >
                    <!-- Comment Header -->
                    <div class="flex items-center justify-between text-[1.2rem] [@media(max-width:550px)]:text-[1rem] mb-[1rem]">
                        <div class="flex items-center gap-4">
                            <!-- User Avatar -->
                            <img 
                                :src="comment.user?.image || '/storage/default-avatar.png'" 
                                :alt="comment.user?.name"
                                class="h-[3rem] w-[3rem] rounded-full object-cover [@media(max-width:550px)]:h-[2rem] [@media(max-width:550px)]:w-[2rem]"
                            >
                            <div class="flex flex-col">
                                <span class="text-text_dark font-medium">{{ comment.user?.name }}</span>
                                <span class="text-text_light text-sm">{{ formatDate(comment.date) }}</span>
                            </div>
                        </div>
                        <router-link 
                            :to="'/admin_watch_content/' + comment.content?.id"
                            class="text-button hover:text-button1 transition-colors duration-200"
                        >
                            View Content
                        </router-link>
                    </div>

                    <!-- Content Title -->
                    <div class="mb-[1rem] text-text_dark">
                        <span class="font-medium">Content:</span> {{ comment.content?.title }}
                    </div>

                    <!-- Comment Body -->
                    <div class="relative">
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">
                            {{ comment.comment }}
                        </div>
                        <div class="absolute top-[-0.5rem] left-[1.5rem] w-[1rem] h-[1rem] bg-background transform rotate-45"></div>
                    </div>

                    <!-- Action Button -->
                    <div class="flex justify-start mt-[1rem]">
                        <button 
                            @click="() => deleteComment(comment.id)"
                            :disabled="isDeleting === comment.id"
                            class="max-w-[12rem] bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] px-[2rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            {{ isDeleting === comment.id ? 'Deleting...' : 'Delete Comment' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const comments = ref([]);
const teacherData = ref(null);
const isLoading = ref(true);
const isDeleting = ref(null);
const error = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const loadTeacher = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return null;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        return response.data;
    } catch (err) {
        console.error('Error loading teacher:', err);
        router.push('/');
        return null;
    }
};

const loadComments = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const teacher = await loadTeacher();
        if (!teacher) return;

        teacherData.value = teacher;
        const token = localStorage.getItem('token');
        const headers = { 
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        };

        // Get all comments where teacher_id matches
        const response = await axios.get(`/api/comments/teacher/${teacher.id}`, { headers });

        // Process comments to include user and content information
        comments.value = response.data.comments.map((comment, index) => {
            const user = response.data.users[index];
            // Clean up user image path
            let userImage = user?.image;
            if (userImage) {
                userImage = userImage
                    .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                    .replace(/^\//, '');
                userImage = `/storage/${userImage}`;
            }

            return {
                ...comment,
                user: {
                    ...user,
                    image: userImage || '/storage/default-avatar.png'
                },
                content: response.data.contents[index]
            };
        });

        // Sort comments by date (newest first)
        comments.value.sort((a, b) => new Date(b.date) - new Date(a.date));

    } catch (err) {
        console.error('Error loading comments:', err);
        error.value = 'Failed to load comments. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

const deleteComment = async (commentId) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        // Get computed styles for SweetAlert
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        // Show confirmation dialog
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            isDeleting.value = commentId;

            await axios.delete(`/api/comments/delete/${commentId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            // Remove the comment from the list
            comments.value = comments.value.filter(comment => comment.id !== commentId);

            await Swal.fire({
                title: 'Deleted!',
                text: 'The comment has been deleted.',
                icon: 'success',
                color: text_dark,
                background: background,
            });
        }
    } catch (err) {
        console.error('Error deleting comment:', err);
        if (err.response?.status === 401) {
            router.push('/');
            return;
        }
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete the comment. Please try again.',
            icon: 'error',
            color: text_dark,
            background: background,
        });
    } finally {
        isDeleting.value = null;
    }
};

// Initialize
onMounted(() => {
    loadComments();
});
</script> 