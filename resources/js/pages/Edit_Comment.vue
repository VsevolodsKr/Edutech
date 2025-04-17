<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Rediģēt komentāru
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadComment"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Mēģināt vēlreiz
                </button>
            </div>

            <div v-else-if="comment && content" class="space-y-4">
                <form @submit.prevent="handleSubmit" class="bg-base rounded-lg px-[1rem] py-[.5rem] mb-[2rem]">
                    <TransitionGroup 
                        name="list" 
                        tag="ul" 
                        v-if="validationErrors.length"
                        class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem] overflow-hidden"
                    >
                        <li 
                            v-for="error in validationErrors" 
                            :key="error"
                            class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem] flex items-center gap-2"
                        >
                            <i class="fas fa-exclamation-circle"></i>
                            {{ error }}
                        </li>
                    </TransitionGroup>

                    <div class="relative">
                        <textarea 
                            v-model="commentText"
                            :disabled="isSubmitting"
                            rows="10"
                            maxlength="1000"
                            placeholder="Jūsu komentārs..."
                            class="h-[20rem] resize-none bg-background rounded-lg border-line p-[1rem] text-[1rem] text-text_light w-full my-[.5rem] outline-none hover:outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                            :class="{ 'border-button4': validationErrors.length }"
                        ></textarea>
                        <span class="absolute bottom-4 right-4 text-text_light text-sm">
                            {{ commentText.length }}/1000
                        </span>
                    </div>

                    <div v-if="canEdit" class="flex justify-between mt-4">
                        <router-link 
                            :to="'/watch_video/' + content.id"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] px-[2rem] transition hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:px-[1.5rem]"
                        >
                            Atcelt rediģēšanu
                        </router-link>
                        <button 
                            type="submit"
                            :disabled="isSubmitting || !commentText.trim()"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[2rem] transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:px-[1.5rem]"
                        >
                            {{ isSubmitting ? 'Saglabā...' : 'Saglabāt izmaiņas' }}
                        </button>
                    </div>
                </form>

                <div class="bg-base rounded-lg px-[1rem] py-[.5rem]">
                    <div class="relative">
                        <div v-if="content">
                            <div v-if="content.video_source_type === 'youtube'" class="relative pb-[56.25%] h-0">
                                <iframe 
                                    :src="getYoutubeEmbedUrl(content.video)"
                                    class="absolute top-0 left-0 w-full h-full rounded-lg"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>
                            <video 
                                v-else
                                :src="content.video" 
                                :poster="content.thumb" 
                                controls
                                class="rounded-lg w-full object-contain bg-black"
                            ></video>
                        </div>
                        <div v-else class="w-full h-[400px] bg-gray-800 rounded-lg flex items-center justify-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                        </div>
                    </div>
                </div>
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

const user = ref(null);
const comment = ref(null);
const content = ref(null);
const commentText = ref('');
const validationErrors = ref([]);
const isLoading = ref(true);
const isSubmitting = ref(false);
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const canEdit = computed(() => user.value?.id === comment.value?.user_id);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

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

const loadComment = async () => {
    try {
        isLoading.value = true;
        error.value = null;
        validationErrors.value = [];

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const userData = await loadUser();
        if (!userData) return;

        user.value = userData;
        const response = await axios.get(`/api/comments/find/${route.params.id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.data?.comment) {
            error.value = 'Comment not found';
            return;
        }

        comment.value = response.data.comment;

        const contentData = response.data.content;
        
        const isYouTube = contentData.video?.includes('youtube.com') || contentData.video?.includes('youtu.be');
        
        if (isYouTube) {
            content.value = {
                ...contentData,
                video_source_type: 'youtube',
                video: contentData.video,
                thumb: contentData.thumb
            };
        } else {
            const cleanThumbPath = contentData.thumb
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');
                
            const cleanVideoPath = contentData.video
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');

            content.value = {
                ...contentData,
                video_source_type: 'file',
                video: cleanVideoPath ? `/storage/${cleanVideoPath}` : null,
                thumb: cleanThumbPath ? `/storage/${cleanThumbPath}` : '/storage/default-thumbnail.png'
            };
        }

        commentText.value = comment.value.comment;

        if (userData.id !== comment.value.user_id) {
            router.push(`/watch_video/${content.value.id}`);
        }
    } catch (err) {
        console.error('Error loading comment:', err);
        error.value = 'Failed to load comment. Please try again.';
        if (err.response?.status === 401) {
            router.push('/');
        }
    } finally {
        isLoading.value = false;
    }
};

const handleSubmit = async () => {
    try {
        validationErrors.value = [];
        isSubmitting.value = true;

        const token = localStorage.getItem('token');
        if (!token) {
            validationErrors.value.push('You must be logged in to edit a comment');
            return;
        }

        if (!commentText.value.trim()) {
            validationErrors.value.push('Komentārs nevar būt tukšs');
            return;
        }

        if (commentText.value.length > 1000) {
            validationErrors.value.push('Comment cannot exceed 1000 characters');
            return;
        }

        const formData = new FormData();
        formData.append('comment_id', route.params.id);
        formData.append('comment', commentText.value.trim());

        await axios.post(`/api/comments/${route.params.id}/edit`, formData, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        await Swal.fire({
            title: 'Veiksmīgi!',
            text: 'Jūsu komentārs ir atjaunināts.',
            icon: 'success',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
        });

        router.push(`/watch_video/${content.value.id}`);
    } catch (err) {
        console.error('Error updating comment:', err);
        
        if (err.response?.status === 401) {
            router.push('/');
            return;
        }
        
        if (err.response?.data?.message) {
            validationErrors.value = Array.isArray(err.response.data.message) 
                ? err.response.data.message 
                : [err.response.data.message];
        } else {
            validationErrors.value = ['Failed to update comment. Please try again.'];
        }
    } finally {
        isSubmitting.value = false;
    }
};

const getYoutubeEmbedUrl = (url) => {
    try {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        const videoId = (match && match[2].length === 11) ? match[2] : null;
        
        if (!videoId) {
            console.error('Invalid YouTube URL');
            return '';
        }
        
        return `https://www.youtube.com/embed/${videoId}?autoplay=0&rel=0`;
    } catch (error) {
        console.error('Error processing YouTube URL:', error);
        return '';
    }
};

onMounted(() => {
    loadComment();
});
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>