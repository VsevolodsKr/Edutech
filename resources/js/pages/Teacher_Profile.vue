<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark">Pasniedzēja profils</h1>
                    <router-link 
                        to="/teachers"
                        class="bg-background text-text_dark px-4 py-2 rounded-lg hover:bg-base transition-colors duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:px-3 [@media(max-width:550px)]:py-1"
                    >
                        Atpakaļ uz pasniedzējiem
                    </router-link>
                </div>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                
                <div v-else-if="teacher" class="grid grid-cols-1 lg:grid-cols-[1fr,_2fr] gap-[2rem]">
                    <div class="bg-base rounded-lg p-[2rem] text-center h-fit">
                        <img 
                            :src="teacher.image" 
                            :alt="teacher.name"
                            class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-md"
                        >
                        <h2 class="text-[1.5rem] text-text_dark mb-2 [@media(max-width:550px)]:text-[1.25rem]">{{ teacher.name }}</h2>
                        <p class="text-[1rem] text-text_light mb-4 [@media(max-width:550px)]:text-[0.9rem]">{{ teacher.profession }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6 bg-background rounded-lg p-4">
                            <div class="text-center">
                                <h3 class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.25rem]">{{ totalPlaylists }}</h3>
                                <p class="text-[0.9rem] text-text_light [@media(max-width:550px)]:text-[0.8rem]">Kursi</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.25rem]">{{ totalContents }}</h3>
                                <p class="text-[0.9rem] text-text_light [@media(max-width:550px)]:text-[0.8rem]">Video</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h3 class="text-[1.25rem] text-text_dark mb-4 [@media(max-width:550px)]:text-[1.1rem]">
                            {{ teacher.name }} kursi
                        </h3>
                        
                        <div v-if="playlists.length === 0" 
                             class="text-center text-text_light text-[1rem] bg-base rounded-lg p-8 [@media(max-width:550px)]:text-[0.9rem]">
                            Kursi nav pieejami
                        </div>
                        
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="playlist in playlists" 
                                 :key="playlist.id"
                                 class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300">
                                
                                <div class="relative group">
                                    <img 
                                        :src="playlist.thumb" 
                                        :alt="playlist.title"
                                        class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                                    >
                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                        <router-link 
                                            :to="'/playlist/' + playlist.encrypted_id"
                                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-transparent"
                                        >
                                            Skatīt kursu
                                        </router-link>
                                    </div>
                                    <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-black bg-opacity-60 text-white text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                                        {{ playlist.content_count }} video
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <h3 class="text-[1.5rem] text-text_dark mb-2 [@media(max-width:550px)]:text-[1.2rem]">
                                        {{ playlist.title }}
                                    </h3>
                                    <p v-if="playlist.description" class="text-text_light text-[1rem] mb-4 line-clamp-2">
                                        {{ playlist.description }}
                                    </p>
                                    <router-link 
                                        :to="'/playlist/' + playlist.encrypted_id"
                                        class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                                    >
                                        Sākt mācīties
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="flex flex-col items-center justify-center min-h-[50vh] bg-base rounded-lg p-8">
                    <i class="fas fa-user-slash text-[2rem] text-text_light mb-4"></i>
                    <p class="text-text_light text-[1.1rem] [@media(max-width:550px)]:text-[1rem]">Pasniedzējs nav atrasts</p>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';
import axios from 'axios';

const route = useRoute();
const { width } = useWindowSize();

const teacher = ref(null);
const playlists = ref([]);
const isLoading = ref(true);
const totalPlaylists = ref(0);
const totalContents = ref(0);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const processPlaylist = (playlist) => {
    if (!playlist) return null;

    const processed = { ...playlist };

    if (processed.thumb) {
        let cleanPath = processed.thumb
            .replace(/^\/storage\/+/g, '')
            .replace(/^storage\/+/, '')
            .replace(/\/+/g, '/');
        
        processed.thumb = `/storage/${cleanPath}`;
    } else {
        processed.thumb = '/storage/default-thumbnail.png';
    }

    return processed;
};

const loadTeacherData = async () => {
    try {
        isLoading.value = true;
        const encryptedTeacherId = route.params.id;
        
        const [teacherResponse, playlistsResponse] = await Promise.all([
            axios.get(`/api/teachers/find/${encryptedTeacherId}`),
            axios.get(`/api/playlists/teacher_active_playlists/${encryptedTeacherId}`)
        ]);
        
        if (!teacherResponse.data.data) {
            throw new Error('Teacher not found');
        }

        const teacherData = teacherResponse.data.data;
        const cleanTeacherImagePath = teacherData.image
            ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
            ?.replace(/^\//, '');

        teacher.value = {
            ...teacherData,
            image: cleanTeacherImagePath ? `/storage/${cleanTeacherImagePath}` : '/storage/default-avatar.png'
        };

        if (playlistsResponse.data.status === 200) {
            const processedPlaylists = playlistsResponse.data.data.map(processPlaylist).filter(Boolean);
            
            const contentCountPromises = processedPlaylists.map(playlist => 
                axios.get(`/api/contents/playlist/${playlist.encrypted_id}/amount`)
                    .then(response => ({ id: playlist.id, encrypted_id: playlist.encrypted_id, count: response.data || 0 }))
                    .catch(() => ({ id: playlist.id, encrypted_id: playlist.encrypted_id, count: 0 }))
            );
            
            const contentCounts = await Promise.all(contentCountPromises);
            const contentCountMap = new Map(contentCounts.map(({ id, count }) => [id, count]));
            
            playlists.value = processedPlaylists.map(playlist => ({
                ...playlist,
                content_count: contentCountMap.get(playlist.id) || 0
            }));
            
            totalPlaylists.value = playlists.value.length;
            totalContents.value = playlists.value.reduce((sum, playlist) => sum + (playlist.content_count || 0), 0);
        } else {
            playlists.value = [];
            totalPlaylists.value = 0;
            totalContents.value = 0;
        }

    } catch (error) {
        console.error('Error loading teacher data:', error);
        if (error.response?.status === 404) {
            teacher.value = null;
        } else {
            teacher.value = null;
            playlists.value = [];
            totalPlaylists.value = 0;
            totalContents.value = 0;
        }
    } finally {
        isLoading.value = false;
    }
};

onMounted(async () => {
    try {
        if (!store.getters.getUser) {
            await store.dispatch('loadUserData');
        }
        
        await loadTeacherData();
    } catch (error) {
        console.error('Error initializing component:', error);
    }
});
</script>