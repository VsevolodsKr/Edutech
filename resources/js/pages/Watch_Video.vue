<template>
    <div>
<Header />
        <section :class="sectionClasses">
    <div class="bg-base rounded-lg p-[1rem]">
        <div class="relative mb-[1rem]">
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

                <template v-if="content && teacher">
                    <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                        {{ content.title }}
                    </h3>
        <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
                        <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                            <i class="fas fa-calendar text-button mr-[.2rem]"></i> 
                            <span class="text-text_light">{{ formatDate(content.date) }}</span>
                        </p>
                        <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                            <i class="fas fa-heart text-button mr-[.2rem]"></i> 
                            <span class="text-text_light">{{ likesCount }}</span>
                        </p>
        </div>

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

                    <div class="flex items-center justify-between gap-[1rem]">
                        <router-link 
                            :to="'/playlist/' + content.encrypted_playlist_id"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                        >
                            Skatīt kursu
                        </router-link>
                        
                        <button 
                            v-if="user"
                            @click="toggleLike"
                            :disabled="isLikeLoading"
                            class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition hover:text-base hover:bg-text_light disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                        >
                            <i :class="[isLiked ? 'fa-solid' : 'far', 'fa-heart', isLiked ? 'text-button' : '']"></i>
                            <span class="ml-2">{{ isLiked ? 'Atzīmēt' : 'Iezīmēt' }}</span>
                        </button>
            </div>

                    <p class="leading-7 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem]">
                        {{ content.description }}
                    </p>
                </template>
    </div>
</section>

        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                {{ commentsCount }} komentāri
            </h1>
    <hr class="border-[#ccc] mb-[2rem]">

            <form 
                v-if="user" 
                @submit.prevent="handleAddComment"
                class="bg-base rounded-lg p-[1rem] mb-[1rem]"
            >
                <h3 class="text-[1.5rem] text-text_dark mb-[.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Pievienot komentāru
                </h3>

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

                <textarea 
                    v-model="newComment"
                    placeholder="Ievadiet savu komentāru..."
                    maxlength="1000"
                    class="h-[20rem] resize-none bg-background rounded-lg border-line p-[1rem] text-[1rem] text-text_light w-full my-[.5rem] outline-none hover:outline-none [@media(max-width:550px)]:text-[.7rem]"
                    :disabled="isCommentLoading"
                ></textarea>

                <button 
                    type="submit"
                    :disabled="isCommentLoading || !newComment.trim()"
                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                >
                    {{ isCommentLoading ? 'Pievienošana...' : 'Pievienot komentāru' }}
                </button>
    </form>

    <div class="grid gap-[1rem] bg-base p-[1rem] rounded-lg [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                <h1 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                    Lietotāju komentāri
                </h1>

                <TransitionGroup name="list">
                    <div 
                        v-for="comment in comments" 
                        :key="comment.id"
                        class="mb-8 last:mb-0"
                    >
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

                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 relative [@media(max-width:550px)]:text-[.7rem]">
                            {{ comment.comment }}
                </div>

                        <div v-if="user?.id === comment.user.id" class="flex gap-[1rem] mt-[.5rem]">
                            <router-link 
                                :to="'/edit_comment/' + comment.encrypted_id"
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem]"
                            >
                                Rediģēt
                            </router-link>
                            <button 
                                @click="handleDeleteComment(comment.id)"
                                :disabled="isDeletingComment === comment.id"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem]"
                            >
                                {{ isDeletingComment === comment.id ? 'Dzēšana...' : 'Dzēst' }}
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
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
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

const loadContent = async () => {
    try {
        const response = await axios.get(`/api/contents/find/${route.params.id}`);
        if (response.data.status === 404) {
            router.push('/404');
            return;
        }

        // Extract content and teacher data from response
        const contentData = response.data.content;
        const teacherData = response.data.teacher;

        // Set content data
        content.value = {
            ...contentData,
            thumb: contentData.thumb ? contentData.thumb.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '').replace(/^\//, '') : null,
            video: contentData.video ? contentData.video.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '').replace(/^\//, '') : null,
            encrypted_playlist_id: contentData.encrypted_playlist_id,
            encrypted_id: contentData.encrypted_id
        };

        // Set teacher data
        if (teacherData) {
            const cleanImagePath = teacherData.image
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');

            teacher.value = {
                ...teacherData,
                image: cleanImagePath ? `/storage/${cleanImagePath}` : '/storage/default-avatar.png'
            };
        }

        // Load comments and counts
        await loadComments();
        await Promise.all([
            loadLikesCount(),
            loadCommentsCount()
        ]).catch(console.error);
    } catch (error) {
        console.error('Error loading content:', error);
        router.push('/404');
    }
};

const loadComments = async () => {
    try {
        if (!content.value?.encrypted_id) {
            console.error('Content not loaded yet');
            return;
        }

        console.log('Loading comments for content:', content.value.encrypted_id);
        
        const [commentsResponse, countResponse] = await Promise.all([
            axios.get(`/api/comments/video/${content.value.encrypted_id}`),
            axios.get(`/api/comments/content_amount/${content.value.encrypted_id}`)
        ]);

        console.log('Comments response:', commentsResponse.data);
        console.log('Count response:', countResponse.data);

        if (commentsResponse.data.status === 200 && Array.isArray(commentsResponse.data.comments)) {
            comments.value = commentsResponse.data.comments.map(comment => {
                if (!comment.user) {
                    console.warn('Comment without user:', comment);
                    return {
                        ...comment,
                        user: {
                            id: 0,
                            name: 'Unknown User',
                            image: '/storage/default-avatar.png'
                        }
                    };
                }

                const cleanImagePath = comment.user.image
                    ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                    ?.replace(/^\//, '');

                return {
                    ...comment,
                    user: {
                        ...comment.user,
                        image: cleanImagePath ? `/storage/${cleanImagePath}` : '/storage/default-avatar.png'
                    }
                };
            });
        } else {
            console.warn('Invalid comments response:', commentsResponse.data);
            comments.value = [];
        }

        commentsCount.value = typeof countResponse.data === 'number' ? countResponse.data : 0;
    } catch (error) {
        console.error('Error loading comments:', error);
        if (error.response) {
            console.error('Response data:', error.response.data);
            console.error('Response status:', error.response.status);
        }
        comments.value = [];
        commentsCount.value = 0;
    }
};

const loadUser = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            user.value = null;
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        const cleanUserImagePath = response.data.image
            ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
            ?.replace(/^\//, '');

        user.value = {
            ...response.data,
            image: cleanUserImagePath ? `/storage/${cleanUserImagePath}` : '/storage/default-avatar.png'
        };
    } catch (err) {
        console.error('Error loading user:', err);
        user.value = null;
    }
};

const checkLikeStatus = async () => {
    try {
        if (!user.value || !teacher.value) return;

        const token = localStorage.getItem('token');
        const formData = new FormData();
        formData.append('user_id', user.value.id);
        formData.append('teacher_id', teacher.value.id);
        formData.append('content_id', route.params.id);

        const response = await axios.post('/api/likes/check', formData, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.data.status === true && response.data.id) {
            isLiked.value = true;
            likeId.value = response.data.id;
        } else {
            isLiked.value = false;
            likeId.value = null;
        }
    } catch (err) {
        console.error('Error checking like status:', err);
        isLiked.value = false;
        likeId.value = null;
    }
};

const loadLikesCount = async () => {
    try {
        const response = await axios.get(`/api/likes/count_content/${route.params.id}`);
        likesCount.value = response.data;
    } catch (err) {
        console.error('Error loading likes count:', err);
        likesCount.value = 0;
    }
};

const loadCommentsCount = async () => {
    try {
        const response = await axios.get(`/api/comments/content_amount/${route.params.id}`);
        commentsCount.value = response.data;
    } catch (err) {
        console.error('Error loading comments count:', err);
        commentsCount.value = 0;
    }
};

const handleAddComment = async () => {
    try {
        if (!user.value) {
            router.push('/login');
            return;
        }

        isCommentLoading.value = true;
        errors.value = [];

        const token = localStorage.getItem('token');
        const formData = new FormData();
        formData.append('comment', newComment.value.trim());
        formData.append('content_id', route.params.id);
        formData.append('user_id', user.value.id);
        formData.append('teacher_id', teacher.value.id);

        await axios.post('/api/comments/add', formData, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        await Swal.fire({
            title: 'Komentārs pievienots!',
            text: 'Paldies par dalīšanos ar savu domu!',
            icon: 'success',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
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
        if (!user.value) {
            router.push('/login');
            return;
        }

        const token = localStorage.getItem('token');
        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Šī darbība nav atsaukama.',
            icon: 'warning',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--button4'),
            confirmButtonText: 'Jā, dzēst!',
            cancelButtonText: 'Atcelt'
        });

        if (result.isConfirmed) {
            isDeletingComment.value = commentId;
            await axios.delete(`/api/comments/delete/${commentId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Jūsu komentārs ir dzēsts.',
                icon: 'success',
                color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
            });

            await Promise.all([
                loadComments(),
                loadCommentsCount()
            ]);
        }
    } catch (err) {
        console.error('Error deleting comment:', err);
        if (err.response?.status === 401) {
            router.push('/login');
            return;
        }
                    Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst komentāru',
            icon: 'error',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
            background: getComputedStyle(document.documentElement).getPropertyValue('--background'),
        });
    } finally {
        isDeletingComment.value = null;
    }
};

const toggleLike = async () => {
    try {
        if (!user.value) {
            router.push('/login');
            return;
        }

        isLikeLoading.value = true;
        const token = localStorage.getItem('token');

        if (isLiked.value && likeId.value) {
            try {
                await axios.delete(`/api/likes/delete/${likeId.value}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                isLiked.value = false;
                likeId.value = null;
                likesCount.value--;
            } catch (error) {
                console.error('Error deleting like:', error);
                if (error.response?.status === 404) {
                    // If the like wasn't found, reset the state
                    isLiked.value = false;
                    likeId.value = null;
                } else {
                    throw error; // Re-throw other errors
                }
            }
        } else {
            const formData = new FormData();
            formData.append('user_id', user.value.id);
            formData.append('teacher_id', teacher.value.id);
            formData.append('content_id', route.params.id);

            await axios.post('/api/likes/add', formData, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            await checkLikeStatus(); // This will set isLiked and likeId
            likesCount.value++;
        }
    } catch (err) {
        console.error('Error toggling like:', err);
        if (err.response?.status === 401) {
            router.push('/login');
        }
    } finally {
        isLikeLoading.value = false;
    }
};

onMounted(async () => {
    try {
        await loadUser();
        await loadContent();
        if (user.value) {
            await checkLikeStatus();
        }
    } catch (err) {
        console.error('Error in onMounted:', err);
        error.value = 'Failed to load content. Please try again.';
    } finally {
        isLoading.value = false;
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