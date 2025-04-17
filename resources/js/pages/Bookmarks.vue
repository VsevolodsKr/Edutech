<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                Gramātiezīmētie kursi
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadBookmarks"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Mēģināt vēlreiz
                </button>
            </div>

            <div v-else-if="!playlists.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                Jums nav grāmatzīmēts neviens kurss.
            </div>

            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(33rem,_1fr))] gap-[1rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="playlist in playlists" 
                    :key="playlist.id" 
                    class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300"
                >
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

                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ playlist.title }}
                    </h3>
                    <div class="flex justify-center gap-[1rem]">
                        <router-link 
                            :to="'/playlist/' + playlist.id"
                            class="flex-1 bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Skatīt kursu
                        </router-link>
                        <button 
                            @click="() => deleteBookmark(playlist.bookmark_id)"
                            :disabled="isDeleting === playlist.bookmark_id"
                            class="flex-1 bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            {{ isDeleting === playlist.bookmark_id ? 'Dzēšana...' : 'Dzēst' }}
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

const playlists = ref([]);
const user = ref(null);
const isLoading = ref(true);
const isDeleting = ref(null);
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
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

        playlists.value = response.data.playlists.map((playlist, index) => {
            const cleanThumbPath = playlist.thumb
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');

            const cleanTeacherImagePath = response.data.teachers[index].image
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');

            return {
                ...playlist,
                thumb: cleanThumbPath ? `/storage/${cleanThumbPath}` : '/storage/default-thumbnail.png',
                teacher: {
                    ...response.data.teachers[index],
                    image: cleanTeacherImagePath ? `/storage/${cleanTeacherImagePath}` : '/storage/default-avatar.png'
                },
                bookmark_id: response.data.bookmarks[index].id
            };
        });
    } catch (err) {
        console.error('Error loading bookmarks:', err);
        error.value = 'Failed to load bookmarks. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

const deleteBookmark = async (bookmarkId) => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Šis gramātiezīmēts kurss tiks dzēsts.',
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
            isDeleting.value = bookmarkId;
            const token = localStorage.getItem('token');
            const headers = { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            };

            await axios.delete(`/api/bookmarks/delete/${bookmarkId}`, { headers });

            playlists.value = playlists.value.filter(playlist => playlist.bookmark_id !== bookmarkId);

            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Gramātiezīmēts kurss ir dzēsts.',
                icon: 'success',
                color: text_dark,
                background: background,
            });
        }
    } catch (err) {
        console.error('Error deleting bookmark:', err);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst grāmatzīmēto kursu. Lūdzu, mēģiniet vēlreiz.',
            icon: 'error',
            color: text_dark,
            background: background,
        });
    } finally {
        isDeleting.value = null;
    }
};

onMounted(() => {
    loadBookmarks();
});
</script>