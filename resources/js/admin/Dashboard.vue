<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Dashboard</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <!-- Welcome Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Welcome back!</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">{{ teacherData?.name }}</div>
                    <router-link to="/admin_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Profile</router-link>
                </div>

                <!-- Contents Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.contents }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Contents</div>
                    <router-link to="/admin_contents" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add New Content</router-link>
                </div>

                <!-- Playlists Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.playlists }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Playlists</div>
                    <router-link to="/admin_playlists" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add New Playlist</router-link>
                </div>

                <!-- Likes Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.likes }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Likes</div>
                    <button class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Likes</button>
                </div>

                <!-- Comments Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.comments }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Comments</div>
                    <button class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Comments</button>
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
const teacherData = ref(null);
const statistics = ref({
    contents: 0,
    playlists: 0,
    likes: 0,
    comments: 0
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadTeacherData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });
        teacherData.value = response.data;

        // Load statistics
        const [playlists, contents] = await Promise.all([
            axios.get(`/api/playlists/amount/${teacherData.value.id}`),
            axios.get(`/api/contents/amount/${teacherData.value.id}`)
        ]);

        statistics.value = {
            playlists: playlists.data.data || 0,
            contents: contents.data || 0,
            likes: 0, // TODO: Add API endpoint for likes count
            comments: 0 // TODO: Add API endpoint for comments count
        };
    } catch (error) {
        console.error('Error loading teacher data:', error);
        if (error.response?.status === 401) {
            router.push('/');
        }
    }
};

// Lifecycle
onMounted(() => {
    loadTeacherData();
});
</script>