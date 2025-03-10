<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                    Playlist Details
                </h1>
                <hr class="border-[#ccc] mb-[2rem]">

                <!-- Playlist Info -->
                <div v-if="playlist" class="flex items-center gap-[3rem] flex-wrap bg-base p-[1rem] rounded-lg">
                    <div class="flex-[1_1_20rem]">
                        <form @submit.prevent="savePlaylist" class="mb-[1rem]">
                            <button type="submit" class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:px-[.5rem] [@media(max-width:550px)]:py-[.2rem]">
                                <i class="far fa-bookmark text-[1rem] mr-[.8rem] [@media(max-width:550px)]:text-[.7rem]"></i>
                                <span class="text-1rem [@media(max-width:550px)]:text-[.7rem]">Save Playlist</span>
                            </button>
                        </form>
                        <div class="relative">
                            <img :src="playlist.thumb" 
                                 :alt="playlist.title"
                                 class="h-[20rem] w-full object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
                            <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                                {{ contentCount }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-[1_1_40rem]">
                        <div>
                            <h3 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                                {{ playlist.title }}
                            </h3>
                            <p class="py-[1rem] leading-2 text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ playlist.description }}
                            </p>
                        </div>
                        <div class="flex gap-[1rem]">
                            <button @click="router.push(`/admin_playlists/update/${playlist.id}`)" 
                                    class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Update Playlist
                            </button>
                            <button @click="handleDeletePlaylist(playlist.id)" 
                                    class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Delete Playlist
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Playlist Contents -->
                <div class="pt-[4rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]">
                    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                        Playlist Videos
                    </h1>
                    <hr class="border-[#ccc] mb-[2rem]">
                    
                    <div v-if="!contents.length" class="text-center text-text_light py-4">
                        No videos in this playlist
                    </div>
                    
                    <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                        <div v-for="content in contents" 
                             :key="content.id" 
                             class="bg-base rounded-lg p-[2rem] w-full">
                            <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                                <div>
                                    <i :class="[content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot text-[1.2rem]']"></i>
                                    <span :class="[content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">
                                        {{ content.status }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                        {{ formatDate(content.date) }}
                                    </span>
                                </div>
                            </div>
                            <div class="relative">
                                <img :src="content.thumb" 
                                     :alt="content.title"
                                     class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                            </div>
                            <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                                {{ content.title }}
                            </h3>
                            <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                                <button @click="router.push(`/admin_contents/update/${content.id}`)" 
                                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                    Update
                                </button>
                                <button @click="handleDeleteContent(content.id)" 
                                        class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                    Delete
                                </button>
                            </div>
                            <button @click="router.push(`/admin_contents/${content.id}`)" 
                                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                View Content
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const route = useRoute();
const { width } = useWindowSize();

// State
const isLoading = ref(true);
const playlist = ref(null);
const contents = ref([]);
const contentCount = ref(0);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadPlaylist = async () => {
    try {
        const response = await axios.get(`/api/playlists/find/${route.params.id}`);
        if (response.data.playlist) {
            playlist.value = {
                ...response.data.playlist,
                thumb: new URL(response.data.playlist.thumb, window.location.origin).href
            };

            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.id}/amount`);
            contentCount.value = countResponse.data;
        }
    } catch (error) {
        console.error('Error loading playlist:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load playlist',
            icon: 'error'
        });
    }
};

const loadContents = async () => {
    if (!playlist.value) return;
    
    try {
        const response = await axios.get(`/api/playlists/${playlist.value.id}/contents`);
        contents.value = response.data.map(content => ({
            ...content,
            thumb: new URL(content.thumb, window.location.origin).href
        }));
    } catch (error) {
        console.error('Error loading contents:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load playlist contents',
            icon: 'error'
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
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

            router.push('/admin_playlists');
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

const handleDeleteContent = async (contentId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Content will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/contents/delete/${contentId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Content has been deleted.',
                icon: 'success'
            });

            // Reload contents and update count
            await loadContents();
            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.id}/amount`);
            contentCount.value = countResponse.data;
        }
    } catch (error) {
        console.error('Error deleting content:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete content',
            icon: 'error'
        });
    }
};

const savePlaylist = async () => {
    try {
        await axios.post(`/api/playlists/${playlist.value.id}/save`);
        Swal.fire({
            title: 'Success!',
            text: 'Playlist has been saved',
            icon: 'success'
        });
    } catch (error) {
        console.error('Error saving playlist:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to save playlist',
            icon: 'error'
        });
    }
};

// Lifecycle
onMounted(async () => {
    try {
        await loadPlaylist();
        await loadContents();
    } catch (error) {
        console.error('Error initializing component:', error);
    } finally {
        isLoading.value = false;
    }
});
</script>