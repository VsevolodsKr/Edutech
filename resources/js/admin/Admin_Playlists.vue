<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Your playlists</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <!-- Error Message -->
            <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                {{ error }}
            </div>

            <!-- Playlists Grid -->
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <!-- Create Playlist Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">
                        Create new playlist
                    </h3>
                    <router-link 
                        to="/admin_add_playlist" 
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Add Playlist
                    </router-link>
                </div>

                <!-- Playlist Cards -->
                <div v-for="playlist in playlists" 
                     :key="playlist.id" 
                     class="bg-base rounded-lg p-[2rem] w-full">
                    <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                        <div>
                            <i :class="[
                                playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                'fas fa-circle-dot text-[1.2rem]'
                            ]"></i>
                            <span :class="[
                                playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                'text-[1.2rem]'
                            ]">
                                {{ playlist.status === 'active' ? 'Active' : 'Deactive' }}
                            </span>
                        </div>
                        <div>
                            <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ playlist.formatted_date || playlist.date || 'Date not available' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-center mb-4">
                        <img 
                            :src="getImageUrl(playlist.thumb)" 
                            :alt="playlist.title"
                            @error="handleImageError"
                            class="w-full h-48 object-cover rounded-lg"
                        >
                    </div>

                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ playlist.title }}
                    </h3>

                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem] line-clamp-3">
                        {{ playlist.description }}
                    </div>

                    <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                        <button 
                            @click="router.push(`/admin_playlists/update/${playlist.id}`)"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Update
                        </button>
                        <button 
                            @click="handleDelete(playlist.id)"
                            class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Delete
                        </button>
                    </div>

                    <button 
                        @click="router.push(`/admin_playlists/${playlist.id}`)"
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Playlist
                    </button>
                </div>
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const loading = ref(true);
const error = ref(null);
const playlists = ref([]);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const defaultThumb = '/storage/default-thumb.png';

const getImageUrl = (image) => {
    console.log('Original image path:', image);
    
    if (!image) {
        console.log('No image path provided, using default thumbnail');
        return defaultThumb;
    }
    
    if (image.startsWith('data:')) {
        console.log('Using data URL image');
        return image;
    }
    
    if (image.startsWith('http')) {
        console.log('Using full URL image');
        return image;
    }

    // Clean up the storage path
    let cleanPath = image;
    
    // Remove all instances of 'storage/' from the beginning of the path
    cleanPath = cleanPath.replace(/^(storage\/)+/, '');
    
    // Remove all instances of '/storage/' from the path
    cleanPath = cleanPath.replace(/(\/storage\/)+/, '/');
    
    // Remove any leading or multiple consecutive slashes
    cleanPath = cleanPath.replace(/^\/+/, '').replace(/\/+/g, '/');
    
    console.log('Cleaned path:', cleanPath);
    
    // Construct the final URL
    const finalUrl = `${window.location.origin}/storage/${cleanPath}`;
    console.log('Final URL:', finalUrl);
    
    return finalUrl;
};

const handleImageError = (event) => {
    console.log('Image failed to load:', event.target.src);
    // Only set default thumbnail if it's not already the default
    if (!event.target.src.includes('default-thumb.png')) {
        event.target.src = defaultThumb;
    }
};

const loadPlaylists = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }

        // Get user first to get teacher_id
        const userResponse = await axios.get('/api/user', {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        console.log('User response:', userResponse.data);

        // Get playlists for this teacher using the correct endpoint
        const playlistsResponse = await axios.get(`/api/playlists/teacher_playlists/${userResponse.data.id}`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        console.log('Raw playlists response:', playlistsResponse.data);

        // Handle the response data
        if (playlistsResponse.data && playlistsResponse.data.data) {
            playlists.value = playlistsResponse.data.data;
            console.log('Playlists loaded:', playlists.value);
        } else {
            console.error('Unexpected response format:', playlistsResponse.data);
            error.value = 'Invalid response format from server';
        }

        // Log each playlist for debugging
        playlists.value.forEach(playlist => {
            console.log('Playlist:', {
                id: playlist.id,
                title: playlist.title,
                status: playlist.status,
                thumb: playlist.thumb
            });
        });
    } catch (err) {
        console.error('Error loading playlists:', err);
        if (err.response) {
            console.error('Error response:', err.response.data);
            error.value = err.response.data.message || 'Failed to load playlists';
        } else if (err.request) {
            console.error('No response received:', err.request);
            error.value = 'No response received from server';
        } else {
            console.error('Error setting up request:', err.message);
            error.value = 'Failed to set up request';
        }
    } finally {
        loading.value = false;
    }
};

const handleDelete = async (id) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }

        const response = await axios.delete(`/api/playlists/delete/${id}`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        if (response.data.status === 200) {
            // Remove the deleted playlist from the list
            playlists.value = playlists.value.filter(playlist => playlist.id !== id);
        }
    } catch (err) {
        console.error('Error deleting playlist:', err);
        error.value = 'Failed to delete playlist';
    }
};

// Lifecycle
onMounted(() => {
    loadPlaylists();
});
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>