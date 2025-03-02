<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Your Comments
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
                You haven't made any comments yet.
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
                        <div class="flex items-center gap-2">
                            <span class="text-text_light">{{ formatDate(comment.date) }}</span>
                            <span class="text-text_dark">{{ comment.content?.title }}</span>
                        </div>
                        <router-link 
                            :to="'/watch_video/' + comment.content?.id"
                            class="text-button hover:text-button1 transition-colors duration-200"
                        >
                            View Content
                        </router-link>
                    </div>

                    <!-- Comment Body -->
                    <div class="relative">
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">
                            {{ comment.comment }}
                        </div>
                        <div class="absolute top-[-0.5rem] left-[1.5rem] w-[1rem] h-[1rem] bg-background transform rotate-45"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-[1rem] mt-[1rem]">
                        <router-link 
                            :to="'/edit_comment/' + comment.id"
                            class="flex-1 max-w-[10rem] bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            Edit Comment
                        </router-link>
                        <button 
                            @click="() => deleteComment(comment.id)"
                            :disabled="isDeleting === comment.id"
                            class="flex-1 max-w-[10rem] bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            {{ isDeleting === comment.id ? 'Deleting...' : 'Delete Comment' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const comments = ref([]);
const user = ref(null);
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

const loadUser = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return null;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        return {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading user:', err);
        router.push('/');
        return null;
    }
};

const loadComments = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const userData = await loadUser();
        if (!userData) return;

        user.value = userData;
        const token = localStorage.getItem('token');
        const headers = { 
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        };

        const response = await axios.get(`/api/comments/user/${userData.id}`, { headers });

        // Transform and combine data
        comments.value = response.data.comments.map((comment, index) => ({
            ...comment,
            content: response.data.contents[index]
        }));
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
            Swal.fire({
                title: 'Error!',
                text: 'You must be logged in to delete a comment',
                icon: 'error',
                color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
            });
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
                text: 'Your comment has been deleted.',
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