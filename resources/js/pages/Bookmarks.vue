<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Bookmarked Playlists
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
                    @click="loadBookmarks"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Try Again
                </button>
            </div>

            <!-- No Bookmarks -->
            <div v-else-if="!playlists.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                You haven't bookmarked any playlists yet.
            </div>

            <!-- Bookmarks Grid -->
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(33rem,_1fr))] gap-[1rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="playlist in playlists" 
                    :key="playlist.id" 
                    class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300"
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
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-play text-[3rem] text-white"></i>
                        </div>
                    </div>

                    <!-- Playlist Title and Actions -->
                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ playlist.title }}
                    </h3>
                    <div class="flex justify-center gap-[1rem]">
                        <router-link 
                            :to="'/playlist/' + playlist.id"
                            class="flex-1 bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            View Playlist
                        </router-link>
                        <button 
                            @click="() => deleteBookmark(playlist.bookmark_id)"
                            :disabled="isDeleting === playlist.bookmark_id"
                            class="flex-1 bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            {{ isDeleting === playlist.bookmark_id ? 'Removing...' : 'Remove' }}
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
const playlists = ref([]);
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
        day: 'numeric'
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

const loadBookmarks = async () => {
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

        const response = await axios.get(`/api/bookmarks/user/${userData.id}`, { headers });

        // Transform and combine data
        playlists.value = response.data.playlists.map((playlist, index) => ({
            ...playlist,
            thumb: new URL(playlist.thumb, import.meta.url),
            teacher: {
                ...response.data.teachers[index],
                image: new URL(response.data.teachers[index].image, import.meta.url)
            },
            bookmark_id: response.data.bookmarks[index].id
        }));
    } catch (err) {
        console.error('Error loading bookmarks:', err);
        error.value = 'Failed to load bookmarks. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

const deleteBookmark = async (bookmarkId) => {
    try {
        // Get computed styles for SweetAlert
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        // Show confirmation dialog
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This playlist will be removed from your bookmarks.',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, remove it!'
        });

        if (result.isConfirmed) {
            isDeleting.value = bookmarkId;
            const token = localStorage.getItem('token');
            const headers = { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            };

            await axios.delete(`/api/bookmarks/delete/${bookmarkId}`, { headers });

            // Remove the playlist from the list
            playlists.value = playlists.value.filter(playlist => playlist.bookmark_id !== bookmarkId);

            await Swal.fire({
                title: 'Removed!',
                text: 'Playlist has been removed from your bookmarks.',
                icon: 'success',
                color: text_dark,
                background: background,
            });
        }
    } catch (err) {
        console.error('Error deleting bookmark:', err);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to remove the bookmark. Please try again.',
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
    loadBookmarks();
});
</script>