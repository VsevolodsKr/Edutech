<template>
    <header 
        :class="[
            'sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base',
            { 'pl-[22rem]': showSidebar && width > 1180 }
        ]"
    >
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <!-- Logo -->
            <router-link to="/dashboard">
                <h1 class="text-[2rem] text-text_dark mr-[.7rem] [@media(max-width:970px)]:text-[1.5rem]">
                    EduTech Admin
                </h1>
            </router-link>

            <!-- Action Buttons -->
            <div class="text-[1.5rem] flex [@media(max-width:970px)]:text-[1rem]">
                <!-- Sidebar Toggle -->
                <button 
                    @click="toggleSidebar"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-bars"></i>
                </button>

                <!-- User Menu Toggle -->
                <button 
                    @click="toggleUserMenu"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-user"></i>
                </button>

                <!-- Theme Toggle -->
                <button 
                    @click="toggleTheme"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i :class="currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon'"></i>
                </button>
            </div>

            <!-- User Menu -->
            <Transition name="menu">
                <div 
                    v-if="showUserMenu && adminData"
                    class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] shadow-lg"
                >
                    <!-- Admin Profile -->
                    <div class="flex justify-center">
                        <img 
                            :src="adminData.image" 
                            :alt="adminData.name"
                            class="h-[8rem] w-[8rem] rounded-full object-cover mb-[1rem]"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap">{{ adminData.name }}</h3>
                    <p class="text-[1.3rem] text-text_light">Administrator</p>

                    <!-- Profile Link -->
                    <router-link 
                        to="/admin_profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1rem] transition-all duration-200 hover:bg-transparent hover:text-button"
                    >
                        View Profile
                    </router-link>

                    <!-- Logout Button -->
                    <button 
                        @click="handleLogout"
                        :disabled="isLoggingOut"
                        class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isLoggingOut" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Logging out...
                        </span>
                        <span v-else>Logout</span>
                    </button>
                </div>
            </Transition>
        </section>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import store from '../store/store';
import Swal from 'sweetalert2';

const router = useRouter();
const { width } = useWindowSize();

// State
const adminData = ref(null);
const showUserMenu = ref(false);
const currentTheme = ref(localStorage.getItem('theme-color') || 'theme-light');
const isLoggingOut = ref(false);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

// Theme configurations
const themes = {
    light: {
        background: '#ddd',
        base: '#fff',
        text_dark: '#000',
        text_light: '#777',
        button: '#7499f8',
        button2: '#abd032',
        button3: '#ea7d10',
        button4: '#e83731',
        selection: '#749af8b9'
    },
    dark: {
        background: '#777',
        base: '#434343',
        text_dark: '#fff',
        text_light: '#ddd',
        button: '#eae883',
        button2: '#abd032',
        button3: '#ea7d10',
        button4: '#e83731',
        selection: '#eae883b9'
    }
};

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

        adminData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading admin data:', err);
        await Swal.fire({
            title: 'Error',
            text: 'Failed to load admin data. Please try again.',
            icon: 'error'
        });
    }
};

const toggleSidebar = () => {
    store.commit('setShowSidebar', !showSidebar.value);
};

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

const toggleTheme = () => {
    const newTheme = currentTheme.value === 'theme-light' ? 'theme-dark' : 'theme-light';
    currentTheme.value = newTheme;
    localStorage.setItem('theme-color', newTheme);
    applyTheme(newTheme === 'theme-light' ? themes.light : themes.dark);
};

const applyTheme = (theme) => {
    Object.entries(theme).forEach(([key, value]) => {
        document.documentElement.style.setProperty(`--${key}`, value);
    });
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        localStorage.removeItem('token');
        store.commit('setUser', null);
        adminData.value = null;
        showUserMenu.value = false;
        
        await Swal.fire({
            title: 'Success',
            text: 'Logged out successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        
        router.push('/admin_login');
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

const handleClickOutside = (event) => {
    if (showUserMenu.value && !event.target.closest('.user-menu') && !event.target.closest('button')) {
        showUserMenu.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    loadAdminData();
    applyTheme(currentTheme.value === 'theme-light' ? themes.light : themes.dark);
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.menu-enter-active,
.menu-leave-active {
    transition: all 0.3s ease;
}

.menu-enter-from,
.menu-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>