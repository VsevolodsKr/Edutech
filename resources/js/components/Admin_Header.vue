<template>
    <header 
        :class="[
            'sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base',
            { 'pl-[20rem]': showSidebar && width > 1180 }
        ]"
    >
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <!-- Logo -->
            <router-link to="/dashboard">
                <h1 class="text-[2rem] text-text_dark mr-[.7rem] [@media(max-width:970px)]:text-[1.5rem]">
                    Admin
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
                    class="absolute top-[120%] right-[1rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] shadow-lg"
                >
                    <!-- Admin Profile -->
                    <div class="flex justify-center">
                        <img 
                            :src="adminData.image" 
                            :alt="adminData.name"
                            class="h-[8rem] w-[8rem] rounded-full object-cover mb-[1rem]"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">
                        {{ adminData.name }}
                    </h3>
                    <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">
                        {{ adminData.profession || 'Administrator' }}
                    </p>

                    <!-- Profile Link -->
                    <router-link 
                        to="/profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1rem] transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Profile
                    </router-link>

                    <!-- Auth Links -->
                    <div class="flex gap-[1rem] mt-[1rem]">
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

                    <!-- Logout Button -->
                    <button 
                        @click="handleLogout"
                        class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button4 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Logout
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

const router = useRouter();
const { width } = useWindowSize();

// State
const adminData = ref(null);
const showUserMenu = ref(false);
const currentTheme = ref(localStorage.getItem('theme-color') || 'theme-light');

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
        button3: '#abd032',
        selection: '#749af8b9'
    },
    dark: {
        background: '#777',
        base: '#434343',
        text_dark: '#fff',
        text_light: '#ddd',
        button: '#eae883',
        button3: '#ea7d10',
        selection: '#eae883b9'
    }
};

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

const handleLogout = () => {
    localStorage.removeItem('token');
    store.commit('setUser', null);
    adminData.value = null;
    showUserMenu.value = false;
    router.push('/login');
};

const handleClickOutside = (event) => {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu && !userMenu.contains(event.target) && !event.target.closest('button')) {
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