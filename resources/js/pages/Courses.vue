<template>
    <div>
<Header />
        <div class="main-content">
            <section :class="sectionClasses">
        <h1 class="text-[1.5rem] text-text_dark capitalize">Mūsu kursi</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                <div class="w-full rounded-lg bg-base py-[.5rem] mb-[1rem] px-[1.5rem] flex gap-[2rem] items-center">
                    <input 
                        v-model="searchQuery"
                        @input="debouncedSearch"
                        class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none [@media(max-width:550px)]:text-[1rem]"
                        type="text" 
                        placeholder="Meklēt kursus..." 
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
        
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                    </div>

                <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                    {{ error }}
                    <button 
                        @click="loadPlaylistData" 
                        class="block mx-auto mt-4 text-button hover:text-text_dark"
                    >
                        Mēģināt vēlreiz
                    </button>
                </div>

                <div v-else-if="playlists.length === 0" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                    {{ searchQuery ? 'Nav kursu, kas atbilst jūsu meklēšanas kritērijiem' : 'Nav pieejami kursi' }}
                </div>

                <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                    <div v-for="playlist in playlists" 
                         :key="playlist.id" 
                         class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300">
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
    </section>
        </div>
<Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useWindowSize } from '@vueuse/core';
import { useRouter } from 'vue-router';
import { debounce } from 'lodash';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

const searchQuery = ref('');
const isSearching = ref(false);

const showSidebar = computed(() => store.getters.getShowSidebar);
const playlists = computed(() => {
    if (!searchQuery.value.trim()) {
        return store.getters.getCourses;
    }
    return store.getters.getCourses.filter(course => 
        course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        course.teacher?.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
const isLoading = computed(() => store.getters.getCoursesLoading);
const error = computed(() => store.getters.getCoursesError);
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

const handleSearch = async () => {
    try {
        isSearching.value = true;
        if (!searchQuery.value.trim()) {
            return;
        } else {
            const existingCourses = store.getters.getCourses;
            const filteredCourses = existingCourses.filter(course => 
                course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                course.teacher?.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            );

            if (filteredCourses.length === 0) {
                await store.dispatch('searchCourses', searchQuery.value);
            }
        }
    } catch (err) {
        console.error('Error searching courses:', err);
    } finally {
        isSearching.value = false;
    }
};

const debouncedSearch = debounce(() => {
    if (searchQuery.value.trim()) {
        handleSearch();
    }
}, 500);

onMounted(async () => {
    await store.dispatch('loadCourses');
});

watch(() => router.currentRoute.value, async () => {
    await store.dispatch('loadCourses');
});

watch(searchQuery, (newValue) => {
    if (!newValue.trim()) {
        store.dispatch('loadCourses');
    }
});
</script>