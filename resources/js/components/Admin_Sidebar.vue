<template>
    <Transition name="slide">
        <div 
            v-if="showSidebar" 
            class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120 [@media(max-width:550px)]:w-[15rem]"
        >
            <!-- Close Button (Mobile) -->
            <div class="text-right p-[2rem] hidden [@media(max-width:1180px)]:block">
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
            <template v-if="adminData">
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
            <template v-else>
                <div class="py-[2rem] px-[2rem]">
                    <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                        Please login or register
                    </h3>
                    <div class="w-full flex gap-[.5rem] pt-[.5rem] mb-[2rem]">
                        <router-link 
                            to="/login"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Login
                        </router-link>
                        <router-link 
                            to="/register"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Register
                        </router-link>
                    </div>
                </div>
            </template>

            <!-- Navigation Menu -->
            <nav class="mt-[1rem]">
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
                    class="block w-full p-[2rem] text-[1.3rem] text-left [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"
                >
                    <span class="text-text_light hover:text-button hover:border-b hover:border-button hover:pb-[.5rem] transition-colors duration-200">
                        <i class="fa-solid fa-right-from-bracket mr-[1.5rem]"></i>
                        Logout
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

const router = useRouter();
const { width } = useWindowSize();

// State
const adminData = ref(null);

// Navigation items
const navigationItems = [
    { path: '/dashboard', icon: 'fa fa-home', label: 'Home' },
    { path: '/admin_playlists', icon: 'fa-solid fa-bars-staggered', label: 'Playlists' },
    { path: '/admin_contents', icon: 'fa fa-graduation-cap', label: 'Contents' },
    { path: '/teachers', icon: 'fas fa-comment', label: 'Comments' }
];

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

// Methods
const loadAdminData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) return;

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        adminData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading admin data:', err);
    }
};

const toggleSidebar = () => {
    store.commit('setShowSidebar', false);
};

const handleLogout = () => {
    localStorage.removeItem('token');
    store.commit('setUser', null);
    adminData.value = null;
    router.push('/login');
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