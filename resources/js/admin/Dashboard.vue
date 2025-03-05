<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Dashboard</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                    <!-- Welcome Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Welcome back!</h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            {{ adminData.name }}
                        </div>
                        <router-link 
                            to="/admin_profile" 
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            View Profile
                        </router-link>
                    </div>

                    <!-- Contents Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">
                            {{ stats.totalContents }}
                        </h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            Total Contents
                        </div>
                        <router-link 
                            to="/admin_contents" 
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Manage Contents
                        </router-link>
                    </div>

                    <!-- Playlists Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">
                            {{ stats.totalPlaylists }}
                        </h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            Total Playlists
                        </div>
                        <router-link 
                            to="/admin_playlists" 
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Manage Playlists
                        </router-link>
                    </div>

                    <!-- Likes Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">
                            {{ stats.totalLikes }}
                        </h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            Total Likes
                        </div>
                        <router-link 
                            to="/admin_likes" 
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            View Likes
                        </router-link>
                    </div>

                    <!-- Comments Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">
                            {{ stats.totalComments }}
                        </h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            Total Comments
                        </div>
                        <router-link 
                            to="/admin_comments" 
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            View Comments
                        </router-link>
                    </div>

                    <!-- Quick Links Card -->
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Quick Links</h2>
                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                            Manage Your Content
                        </div>
                        <div class="w-full flex gap-[.5rem] pt-[.5rem]">
                            <router-link 
                                to="/admin_add_content" 
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Add Content
                            </router-link>
                            <router-link 
                                to="/admin_add_playlist" 
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Add Playlist
                            </router-link>
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
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import store from '../store/store';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';

const router = useRouter();
const { width } = useWindowSize();

// State
const adminData = ref(null);
const isLoading = ref(true);
const stats = ref({
    totalContents: 0,
    totalPlaylists: 0,
    totalLikes: 0,
    totalComments: 0
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadAdminData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/admin_login');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        // Check if user is an admin
        if (!response.data.is_admin) {
            await Swal.fire({
                title: 'Access Denied',
                text: 'You do not have permission to access the admin dashboard.',
                icon: 'error'
            });
            router.push('/');
            return;
        }

        adminData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading admin data:', err);
        if (err.response?.status === 401) {
            router.push('/admin_login');
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'Failed to load admin data. Please try again.',
                icon: 'error'
            });
        }
    }
};

const loadStats = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/admin_login');
            return;
        }

        const [contentsResponse, playlistsResponse] = await Promise.all([
            axios.get('/api/contents/amount/' + adminData.value.id, {
                headers: { Authorization: `Bearer ${token}` }
            }),
            axios.get('/api/playlists/amount/' + adminData.value.id, {
                headers: { Authorization: `Bearer ${token}` }
            })
        ]);

        stats.value = {
            totalContents: contentsResponse.data,
            totalPlaylists: playlistsResponse.data.data,
            totalLikes: 0, // TODO: Add API endpoint for likes count
            totalComments: 0 // TODO: Add API endpoint for comments count
        };
    } catch (err) {
        console.error('Error loading stats:', err);
        if (err.response?.status === 401) {
            router.push('/admin_login');
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'Failed to load statistics. Please try again.',
                icon: 'error'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

// Lifecycle hooks
onMounted(async () => {
    await loadAdminData();
    if (adminData.value) {
        await loadStats();
    }
});
</script>