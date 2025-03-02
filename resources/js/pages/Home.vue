<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Dashboard</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
                    <p class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ error }}
                    </p>
                </div>

                <!-- Content -->
                <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <!-- User Profile -->
                        <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                            <img 
                                :src="userData?.image" 
                                :alt="userData?.name"
                                class="h-[4rem] w-[4rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                            >
                            <div>
                                <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">
                                    {{ userData?.name }}
                                </h3>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ userData?.profession || 'Student' }}
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="grid grid-cols-3 gap-[1.5rem] mb-[2rem]">
                            <div 
                                v-for="(stat, index) in statistics" 
                                :key="index"
                                class="bg-background rounded-lg p-[1rem] text-center hover:shadow-md transition-shadow duration-300 relative group"
                            >
                                <h3 class="text-[1.5rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1.2rem]">
                                    {{ stat.value }}
                                </h3>
                                <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] mb-2">
                                    {{ stat.label }}
                                </p>
                                <router-link 
                                    v-if="stat.link"
                                    :to="stat.link"
                                    class="text-button text-sm hover:text-text_dark transition-colors duration-200 opacity-0 group-hover:opacity-100 absolute bottom-2 left-1/2 transform -translate-x-1/2"
                                >
                                    View All
                                </router-link>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-[1rem]">
                            <button 
                                @click="$router.push('/update-profile')" 
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Update Profile
                            </button>
                            <button 
                                @click="handleLogout" 
                                :disabled="isLoggingOut"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                <span v-if="isLoggingOut" class="flex items-center justify-center gap-2">
                                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-base"></div>
                                    Logging out...
                                </span>
                                <span v-else>Logout</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Latest Playlists Section -->
                <div class="mt-8">
                    <h2 class="text-[1.5rem] text-text_dark mb-4 [@media(max-width:550px)]:text-[1.2rem]">Latest Courses</h2>
                    <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                    <!-- Loading State -->
                    <div v-if="isPlaylistsLoading" class="flex justify-center items-center min-h-[20vh]">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="playlistsError" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                        {{ playlistsError }}
                        <button 
                            @click="loadLatestPlaylists" 
                            class="block mx-auto mt-4 text-button hover:text-text_dark"
                        >
                            Try Again
                        </button>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="latestPlaylists.length === 0" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                        No courses available yet
                    </div>

                    <!-- Playlists Grid -->
                    <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                        <div v-for="playlist in latestPlaylists" 
                             :key="playlist.id" 
                             class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300">
                            <!-- Teacher Info -->
                            <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                                <img 
                                    :src="playlist.teacher?.image" 
                                    :alt="playlist.teacher?.name"
                                    class="h-[4rem] w-[4rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                                >
                                <div>
                                    <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem]">
                                        {{ playlist.teacher?.name }}
                                    </h3>
                                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                        {{ formatDate(playlist.date) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Course Thumbnail -->
                            <div class="relative group">
                                <img 
                                    :src="playlist.thumb" 
                                    :alt="playlist.title"
                                    class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                                >
                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                    <router-link 
                                        :to="'/playlist/' + playlist.id"
                                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-transparent"
                                    >
                                        View Course
                                    </router-link>
                                </div>
                                <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-black bg-opacity-60 text-white text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                                    {{ playlist.content_count }} videos
                                </span>
                            </div>

                            <!-- Course Info -->
                            <div class="mt-4">
                                <h3 class="text-[1.5rem] text-text_dark mb-2 [@media(max-width:550px)]:text-[1.2rem]">
                                    {{ playlist.title }}
                                </h3>
                                <p v-if="playlist.description" class="text-text_light text-[1rem] mb-4 line-clamp-2">
                                    {{ playlist.description }}
                                </p>
                                <router-link 
                                    :to="'/playlist/' + playlist.id"
                                    class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                                >
                                    Start Learning
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const userData = ref(null);
const playlistsAmount = ref(0);
const contentsAmount = ref(0);
const likesAmount = ref(0);
const bookmarksAmount = ref(0);
const commentsAmount = ref(0);
const isLoading = ref(true);
const isLoggingOut = ref(false);
const error = ref(null);

// Latest Playlists State
const latestPlaylists = ref([]);
const isPlaylistsLoading = ref(true);
const playlistsError = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const statistics = computed(() => [
    { value: likesAmount.value, label: 'Liked Videos', link: '/likes' },
    { value: bookmarksAmount.value, label: 'Bookmarked Playlists', link: '/bookmarks' },
    { value: commentsAmount.value, label: 'Comments', link: '/comments' }
]);

// Format date helper
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Process playlist helper
const processPlaylist = async (playlist) => {
    try {
        if (!playlist) return null;

        // Create a processed copy of the playlist
        const processed = { ...playlist };

        // Handle thumbnail
        if (processed.thumb) {
            // Remove any storage/app/public prefixes and clean up the path
            const cleanPath = processed.thumb
                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                .replace(/^\//, '');
            processed.thumb = `/storage/${cleanPath}`;
        } else {
            processed.thumb = '/storage/default-thumbnail.png';
        }

        // Fetch teacher data if we have teacher_id
        if (processed.teacher_id) {
            try {
                const token = localStorage.getItem('token');
                const teacherResponse = await axios.get(`/api/teachers/find/${processed.teacher_id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (teacherResponse.data.data) {
                    const teacherData = teacherResponse.data.data;
                    // Clean up teacher image path
                    let teacherImage = teacherData.image;
                    if (teacherImage) {
                        const cleanTeacherPath = teacherImage
                            .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                            .replace(/^\//, '');
                        teacherImage = `/storage/${cleanTeacherPath}`;
                    }
                    
                    processed.teacher = {
                        ...teacherData,
                        name: teacherData.name || 'Unknown Teacher',
                        image: teacherImage || '/storage/default-avatar.png'
                    };
                } else {
                    throw new Error('No teacher data received');
                }
            } catch (teacherError) {
                console.error('Error fetching teacher:', teacherError);
                processed.teacher = {
                    name: 'Unknown Teacher',
                    image: '/storage/default-avatar.png'
                };
            }
        } else {
            processed.teacher = {
                name: 'Unknown Teacher',
                image: '/storage/default-avatar.png'
            };
        }

        // Get content count
        try {
            const token = localStorage.getItem('token');
            const contentResponse = await axios.get(`/api/contents/playlist/${processed.id}/amount`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            processed.content_count = contentResponse.data || 0;
        } catch (contentError) {
            console.error('Error fetching content count:', contentError);
            processed.content_count = 0;
        }

        return processed;
    } catch (error) {
        console.error(`Error processing playlist ${playlist?.id}:`, error);
        return {
            ...playlist,
            thumb: '/storage/default-thumbnail.png',
            teacher: {
                name: 'Unknown Teacher',
                image: '/storage/default-avatar.png'
            },
            content_count: 0
        };
    }
};

// Methods
const loadUserData = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }
        });
        userData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
        return userData.value;
    } catch (error) {
        console.error('Error loading user data:', error);
        if (!localStorage.getItem('token')) {
            router.push('/');
        }
        throw error;
    }
};

const loadStatistics = async (userId) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const headers = {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        };

        const [likes, bookmarks, comments] = await Promise.all([
            axios.get(`/api/likes/count_user/${userId}`, { headers }),
            axios.get(`/api/bookmarks/count_user/${userId}`, { headers }),
            axios.get(`/api/comments/count_user/${userId}`, { headers })
        ]);
        
        // Handle responses that are wrapped in data property
        likesAmount.value = likes.data?.data || 0;
        bookmarksAmount.value = bookmarks.data?.data || 0;
        commentsAmount.value = comments.data?.data || 0;
    } catch (error) {
        console.error('Error loading statistics:', error);
        // Set default values on error
        likesAmount.value = 0;
        bookmarksAmount.value = 0;
        commentsAmount.value = 0;
        
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/');
        }
    }
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        await axios.post('/api/logout');
        localStorage.removeItem('token');
        router.push('/');
    } catch (error) {
        console.error('Error during logout:', error);
    } finally {
        isLoggingOut.value = false;
    }
};

const loadLatestPlaylists = async () => {
    try {
        isPlaylistsLoading.value = true;
        playlistsError.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const response = await axios.get('/api/playlists/latest', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!Array.isArray(response.data)) {
            throw new Error('Invalid response format');
        }

        const processedPlaylists = await Promise.all(
            response.data.map(async (playlist) => {
                return await processPlaylist(playlist);
            })
        );

        latestPlaylists.value = processedPlaylists.filter(Boolean);
    } catch (err) {
        console.error('Error loading playlists:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/');
        } else {
            playlistsError.value = 'Failed to load latest courses. Please try again.';
        }
    } finally {
        isPlaylistsLoading.value = false;
    }
};

// Lifecycle
onMounted(async () => {
    try {
        isLoading.value = true;
        error.value = null;
        
        const user = await loadUserData();
        if (user) {
            await Promise.all([
                loadStatistics(user.id),
                loadLatestPlaylists()
            ]);
        }
    } catch (err) {
        error.value = 'Failed to load dashboard data. Please try again later.';
        console.error('Dashboard error:', err);
    } finally {
        isLoading.value = false;
    }
});
</script>