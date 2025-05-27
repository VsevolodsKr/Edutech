<template>
    <div>
    <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <!-- Dashboard section for authenticated users -->
                <div v-if="isAuthenticated">
                    <h1 class="text-[1.5rem] text-text_dark">Vadības panelis</h1>
                    <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                    <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                    </div>

                    <div v-else-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
                        <p class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ error }}
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                        <div class="bg-base rounded-lg p-[2rem] w-full">
                            <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                                <img 
                                    :src="user?.image" 
                                    :alt="user?.name"
                                    class="h-[4rem] w-[4rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                                >
                                <div>
                                    <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">
                                        {{ user?.name }}
                                    </h3>
                                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                        {{ user?.profession || 'students' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-[1.5rem] mb-[2rem]">
                                <div 
                                    v-for="(stat, index) in statistics" 
                                    :key="index"
                                    class="bg-background rounded-lg p-[1rem] text-center hover:shadow-md transition-shadow duration-300 relative group"
                                >
                                    <h3 class="text-[1.5rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1.2rem]">
                                        {{ stat.value }}
                                    </h3>
                                    <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] mb-2">
                                        {{ stat.label }}
                                    </p>
                                    <router-link 
                                        v-if="stat.link"
                                        :to="stat.link"
                                        class="text-button text-sm hover:text-text_dark transition-colors duration-200 opacity-0 group-hover:opacity-100 absolute bottom-2 left-1/2 transform -translate-x-1/2"
                                    >
                                        Skatīt visu
                                    </router-link>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-[1rem]">
                                <button 
                                    @click="$router.push('/update-profile')" 
                                    class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                                >
                                    Rediģēt profilu
                                </button>
                                <button 
                                    @click="handleLogout" 
                                    :disabled="isLoggingOut"
                                    class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                                >
                                    <span v-if="isLoggingOut" class="flex items-center justify-center gap-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-base"></div>
                                        Izlogošana...
                                    </span>
                                    <span v-else>Iziet</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest courses section - visible to all users -->
                <div class="mt-8">
                    <h2 class="text-[1.5rem] text-text_dark mb-4 [@media(max-width:550px)]:text-[1.2rem]">
                        {{ isAuthenticated ? 'Jaunākie kursi' : 'Pieejamie kursi' }}
                    </h2>
                    <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                    <div v-if="isPlaylistsLoading" class="flex justify-center items-center min-h-[20vh]">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                    </div>

                    <div v-else-if="playlistsError" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                        {{ playlistsError }}
                        <button 
                            @click="store.dispatch('loadLatestPlaylists')" 
                            class="block mx-auto mt-4 text-button hover:text-text_dark"
                        >
                            Mēģināt vēlreiz
                        </button>
                    </div>

                    <div v-else-if="latestPlaylists.length === 0" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                        Nav pieejami kursi
                    </div>

                    <!-- Playlists Grid with pagination -->
                    <div v-else>
                        <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                            <div v-for="playlist in latestPlaylists" 
                                :key="playlist.id" 
                                class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300">
                                <!-- Teacher Info with lazy loading -->
                                <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                                    <img 
                                        :data-src="formatTeacherImage(playlist.teacher?.image)" 
                                        :alt="playlist.teacher?.name"
                                        class="h-[4rem] w-[4rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                                        :src="formatTeacherImage(playlist.teacher?.image)"
                                        @error="$event.target.src = '/storage/default-avatar.png'"
                                    >
                                    <div>
                                        <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem]">
                                            {{ playlist.teacher?.name || 'Unknown Teacher' }}
                                        </h3>
                                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                            {{ formatDate(playlist.date) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Course Thumbnail with lazy loading -->
                                <div class="relative group">
                                    <img 
                                        :data-src="playlist.thumb" 
                                        :alt="playlist.title"
                                        class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                                        :src="playlist.thumb"
                                        @error="$event.target.src = '/storage/default-thumbnail.png'"
                                    >
                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                        <router-link 
                                            :to="isAuthenticated ? '/playlist/' + playlist.encrypted_id : '/login'"
                                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-transparent"
                                        >
                                            Skatīt kursu
                                        </router-link>
                                    </div>
                                    <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-black bg-opacity-60 text-white text-[1rem] [@media(max-width:550px)]:text-[.7rem]">
                                        {{ playlist.content_count }} video
                                    </span>
                                </div>

                                <!-- Course Info -->
                                <div class="mt-4">
                                    <h3 class="text-[1.5rem] text-text_dark mb-2 [@media(max-width:550px)]:text-[1.2rem]">
                                        {{ playlist.title }}
                                    </h3>
                                    <p v-if="playlist.description" class="text-text_light text-[1rem] mb-4 line-clamp-2">
                                        {{ playlist.description }}
                                    </p>
                                    <router-link 
                                        :to="isAuthenticated ? '/playlist/' + playlist.encrypted_id : '/login'"
                                        class="inline-block bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] px-[1rem] transition hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem]"
                                    >
                                        Sākt mācīties
                                    </router-link>
                                </div>
                            </div>
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
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const isLoggingOut = ref(false);
const error = ref(null);
const isAuthenticated = computed(() => store.getters.getUser !== null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const user = computed(() => {
    const storedUser = store.getters.getUser;
    if (!storedUser) return null;

    // Format the image URL
    const imageUrl = storedUser.image ? 
        (storedUser.image.startsWith('http') ? 
            storedUser.image : 
            `/storage/${storedUser.image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`) :
        `/storage/default-avatar.png`;

    return {
        ...storedUser,
        image: imageUrl,
        profession: storedUser.profession || 'students'
    };
});
const isLoading = computed(() => store.getters.getIsLoading);
const dashboardStats = computed(() => store.getters.getDashboardStats);
const latestPlaylists = computed(() => store.getters.getLatestPlaylists);
const isPlaylistsLoading = computed(() => store.getters.getLatestPlaylistsLoading);
const playlistsError = computed(() => store.getters.getLatestPlaylistsError);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const statistics = computed(() => {
    const stats = store.getters.getDashboardStats;
    return [
        { value: parseInt(stats?.likes) || 0, label: 'Favorītvideo', link: '/likes' },
        { value: parseInt(stats?.playlists) || 0, label: 'Grāmatzīmes', link: '/bookmarks' },
        { value: parseInt(stats?.comments) || 0, label: 'Komentāri', link: '/comments' }
    ];
});

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
};

const formatTeacherImage = (image) => {
    if (!image) return `${window.location.origin}/storage/default-avatar.png`;
    
    return image.startsWith('http') ? 
        image : 
        `${window.location.origin}/storage/${image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`;
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        localStorage.removeItem('token');
        store.commit('setUser', null);
        await router.push('/login');
    } catch (error) {
        console.error('Error during logout:', error);
    } finally {
        isLoggingOut.value = false;
    }
};

// Watchers
watch(width, (value) => {
    store.commit('setShowSidebar', value >= 1180);
});

watch(() => store.getters.getDashboardStats, (newStats, oldStats) => {
    console.log('Dashboard stats changed:', {
        old: oldStats,
        new: newStats
    });
}, { deep: true });

onMounted(async () => {
    try {
        // Load latest playlists
        await store.dispatch('loadLatestPlaylists');
        
        // Load user data only if not already loaded
        if (!store.getters.isCacheValid) {
            await store.dispatch('clearAndLoadUserData');
        }
    } catch (error) {
        console.error('Error in Home.vue setup:', error);
    }
});

// Watch for user changes - simplified since stats are now part of profile
watch(() => store.getters.getUser, async (newUser) => {
    console.log('User changed:', newUser);
}, { deep: true });

// Watch for dashboard stats changes
watch(() => store.getters.getDashboardStats, (newStats) => {
    console.log('Dashboard stats updated:', newStats);
}, { deep: true });

// Watch for route changes - simplified to avoid redundant calls
watch(() => router.currentRoute.value.path, async (newPath, oldPath) => {
    if (newPath === '/' && newPath !== oldPath && !store.getters.isCacheValid) {
        await store.dispatch('clearAndLoadUserData');
    }
});
</script>