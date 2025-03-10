<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Your playlists</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                    <!-- Create New Playlist Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">
                            Create new playlist
                        </h3>
                        <router-link to="/admin_add_playlist" 
                                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                            Add Playlist
                        </router-link>
                    </div>

                    <!-- Playlist Cards -->
                    <div v-for="playlist in playlistsWithCount" 
                         :key="playlist.id" 
                         class="bg-base rounded-lg p-[2rem] w-full">
                        <!-- Status and Date -->
                        <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                            <div>
                                <i :class="[playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot text-[1.2rem]']"></i>
                                <span :class="[playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">
                                    {{ playlist.status }}
                                </span>
                            </div>
                            <div>
                                <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ formatDate(playlist.date) }}
                                </span>
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div class="relative">
                            <img :src="playlist.thumb" 
                                 :alt="playlist.title"
                                 class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                            <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[1rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1.2rem] [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.7rem]">
                                {{ playlist.contentCount }}
                            </span>
                        </div>

                        <!-- Content -->
                        <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                            {{ playlist.title }}
                        </h3>
                        <p class="text-text_light mb-[1rem]">{{ playlist.description }}</p>

                        <!-- Actions -->
                        <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                            <button @click="router.push(`/admin_playlists/update/${playlist.id}`)" 
                                    class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Update
                            </button>
                            <button @click="handleDeletePlaylist(playlist.id)" 
                                    class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Delete
                            </button>
                        </div>
                        <button @click="router.push(`/admin_playlists/${playlist.id}`)" 
                                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                            View Playlist
                        </button>
                    </div>
                </div>
            </template>
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
const isLoading = ref(true);
const playlists = ref([]);
const playlistsWithCount = ref([]);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

const loadPlaylists = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        const playlistsResponse = await axios.get(`/api/playlists/${userResponse.data.id}`);
        playlists.value = playlistsResponse.data.map(playlist => ({
            ...playlist,
            thumb: new URL(playlist.thumb, window.location.origin).href
        }));

        // Load content counts for each playlist
        const contentCountPromises = playlists.value.map(playlist => 
            axios.get(`/api/contents/playlist/${playlist.id}/amount`)
        );

        const contentCounts = await Promise.all(contentCountPromises);
        
        playlistsWithCount.value = playlists.value.map((playlist, index) => ({
            ...playlist,
            contentCount: contentCounts[index].data
        }));
    } catch (error) {
        console.error('Error loading playlists:', error);
        if (error.response?.status === 401) {
            router.push('/');
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load playlists',
                icon: 'error'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const handleDeletePlaylist = async (playlistId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Playlist will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/playlists/delete/${playlistId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Playlist has been deleted.',
                icon: 'success'
            });

            // Reload playlists after deletion
            await loadPlaylists();
        }
    } catch (error) {
        console.error('Error deleting playlist:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete playlist',
            icon: 'error'
        });
    }
};

// Lifecycle
onMounted(() => {
    loadPlaylists();
});
</script>