<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Playlist Details
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
                    @click="loadPlaylistData"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Try Again
                </button>
            </div>

            <!-- Playlist Content -->
            <template v-else-if="playlist && teacher">
                <div class="flex items-center gap-[3rem] flex-wrap bg-base p-[1rem] rounded-lg">
                    <!-- Playlist Thumbnail Section -->
                    <div class="flex-[1_1_20rem]">
                        <div class="mb-[1rem]">
                            <button 
                                v-if="user"
                                @click="toggleBookmark"
                                :disabled="isBookmarkLoading"
                                class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition hover:text-base hover:bg-text_light disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:px-[.5rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                <i :class="[isBookmarked ? 'fa-solid text-button' : 'far', 'fa-bookmark text-[1rem] mr-[.8rem] [@media(max-width:550px)]:text-[.7rem]']"></i>
                                <span class="text-1rem [@media(max-width:550px)]:text-[.7rem]">
                                    {{ isBookmarked ? 'Remove Playlist' : 'Save Playlist' }}
                                </span>
                            </button>
                        </div>
                        <div class="relative">
                            <img 
                                :src="playlist.thumb" 
                                :alt="playlist.title"
                                class="h-[30rem] w-full object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]"
                            >
                            <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-black bg-opacity-30 text-white text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                                {{ contentCount }} videos
                            </span>
                        </div>
                    </div>

                    <!-- Playlist Info Section -->
                    <div class="flex-[1_1_40rem]">
                        <div class="flex items-center gap-[1rem] mb-[1rem]">
                            <img 
                                :src="teacher.image" 
                                :alt="teacher.name"
                                class="h-[5rem] w-[5rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                            >
                            <div>
                                <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ teacher.name }}
                                </h3>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ formatDate(playlist.date) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                                {{ playlist.title }}
                            </h3>
                            <p class="py-[1rem] leading-7 text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ playlist.description }}
                            </p>
                            <router-link 
                                :to="'/teacher_profile/' + teacher.id"
                                class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                            >
                                View Profile
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Videos Section -->
                <div class="pt-[2rem] bg-background [@media(max-width:550px)]:px-[.5rem]">
                    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                        Playlist Videos
                    </h1>
                    <hr class="border-[#ccc] mb-[2rem]">

                    <!-- Videos Grid -->
                    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] pb-[2rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                        <router-link 
                            v-for="content in contents" 
                            :key="content.id"
                            :to="'/watch_video/' + content.id"
                            class="block bg-base rounded-lg p-[1rem] hover:shadow-lg transition-shadow duration-300"
                        >
                            <div class="relative group">
                                <img 
                                    :src="content.thumb" 
                                    :alt="content.title"
                                    class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]"
                                >
                                <div class="absolute inset-0 bg-black bg-opacity-30 rounded-lg flex items-center justify-center text-[3rem] text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark group-hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">
                                {{ content.title }}
                            </h3>
                        </router-link>
                    </div>
                </div>
            </template>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const route = useRoute();
const router = useRouter();
const { width } = useWindowSize();

// State
const playlist = ref(null);
const teacher = ref(null);
const user = ref(null);
const contents = ref([]);
const contentCount = ref(0);
const isBookmarked = ref(false);
const bookmarkId = ref(null);
const isLoading = ref(true);
const isBookmarkLoading = ref(false);
const error = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
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

const loadPlaylistData = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const response = await axios.get(`/api/playlists/find/${route.params.id}`);
        
        if (!response.data?.playlist) {
            throw new Error('Playlist not found');
        }

        // Safely handle playlist thumbnail
        playlist.value = {
            ...response.data.playlist,
            thumb: response.data.playlist?.thumb ? 
                new URL(response.data.playlist.thumb, import.meta.url).href :
                '/storage/app/public/default-thumbnail.png'
        };

        // Safely handle teacher data and image
        if (response.data?.teacher) {
            teacher.value = {
                ...response.data.teacher,
                image: response.data.teacher?.image ?
                    new URL(response.data.teacher.image, import.meta.url).href :
                    '/storage/app/public/default.png'
            };
        } else {
            teacher.value = {
                name: 'Unknown Teacher',
                image: '/storage/app/public/default.png'
            };
        }

        const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.id}/amount`);
        contentCount.value = countResponse.data;

    } catch (err) {
        console.error('Error loading playlist:', err);
        error.value = 'Failed to load playlist. Please try again.';
        playlist.value = null;
        teacher.value = null;
    } finally {
        isLoading.value = false;
    }
};

const loadContents = async () => {
    try {
        if (!playlist.value) return;

        const response = await axios.get(`/api/playlists/${playlist.value.id}/contents`);
        contents.value = response.data.map(content => ({
            ...content,
            thumb: content?.thumb ? 
                new URL(content.thumb, import.meta.url).href :
                '/storage/app/public/default-thumbnail.png'
        }));
    } catch (err) {
        console.error('Error loading contents:', err);
        error.value = 'Failed to load video contents. Please try again.';
        contents.value = [];
    }
};

const checkBookmarkStatus = async () => {
    try {
        if (!user.value || !playlist.value) return;

        const formData = new FormData();
        formData.append('user_id', user.value.id);
        formData.append('playlist_id', playlist.value.id);

        const response = await axios.post('/api/bookmarks/check', formData);
        isBookmarked.value = response.data.status;
        bookmarkId.value = response.data.id;
    } catch (err) {
        console.error('Error checking bookmark status:', err);
    }
};

const toggleBookmark = async () => {
    try {
        isBookmarkLoading.value = true;

        if (isBookmarked.value) {
            await axios.delete(`/api/bookmarks/delete/${bookmarkId.value}`);
            isBookmarked.value = false;
            bookmarkId.value = null;
        } else {
            const formData = new FormData();
            formData.append('user_id', user.value.id);
            formData.append('playlist_id', playlist.value.id);

            await axios.post('/api/bookmarks/add', formData);
            await checkBookmarkStatus();
        }
    } catch (err) {
        console.error('Error toggling bookmark:', err);
    } finally {
        isBookmarkLoading.value = false;
    }
};

// Initialize
onMounted(async () => {
    await Promise.all([
        loadUser(),
        loadPlaylistData()
    ]);

    if (playlist.value) {
        await Promise.all([
            loadContents(),
            user.value && checkBookmarkStatus()
        ]);
    }
});
</script>