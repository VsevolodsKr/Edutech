<template>
    <Transition name="slide">
        <div 
            v-if="showSidebar" 
            class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120 [@media(max-width:550px)]:w-[15rem]"
        >
            <!-- Loading State -->
            <div v-if="isLoading" class="py-[3rem] px-[2rem] text-center">
                <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                <p class="text-text_light">Loading...</p>
            </div>

            <!-- Close Button (Mobile) -->
            <div v-else class="text-right p-[2rem] hidden [@media(max-width:1180px)]:block">
                <div class="flex justify-end">
                    <button 
                        @click="toggleSidebar"
                        class="flex items-center justify-center text-[#fff] text-[1.5rem] bg-button4 rounded-lg h-[3rem] w-[3rem] hover:bg-text_dark hover:text-base transition-colors duration-200"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Admin Profile Section -->
            <template v-if="!isLoading && adminData">
                <div class="py-[3rem] px-[2rem] text-center">
                    <div class="flex justify-center">
                        <img 
                            :src="adminData.image" 
                            :alt="adminData.name"
                            class="h-[9rem] w-[9rem] rounded-full object-cover mb-[1rem] [@media(max-width:550px)]:h-[4rem] [@media(max-width:550px)]:w-[4rem]"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">
                        {{ adminData.name }}
                    </h3>
                    <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">
                        {{ adminData.profession || 'Administrator' }}
                    </p>
                    <router-link 
                        to="/admin_profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Profile
                    </router-link>
                </div>
            </template>

            <!-- Guest Section -->
            <template v-if="!isLoading && !adminData">
                <div class="py-[2rem] px-[2rem]">
                    <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                        Please login to access admin panel
                    </h3>
                    <div class="w-full pt-[.5rem] mb-[2rem]">
                        <router-link 
                            to="/login"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Login
                        </router-link>
                    </div>
                </div>
            </template>

            <!-- Navigation Menu -->
            <nav v-if="!isLoading" class="mt-[1rem]">
                <router-link 
                    v-for="(item, index) in navigationItems" 
                    :key="index"
                    :to="item.path"
                    class="block p-[2rem] text-[1.3rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"
                    :class="{ 'pt-[.5rem] pb-[2rem]': index === 0 }"
                >
                    <span class="text-text_light hover:text-button hover:border-b hover:border-button hover:pb-[.5rem] transition-colors duration-200">
                        <i :class="[item.icon, 'mr-[1.5rem]']"></i>
                        {{ item.label }}
                    </span>
                </router-link>

                <button 
                    v-if="adminData"
                    @click="handleLogout"
                    :disabled="isLoggingOut"
                    class="block w-full p-[2rem] text-[1.3rem] text-left [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"
                >
                    <span class="text-text_light hover:text-button hover:border-b hover:border-button hover:pb-[.5rem] transition-colors duration-200 flex items-center">
                        <i class="fa-solid fa-right-from-bracket mr-[1.5rem]"></i>
                        <span v-if="isLoggingOut" class="flex items-center gap-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-button"></div>
                            Logging out...
                        </span>
                        <span v-else>Logout</span>
                    </span>
                </button>
            </nav>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import store from '../store/store';
import Swal from 'sweetalert2';

const router = useRouter();
const { width } = useWindowSize();

// State
const adminData = ref(null);
const isLoading = ref(true);
const isLoggingOut = ref(false);

// Navigation items
const navigationItems = [
    { path: '/dashboard', icon: 'fa fa-home', label: 'Dashboard' },
    { path: '/admin_playlists', icon: 'fa-solid fa-bars-staggered', label: 'Playlists' },
    { path: '/admin_contents', icon: 'fa fa-graduation-cap', label: 'Contents' },
    { path: '/admin_comments', icon: 'fas fa-comment', label: 'Comments' }
];

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

// Methods
const loadAdminData = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('token');
        const storedUser = localStorage.getItem('user');
        
        if (!token || !storedUser) {
            router.push('/login');
            return;
        }

        const user = JSON.parse(storedUser);
        if (!user.profession) {
            await Swal.fire({
                title: 'Access Denied',
                text: 'You do not have permission to access the admin dashboard.',
                icon: 'error'
            });
            router.push('/');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log('Raw response data:', response.data);
        console.log('Image path from response:', response.data.image);

        // Set admin data
        const imageUrl = response.data.image ? 
            (response.data.image.startsWith('http') ? 
                response.data.image : 
                `${window.location.origin}/storage/uploads/${response.data.image.split('/').pop()}`) :
            `${window.location.origin}/images/default-avatar.png`;

        console.log('Final image URL:', imageUrl);

        adminData.value = {
            ...response.data,
            image: imageUrl
        };

        // Update stored user data
        localStorage.setItem('user', JSON.stringify(response.data));
        store.commit('setUser', response.data);
    } catch (err) {
        console.error('Error loading admin data:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            router.push('/login');
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'Failed to load admin data. Please try again.',
                icon: 'error'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const toggleSidebar = () => {
    store.commit('setShowSidebar', false);
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        store.commit('setUser', null);
        adminData.value = null;
        
        await Swal.fire({
            title: 'Success',
            text: 'Logged out successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        
        router.push('/login');
    } catch (err) {
        console.error('Error during logout:', err);
        await Swal.fire({
            title: 'Error',
            text: 'Failed to logout. Please try again.',
            icon: 'error'
        });
    } finally {
        isLoggingOut.value = false;
    }
};

// Watchers
watch(width, (value) => {
    store.commit('setShowSidebar', value >= 1180);
});

// Lifecycle hooks
onMounted(() => {
    loadAdminData();
});
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}
</style>