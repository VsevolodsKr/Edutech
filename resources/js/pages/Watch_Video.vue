<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <!-- Video Player Section -->
            <div class="bg-base rounded-lg p-[1rem]">
                <div class="relative mb-[1rem]">
                    <video 
                        v-if="content"
                        :src="content.video" 
                        :poster="content.thumb" 
                        controls
                        class="rounded-lg w-full object-contain bg-black"
                    ></video>
                    <div v-else class="w-full h-[400px] bg-gray-800 rounded-lg flex items-center justify-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                    </div>
                </div>

                <template v-if="content && teacher">
                    <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                        {{ content.title }}
                    </h3>
                    
                    <!-- Video Stats -->
                    <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
                        <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                            <i class="fas fa-calendar text-button"></i> 
                            <span class="text-text_light">{{ formatDate(content.date) }}</span>
                        </p>
                        <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                            <i class="fas fa-heart text-button"></i> 
                            <span class="text-text_light">{{ likesCount }} likes</span>
                        </p>
                    </div>

                    <!-- Teacher Info -->
                    <div class="flex items-center gap-[1rem] mb-[1rem]">
                        <img 
                            :src="teacher.image" 
                            :alt="teacher.name"
                            class="rounded-full h-[5rem] w-[5rem] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                        >
                        <div>
                            <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                {{ teacher.name }}
                            </h3>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ teacher.profession }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-[1rem]">
                        <router-link 
                            :to="'/playlist/' + content.playlist_id"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                        >
                            View Playlist
                        </router-link>
                        
                        <button 
                            v-if="user"
                            @click="toggleLike"
                            :disabled="isLikeLoading"
                            class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition hover:text-base hover:bg-text_light disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                        >
                            <i :class="[isLiked ? 'fa-solid' : 'far', 'fa-heart', isLiked ? 'text-button' : '']"></i>
                            <span class="ml-2">{{ isLiked ? 'Unlike' : 'Like' }}</span>
                        </button>
                    </div>

                    <!-- Description -->
                    <p class="leading-7 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem]">
                        {{ content.description }}
                    </p>
                </template>
            </div>
        </section>

        <!-- Comments Section -->
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                {{ commentsCount }} Comments
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <!-- Add Comment Form -->
            <form 
                v-if="user" 
                @submit.prevent="handleAddComment"
                class="bg-base rounded-lg p-[1rem] mb-[1rem]"
            >
                <h3 class="text-[1.5rem] text-text_dark mb-[.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Add Comment
                </h3>

                <!-- Error Messages -->
                <TransitionGroup 
                    name="list" 
                    tag="ul" 
                    class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]"
                >
                    <li 
                        v-for="error in errors" 
                        :key="error"
                        class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]"
                    >
                        <i class="fa fa-warning"></i> {{ error }}
                    </li>
                </TransitionGroup>

                <!-- Comment Input -->
                <textarea 
                    v-model="newComment"
                    placeholder="Enter your comment..."
                    maxlength="1000"
                    class="h-[20rem] resize-none bg-background rounded-lg border-line p-[1rem] text-[1rem] text-text_light w-full my-[.5rem] outline-none hover:outline-none [@media(max-width:550px)]:text-[.7rem]"
                    :disabled="isCommentLoading"
                ></textarea>

                <button 
                    type="submit"
                    :disabled="isCommentLoading || !newComment.trim()"
                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                >
                    {{ isCommentLoading ? 'Adding...' : 'Add Comment' }}
                </button>
            </form>

            <!-- Comments List -->
            <div class="grid gap-[1rem] bg-base p-[1rem] rounded-lg [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                    User Comments
                </h1>

                <TransitionGroup name="list">
                    <div 
                        v-for="comment in comments" 
                        :key="comment.id"
                        class="mb-8 last:mb-0"
                    >
                        <!-- Comment Author -->
                        <div class="flex items-center gap-[1rem] mb-[1rem]">
                            <img 
                                :src="comment.user.image" 
                                :alt="comment.user.name"
                                class="h-[5rem] w-[5rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                            >
                            <div>
                                <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ comment.user.name }}
                                </h3>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ formatDate(comment.date) }}
                                </span>
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 relative [@media(max-width:550px)]:text-[.7rem]">
                            {{ comment.comment }}
                        </div>

                        <!-- Comment Actions -->
                        <div v-if="user?.id === comment.user.id" class="flex gap-[1rem] mt-[.5rem]">
                            <router-link 
                                :to="'/edit_comment/' + comment.id"
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem]"
                            >
                                Edit Comment
                            </router-link>
                            <button 
                                @click="handleDeleteComment(comment.id)"
                                :disabled="isDeletingComment === comment.id"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                            >
                                {{ isDeletingComment === comment.id ? 'Deleting...' : 'Delete Comment' }}
                            </button>
                        </div>
                    </div>
                </TransitionGroup>
            </div>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const route = useRoute();
const router = useRouter();
const { width } = useWindowSize();

// State
const content = ref(null);
const teacher = ref(null);
const user = ref(null);
const comments = ref([]);
const newComment = ref('');
const errors = ref([]);
const isLiked = ref(false);
const likeId = ref(null);
const likesCount = ref(0);
const commentsCount = ref(0);
const isLoading = ref(true);
const isLikeLoading = ref(false);
const isCommentLoading = ref(false);
const isDeletingComment = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const loadContent = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/api/contents/find/${route.params.id}`);
        
        content.value = {
            ...response.data.content,
            thumb: new URL(response.data.content.thumb, import.meta.url),
            video: new URL(response.data.content.video, import.meta.url)
        };
        
        teacher.value = {
            ...response.data.teacher,
            image: new URL(response.data.teacher.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading content:', err);
        router.push('/');
    } finally {
        isLoading.value = false;
    }
};

const loadUser = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        user.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading user:', err);
        router.push('/');
    }
};

const checkLikeStatus = async () => {
    try {
        if (!user.value || !teacher.value) return;

        const formData = new FormData();
        formData.append('user_id', user.value.id);
        formData.append('teacher_id', teacher.value.id);
        formData.append('content_id', route.params.id);

        const response = await axios.post('/api/likes/check', formData);
        isLiked.value = response.data.status;
        likeId.value = response.data.id;
    } catch (err) {
        console.error('Error checking like status:', err);
    }
};

const loadLikesCount = async () => {
    try {
        const response = await axios.get(`/api/likes/count_content/${route.params.id}`);
        likesCount.value = response.data;
    } catch (err) {
        console.error('Error loading likes count:', err);
    }
};

const toggleLike = async () => {
    try {
        isLikeLoading.value = true;

        if (isLiked.value) {
            await axios.delete(`/api/likes/delete/${likeId.value}`);
            isLiked.value = false;
            likesCount.value--;
        } else {
            const formData = new FormData();
            formData.append('user_id', user.value.id);
            formData.append('teacher_id', teacher.value.id);
            formData.append('content_id', route.params.id);

            await axios.post('/api/likes/add', formData);
            isLiked.value = true;
            likesCount.value++;
            await checkLikeStatus(); // Get the new like ID
        }
    } catch (err) {
        console.error('Error toggling like:', err);
    } finally {
        isLikeLoading.value = false;
    }
};

const loadComments = async () => {
    try {
        const response = await axios.get(`/api/comments/video/${route.params.id}`);
        comments.value = response.data.comments.map((comment, index) => ({
            ...comment,
            user: {
                ...response.data.users[index],
                image: new URL(response.data.users[index].image, import.meta.url)
            }
        }));
    } catch (err) {
        console.error('Error loading comments:', err);
    }
};

const loadCommentsCount = async () => {
    try {
        const response = await axios.get(`/api/comments/content_amount/${route.params.id}`);
        commentsCount.value = response.data;
    } catch (err) {
        console.error('Error loading comments count:', err);
    }
};

const handleAddComment = async () => {
    try {
        isCommentLoading.value = true;
        errors.value = [];

        const formData = new FormData();
        formData.append('comment', newComment.value.trim());
        formData.append('content_id', route.params.id);
        formData.append('user_id', user.value.id);
        formData.append('teacher_id', teacher.value.id);

        await axios.post('/api/comments/add', formData);

        await Swal.fire({
            title: 'Comment Added!',
            text: 'Thank you for sharing your thoughts!',
            icon: 'success',
        });

        newComment.value = '';
        await Promise.all([
            loadComments(),
            loadCommentsCount()
        ]);
    } catch (err) {
        console.error('Error adding comment:', err);
        errors.value = Array.isArray(err.response?.data?.message) 
            ? err.response.data.message 
            : [err.response?.data?.message || 'Failed to add comment'];
    } finally {
        isCommentLoading.value = false;
    }
};

const handleDeleteComment = async (commentId) => {
    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--button4'),
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            isDeletingComment.value = commentId;
            await axios.delete(`/api/comments/delete/${commentId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your comment has been deleted.',
                icon: 'success'
            });

            await Promise.all([
                loadComments(),
                loadCommentsCount()
            ]);
        }
    } catch (err) {
        console.error('Error deleting comment:', err);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete comment',
            icon: 'error'
        });
    } finally {
        isDeletingComment.value = null;
    }
};

// Initialize
onMounted(async () => {
    await Promise.all([
        loadContent(),
        loadUser()
    ]);

    if (content.value && user.value) {
        await Promise.all([
            checkLikeStatus(),
            loadLikesCount(),
            loadComments(),
            loadCommentsCount()
        ]);
    }
});
</script>

<style>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>