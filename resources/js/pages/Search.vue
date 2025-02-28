<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">
                {{ searchTitle }}
            </h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadPlaylists"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Try Again
                </button>
            </div>

            <!-- No Results -->
            <div v-else-if="!playlists.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                No playlists found{{ searchQuery ? ` for "${searchQuery}"` : '' }}
            </div>

            <!-- Results Grid -->
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="(playlist, index) in playlists" 
                    :key="playlist.id" 
                    class="bg-base rounded-lg p-[2rem] w-full hover:shadow-lg transition-shadow duration-300"
                >
                    <!-- Teacher Info -->
                    <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                        <img 
                            :src="playlist.teacher?.image" 
                            :alt="playlist.teacher?.name"
                            class="h-[4rem] w-[4rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                        >
                        <div>
                            <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">
                                {{ playlist.teacher?.name }}
                            </h3>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ formatDate(playlist.date) }}
                            </span>
                        </div>
                    </div>

                    <!-- Playlist Thumbnail -->
                    <div class="relative group">
                        <img 
                            :src="playlist.thumb" 
                            :alt="playlist.title"
                            class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                        >
                        <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-black bg-opacity-30 text-white text-[1rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.5rem]">
                            {{ playlist.contentCount }} videos
                        </span>
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-play text-[3rem] text-white"></i>
                        </div>
                    </div>

                    <!-- Playlist Title and Action -->
                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ playlist.title }}
                    </h3>
                    <router-link 
                        :to="'/playlist/' + playlist.id"
                        class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[2rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:px-[1.5rem]"
                    >
                        View Playlist
                    </router-link>
                </div>
            </div>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const playlists = ref([]);
const isLoading = ref(true);
const error = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const searchQuery = computed(() => store.getters.getSearchPlaylist);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const searchTitle = computed(() => 
    searchQuery.value ? `Search Results for "${searchQuery.value}"` : 'All Playlists'
);

// Methods
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const loadPlaylists = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // Determine which API endpoint to use
        const response = searchQuery.value
            ? await axios.post('/api/playlists/search', { name: searchQuery.value })
            : await axios.get('/api/playlists/all');

        // Process playlists and fetch related data
        const playlistsWithDetails = await Promise.all(
            response.data.map(async (playlist) => {
                // Transform playlist data
                const transformedPlaylist = {
                    ...playlist,
                    thumb: new URL(playlist.thumb, import.meta.url)
                };

                try {
                    // Fetch teacher data
                    const teacherResponse = await axios.get(`/api/playlists/${playlist.teacher_id}/teacher`);
                    transformedPlaylist.teacher = {
                        ...teacherResponse.data,
                        image: new URL(teacherResponse.data.image, import.meta.url)
                    };

                    // Fetch content count
                    const countResponse = await axios.get(`/api/contents/playlist/${playlist.id}/amount`);
                    transformedPlaylist.contentCount = countResponse.data;

                    return transformedPlaylist;
                } catch (err) {
                    console.error('Error fetching playlist details:', err);
                    return transformedPlaylist;
                }
            })
        );

        playlists.value = playlistsWithDetails;
    } catch (err) {
        console.error('Error loading playlists:', err);
        error.value = 'Failed to load playlists. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

// Watch for search query changes
watch(() => store.getters.getSearchPlaylist, () => {
    loadPlaylists();
}, { immediate: true });

// Initialize
onMounted(() => {
    if (!playlists.value.length) {
        loadPlaylists();
    }
});
</script>