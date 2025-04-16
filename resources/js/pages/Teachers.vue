<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Expert Teachers</h1>
            <hr class="border-[#ccc] mb-[2rem]">
            
            <!-- Search Bar -->
            <div class="w-full rounded-lg bg-base py-[.5rem] px-[1.5rem] flex gap-[2rem] items-center">
                <input 
                    v-model="searchQuery"
                    @input="debouncedSearch"
                    class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none [@media(max-width:550px)]:text-[1rem]"
                    type="text"
                    placeholder="Search teachers..."
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

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadTeachers"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Try Again
                </button>
            </div>

            <!-- Teachers Grid -->
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] mt-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                <!-- Become a Teacher Card -->
                <div v-if="showBecomingTeacher" class="bg-base rounded-lg p-[2rem] text-center hover:shadow-lg transition-shadow duration-300">
                    <h3 class="text-[2rem] text-text_dark capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">
                        Become A Teacher
                    </h3>
                    <p class="leading-7 py-[.5rem] text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">
                        Step into a network of passionate educators, innovators, and mentors. Share your insights and make a meaningful difference by helping students achieve their goals.
                    </p>
                    <div class="flex justify-center">
                        <router-link 
                            to="/register"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] mt-[.7rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                        >
                            Get Started
                        </router-link>
                    </div>
                </div>

                <!-- Teacher Cards -->
                <div v-for="teacher in teachers" 
                     :key="teacher.id" 
                     class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-start gap-[1rem] mb-[1.5rem]">
                        <img 
                            :src="teacher.image" 
                            :alt="teacher.name"
                            class="h-[5rem] w-[5rem] object-cover rounded-full [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                        >
                        <div>
                            <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                {{ teacher.name }}
                            </h3>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ teacher.profession }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                            Total playlists: <span class="text-button">{{ teacher.playlist_count }}</span>
                        </p>
                        <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                            Total contents: <span class="text-button">{{ teacher.content_count }}</span>
                        </p>
                    </div>

                    <router-link 
                        :to="'/teacher_profile/' + teacher.id"
                        class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                    >
                        View Profile
                    </router-link>
                </div>
            </div>
        </section>
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

// State
const searchQuery = ref('');
const isSearching = ref(false);
const showBecomingTeacher = ref(true);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const teachers = computed(() => store.getters.getTeachers);
const isLoading = computed(() => store.getters.getTeachersLoading);
const error = computed(() => store.getters.getTeachersError);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const handleSearch = async () => {
    try {
        isSearching.value = true;
        if (!searchQuery.value.trim()) {
            // If search is cleared, reload all teachers
            await store.dispatch('loadTeachers');
        } else {
            // First try to search in existing data
            const existingTeachers = store.getters.getTeachers;
            const filteredTeachers = existingTeachers.filter(teacher => 
                teacher.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                teacher.profession.toLowerCase().includes(searchQuery.value.toLowerCase())
            );

            if (filteredTeachers.length > 0) {
                // If we found results in existing data, use them
                store.commit('setTeachers', filteredTeachers);
            } else {
                // If no results found in existing data, perform API search
                await store.dispatch('searchTeachers', searchQuery.value);
            }
        }
    } catch (err) {
        console.error('Error searching teachers:', err);
    } finally {
        isSearching.value = false;
    }
};

// Debounced search
const debouncedSearch = debounce(() => {
    if (searchQuery.value.trim()) {
        handleSearch();
    } else {
        // If input is empty, reload all teachers
        store.dispatch('loadTeachers');
    }
}, 500);

// Lifecycle
onMounted(async () => {
    await store.dispatch('loadTeachers');
});

// Watch for route changes
watch(() => router.currentRoute.value, async () => {
    await store.dispatch('loadTeachers');
});

// Watch for search query changes
watch(searchQuery, (newValue) => {
    if (!newValue.trim()) {
        // If input is empty, reload all teachers
        store.dispatch('loadTeachers');
    }
});
</script>