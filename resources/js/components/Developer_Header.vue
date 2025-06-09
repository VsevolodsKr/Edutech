<template>
    <header 
        :class="[
            'sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base',
            { 'pl-[22rem]': showSidebar && width > 1180 }
        ]"
    >
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <router-link to="/developer_dashboard">
                <h1 class="text-[2rem] text-text_dark mr-[.7rem] [@media(max-width:970px)]:text-[1.5rem]">
                    EduTech
                </h1>
            </router-link>

            <div class="text-[1.5rem] flex [@media(max-width:970px)]:text-[1rem]">
                <button 
                    @click="toggleSidebar"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-bars"></i>
                </button>

                <button 
                    @click="toggleUserMenu"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-user"></i>
                </button>

                <button 
                    @click="toggleTheme"
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] ml-[.7rem] hover:bg-text_dark hover:text-base transition-colors duration-200 [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i :class="currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon'"></i>
                </button>
            </div>

            <Transition name="menu">
                <div 
                    v-if="showUserMenu && userData"
                    class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] shadow-lg [@media(max-width:550px)]:w-[15rem] [@media(max-width:550px)]:p-[.7rem]"
                >
                    <div class="flex justify-center">
                        <img 
                            :src="getUserImageUrl(userData.image)" 
                            :alt="userData.name"
                            class="h-[8rem] w-[8rem] rounded-full object-cover mb-[1rem] [@media(max-width:550px)]:h-[4rem] [@media(max-width:550px)]:w-[4rem] [@media(max-width:550px)]:mb-[.5rem]"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.1rem]">{{ userData.name }}</h3>
                    <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">{{ userData.profession }}</p>

                    <router-link 
                        to="/developer_profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1rem] transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:py-[.3rem] [@media(max-width:550px)]:mt-[.5rem]"
                    >
                        Skatīt profilu
                    </router-link>

                    <button 
                        @click="handleLogout"
                        :disabled="isLoggingOut"
                        class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:py-[.3rem] [@media(max-width:550px)]:mt-[.5rem]"
                    >
                        <span v-if="isLoggingOut" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Izlogošana...
                        </span>
                        <span v-else>Izlogoties</span>
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

const showUserMenu = ref(false);
const currentTheme = ref(localStorage.getItem('theme-color') || 'theme-light');
const isLoggingOut = ref(false);
const defaultAvatar = '/images/default-avatar.png';

const showSidebar = computed(() => store.getters.getShowSidebar);
const userData = computed(() => store.getters.getUser);

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

const getUserImageUrl = (image) => {
    if (!image) return defaultAvatar;
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;

    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.startsWith('/storage/')) {
        cleanPath = cleanPath.replace('/storage/', '');
    }
    if (cleanPath.startsWith('/')) {
        cleanPath = cleanPath.substring(1);
    }

    return `${window.location.origin}/storage/${cleanPath}`;
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
        showUserMenu.value = false;
        
        await Swal.fire({
            title: 'Veiksmīgi',
            text: 'Izlogojās veiksmīgi',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        
        router.push('/login');
    } catch (err) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās izlogoties. Lūdzu, mēģiniet vēlreiz.',
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

onMounted(() => {
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