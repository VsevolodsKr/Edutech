<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Your playlists</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

            <!-- Loading State -->
            <div v-if="loading && !playlists.length && playlists.length != 0" class="flex justify-center items-center min-h-[50vh]">
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
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const error = ref(null);
const isLoading = ref(true);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const playlists = computed(() => store.getters.getPlaylists);
const storeIsLoading = computed(() => store.getters.getIsLoading);
const loading = computed(() => isLoading.value || storeIsLoading.value);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const defaultThumb = '/storage/default-thumb.png';

const getImageUrl = (image) => {
    if (!image) return defaultThumb;
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;

    // Clean up the storage path
    let cleanPath = image;
    cleanPath = cleanPath.replace(/^(storage\/)+/, '');
    cleanPath = cleanPath.replace(/(\/storage\/)+/, '/');
    cleanPath = cleanPath.replace(/^\/+/, '').replace(/\/+/g, '/');
    
    return `${window.location.origin}/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    if (!event.target.src.includes('default-thumb.png')) {
        event.target.src = defaultThumb;
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
            // Update store instead of local state
            const updatedPlaylists = playlists.value.filter(playlist => playlist.id !== id);
            store.commit('setPlaylists', updatedPlaylists);
            error.value = null;
        } else {
            error.value = response.data.message || 'Failed to delete playlist';
        }
    } catch (err) {
        console.error('Error deleting playlist:', err);
        error.value = err.response?.data?.message || 'Failed to delete playlist';
    }
};

// Lifecycle
onMounted(async () => {
    try {
        const user = store.getters.getUser;
        if (user) {
            await store.dispatch('loadPlaylists', user.id);
        }
    } catch (error) {
        console.error('Error loading playlists:', error);
    } finally {
        isLoading.value = false;
    }
});

// Watch for store loading state changes
watch(storeIsLoading, (newValue) => {
    if (!newValue && playlists.value.length === 0) {
        isLoading.value = false;
    }
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