<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                    Kursa informācija
                </h1>
                <hr class="border-[#ccc] mb-[2rem]">

                <div v-if="playlist" class="flex items-center gap-[3rem] flex-wrap bg-base p-[1rem] rounded-lg">
                    <div class="flex-[1_1_20rem]">
                        <div class="relative">
                            <img :src="getImageUrl(playlist.thumb)" 
                                 :alt="playlist.title"
                                 @error="handleImageError"
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
                                    class="bg-button3 text-base text-center border-3 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Rediģēt kursu
                            </button>
                            <button @click="handleDeletePlaylist(playlist.id)" 
                                    class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Dzēst kursu
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-[4rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]">
                    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                        Kursa video
                    </h1>
                    <hr class="border-[#ccc] mb-[2rem]">
                    
                    <div v-if="!contents.length" class="text-center text-text_light py-4">
                        Nav video kursā
                    </div>
                    
                    <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                        <div v-for="content in contents" 
                             :key="content.id" 
                             class="bg-base rounded-lg p-[2rem] w-full">
                            <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                                <div>
                                    <i :class="[content.status === 'Aktīvs' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot text-[1.2rem] mr-[.3rem]']"></i>
                                    <span :class="[content.status === 'Aktīvs' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">
                                        {{ content.status }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-calendar text-button text-[1.2rem] mr-[.3rem]"></i>
                                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                        {{ formatDate(content.date) }}
                                    </span>
                                </div>
                            </div>
                            <div class="relative">
                                <img :src="getImageUrl(content.thumb)" 
                                     :alt="content.title"
                                     @error="handleImageError"
                                     class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                            </div>
                            <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                                {{ content.title }}
                            </h3>
                            <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                                <button @click="router.push(`/admin_contents/update/${content.id}`)" 
                                        class="bg-button3 text-base text-center border-2 border-button3 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button3 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                    Rediģēt
                                </button>
                                <button @click="handleDeleteContent(content.id)" 
                                        class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                    Dzēst
                                </button>
                            </div>
                            <button @click="router.push(`/admin_watch_content/${content.encrypted_id}`)" 
                                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">
                                Skatīt video
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

const isLoading = ref(true);
const playlist = ref(null);
const contents = ref([]);
const contentCount = ref(0);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] bg-background pr-[2rem] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const defaultThumb = '/storage/default-thumb.png';

const getImageUrl = (image) => {
    
    if (!image) {
        return defaultThumb;
    }
    
    if (image.startsWith('data:') || image.startsWith('http')) {
        return image;
    }

    let cleanPath = image;
    
    cleanPath = cleanPath.replace(/^\/?(storage\/)?(app\/public\/)?/g, '');
    
    cleanPath = cleanPath.replace(/^\/+/, '');
    
    const finalUrl = `${window.location.origin}/storage/${cleanPath}`;
    
    return finalUrl;
};

const handleImageError = (event) => {
    if (!event.target.src.includes('default-thumb.png')) {
        event.target.src = defaultThumb;
    }
};

const loadPlaylist = async () => {
    try {
        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            console.error('No user data available');
            Swal.fire({
                title: 'Kļūda!',
                text: 'Nav pieejama lietotāja informācija',
                icon: 'error'
            });
            return;
        }

        console.log('Loading playlist with ID:', route.params.id);
        const response = await axios.get(`/api/playlists/find/${route.params.id}`, {
            headers: {
                'X-Encrypted-ID': user.encrypted_id
            }
        });
        
        console.log('Playlist response:', response.data);
        
        if (!response.data || !response.data.playlist) {
            console.error('Invalid playlist data:', response.data);
            throw new Error('Invalid playlist data received');
        }

        playlist.value = {
            ...response.data.playlist,
            thumb: response.data.playlist.thumb,
            id: response.data.playlist.id,
            encrypted_id: response.data.playlist.encrypted_id
        };

        try {
            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.encrypted_id}/amount`, {
                headers: {
                    'X-Encrypted-ID': user.encrypted_id
                }
            });
            contentCount.value = countResponse.data;
        } catch (countError) {
            contentCount.value = 0;
        }
    } catch (error) {
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās ielādēt kursu',
            icon: 'error'
        });
        playlist.value = null;
    } finally {
        isLoading.value = false;
    }
};

const loadContents = async () => {
    if (!playlist.value) return;
    
    try {
        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            return;
        }

        const response = await axios.get(`/api/playlists/${playlist.value.encrypted_id}/contents`, {
            headers: {
                'X-Encrypted-ID': user.encrypted_id
            }
        });
        
        if (!response.data) {
            throw new Error('No data received from server');
        }

        contents.value = response.data.map(content => ({
            ...content,
            thumb: content.thumb
        }));
    } catch (error) {
        if (error.response?.status !== 200 || error.response?.data?.length === 0) {
            Swal.fire({
                title: 'Kļūda!',
                text: 'Neizdevās ielādēt kursa saturu',
                icon: 'error'
            });
        }
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
};

const handleDeletePlaylist = async (playlistId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            return;
        }

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Kurss tiks dzēsts!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Jā, dzēst!',
            cancelButtonText: 'Atcelt'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/playlists/delete/${playlistId}`, {
                headers: {
                    'X-Encrypted-ID': user.encrypted_id
                }
            });
            
            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Kursa video ir dzēsts.',
                icon: 'success'
            });

            router.push('/admin_playlists');
        }
    } catch (error) {
        console.error('Error deleting playlist:', error);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst kursu',
            icon: 'error'
        });
    }
};

const handleDeleteContent = async (contentId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            return;
        }

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Video tiks dzēsts!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Jā, dzēst!',
            cancelButtonText: 'Atcelt'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/contents/delete/${contentId}`, {
                headers: {
                    'X-Encrypted-ID': user.encrypted_id
                }
            });
            
            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Video ir dzēsts.',
                icon: 'success'
            });

            await loadContents();
            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.encrypted_id}/amount`, {
                headers: {
                    'X-Encrypted-ID': user.encrypted_id
                }
            });
            contentCount.value = countResponse.data;
        }
    } catch (error) {
        console.error('Error deleting content:', error);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst video',
            icon: 'error'
        });
    }
};

const savePlaylist = async () => {
    try {
        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            return;
        }

        await axios.post(`/api/playlists/${playlist.value.id}/save`, {}, {
            headers: {
                'X-Encrypted-ID': user.encrypted_id
            }
        });
        Swal.fire({
            title: 'Veiksmīgi!',
            text: 'Kurss ir saglabāts.',
            icon: 'success'
        });
    } catch (error) {
        console.error('Error saving playlist:', error);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās saglabāt kursu',
            icon: 'error'
        });
    }
};

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