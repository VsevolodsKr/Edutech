<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Our Courses</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <!-- Search Bar -->
                <div class="w-full rounded-lg bg-base py-[.5rem] mb-[1rem] px-[1.5rem] flex gap-[2rem] items-center">
                    <input 
                        v-model="searchQuery"
                        @input="debouncedSearch"
                        class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none [@media(max-width:550px)]:text-[1rem]"
                        type="text" 
                        placeholder="Search courses..." 
                        maxlength="100"
                    >
                    <button 
                        @click="handleSearch" 
                        :disabled="isSearching"
                        class="bg-transparent text-[1.5rem] cursor-pointer text-text_light hover:text-button disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i :class="isSearching ? 'fas fa-spinner fa-spin' : 'fas fa-search'"></i>
                    </button>
                </div>

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

                <!-- Empty State -->
                <div v-else-if="playlists.length === 0" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                    {{ searchQuery ? 'No courses found matching your search' : 'No courses available' }}
                </div>

                <!-- Course Grid -->
                <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                    <div v-for="playlist in playlists" 
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
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import { useRouter } from 'vue-router';
import { debounce } from 'lodash';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const playlists = ref([]);
const searchQuery = ref('');
const isLoading = ref(true);
const isSearching = ref(false);
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
        month: 'short',
        day: 'numeric'
    });
};

const processPlaylist = async (playlist) => {
    try {
        if (!playlist) return null;

        // Create a processed copy of the playlist
        const processed = { ...playlist };

        // Handle thumbnail
        if (processed.thumb) {
            processed.thumb = processed.thumb.startsWith('http') ? 
                processed.thumb : 
                `/storage/${processed.thumb.replace('/storage/', '')}`;
        } else {
            processed.thumb = '/images/default-thumbnail.png';
        }

        // Fetch teacher data if we have teacher_id
        if (processed.teacher_id) {
            try {
                const token = localStorage.getItem('token');
                console.log('Fetching teacher for ID:', processed.teacher_id); // Debug log
                const teacherResponse = await axios.get(`/api/teachers/find/${processed.teacher_id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Teacher Response:', teacherResponse.data); // Debug log
                const teacherData = teacherResponse.data;
                
                if (teacherData) {
                    processed.teacher = {
                        ...teacherData,
                        name: teacherData.name || 'Unknown Teacher',
                        image: teacherData.image || '/images/default-avatar.png'
                    };
                } else {
                    throw new Error('No teacher data received');
                }
            } catch (teacherError) {
                console.error('Error fetching teacher:', teacherError);
                processed.teacher = {
                    name: 'Unknown Teacher',
                    image: '/images/default-avatar.png'
                };
            }
        } else {
            processed.teacher = {
                name: 'Unknown Teacher',
                image: '/images/default-avatar.png'
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

        console.log('Processed playlist:', processed); // Debug log
        return processed;
    } catch (error) {
        console.error(`Error processing playlist ${playlist?.id}:`, error);
        return {
            ...playlist,
            thumb: '/images/default-thumbnail.png',
            teacher: {
                name: 'Unknown Teacher',
                image: '/images/default-avatar.png'
            },
            content_count: 0
        };
    }
};

const loadPlaylistData = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const response = await axios.get('/api/playlists/all', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        console.log('Raw API Response:', response.data);

        if (!Array.isArray(response.data)) {
            throw new Error('Invalid response format');
        }

        const processedPlaylists = await Promise.all(
            response.data.map(async (playlist) => {
                console.log('Processing playlist:', playlist);
                return await processPlaylist(playlist);
            })
        );

        console.log('Processed playlists:', processedPlaylists);
        playlists.value = processedPlaylists.filter(Boolean);
    } catch (err) {
        console.error('Error loading playlists:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/');
        } else {
            error.value = 'Failed to load courses. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

const handleSearch = async () => {
    try {
        isSearching.value = true;
        error.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        if (!searchQuery.value.trim()) {
            return loadPlaylistData();
        }

        const formData = new FormData();
        formData.append('name', searchQuery.value.trim());

        const response = await axios.post('/api/playlists/search', formData, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        console.log('Search API Response:', response.data);

        const processedPlaylists = await Promise.all(
            response.data.map(async (playlist) => {
                console.log('Processing search result:', playlist);
                return await processPlaylist(playlist);
            })
        );

        playlists.value = processedPlaylists.filter(Boolean);
    } catch (err) {
        console.error('Error searching courses:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/');
        } else {
            error.value = 'Failed to search courses. Please try again.';
        }
    } finally {
        isSearching.value = false;
    }
};

// Debounced search
const debouncedSearch = debounce(() => {
    handleSearch();
}, 500);

// Lifecycle
onMounted(() => {
    loadPlaylistData();
});
</script>