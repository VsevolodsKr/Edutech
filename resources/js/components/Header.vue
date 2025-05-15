<template>
    <header v-if="showSidebar && width >= 1180" class="sticky z-[120] top-0 right-0 left-0 pl-[22rem] border-b-2 bg-base">
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <router-link to="/">
                <h1 class="text-[2rem] text-text_dark mr-[.7rem]">EduTech</h1>
            </router-link>
            
            <div class="text-[1.5rem] flex">
                <button 
                    @click="toggleSidebar" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"
                >
                    <i class="fa fa-bars"></i>
                </button>
                <button 
                    @click="show = !show" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"
                >
                    <i class="fa fa-user"></i>
                </button>
                <button 
                    @click="switchTheme()" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"
                >
                    <i :class="[currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon']"></i>
                </button>
            </div>

            <div 
                v-if="show && user" 
                class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100"
            >
                <div class="flex justify-center">
                    <img :src="user.image" class="h-[8rem] w-[8rem] rounded-[50%] object-contain mb-[1rem]">
                </div>
                <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap">{{ user.name }}</h3>
                <p class="text-[1.3rem] text-text_light">students</p>
                <router-link 
                    to="/profile" 
                    class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base mb-[1rem]"
                >
                    Skatīt profilu
                </router-link>
                <button 
                    @click="logout" 
                    class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base"
                >
                    Iziet
                </button>
            </div>

            <div 
                v-if="show && !user" 
                class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100"
            >
                <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                    Lūdzu ielogojieties vai reģistrējieties
                </h3>
                <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem]">
                    <router-link 
                        to="/login" 
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Ielogojies
                    </router-link>
                    <router-link 
                        to="/register" 
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Reģistrēties
                    </router-link>
                </div>
            </div>
        </section>
    </header>

    <header 
        v-else 
        class="sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base"
    >
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <router-link to="/">
                <h1 class="text-[2rem] text-text_dark mr-[.7rem] [@media(max-width:970px)]:text-[1.5rem]">
                    EduTech
                </h1>
            </router-link>
            <div class="text-[1.5rem] flex [@media(max-width:970px)]:text-[1rem]">
                <button 
                    @click="toggleSidebar" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-bars"></i>
                </button>
                <button 
                    @click="show = !show" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i class="fa fa-user"></i>
                </button>
                <button 
                    @click="switchTheme()" 
                    class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"
                >
                    <i :class="[currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon']"></i>
                </button>
            </div>

            <div 
                v-if="show && user" 
                class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100"
            >
                <div class="flex justify-center">
                    <img :src="user.image" class="h-[8rem] w-[8rem] rounded-[50%] object-contain mb-[1rem]">
                </div>
                <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap">{{ user.name }}</h3>
                <p class="text-[1.3rem] text-text_light">students</p>
                <router-link 
                    to="/profile" 
                    class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base mb-[1rem]"
                >
                    Skatīt profilu
                </router-link>
                <button 
                    @click="logout" 
                    class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base"
                >
                    Iziet
                </button>
            </div>

            <div 
                v-if="show && !user" 
                class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100"
            >
                <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                    Lūdzu ielogojieties vai reģistrējieties
                </h3>
                <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem]">
                    <router-link 
                        to="/login" 
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Ielogojies
                    </router-link>
                    <router-link 
                        to="/register" 
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Reģistrēties
                    </router-link>
                </div>
            </div>
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
const show = ref(false);
const user = ref(null);
const currentTheme = ref(localStorage.getItem('theme-color') || 'theme-light');

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

// Methods
const toggleSidebar = () => {
    store.commit('setShowSidebar', !showSidebar.value);
};

const switchTheme = () => {
    const newTheme = currentTheme.value === 'theme-light' ? 'theme-dark' : 'theme-light';
    currentTheme.value = newTheme;
    localStorage.setItem('theme-color', newTheme);
    applyTheme(newTheme === 'theme-light' ? themes.light : themes.dark);
};

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

const applyTheme = (theme) => {
    Object.entries(theme).forEach(([key, value]) => {
        document.documentElement.style.setProperty(`--${key}`, value);
    });
};

const loadUserData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) return;

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        user.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading user data:', err);
    }
};

const logout = () => {
    localStorage.removeItem('token');
    store.commit('setUser', null);
    user.value = null;
    show.value = false;
    router.push('/login');
};

const handleClickOutside = (event) => {
    if (show.value && !event.target.closest('.user-menu') && !event.target.closest('button')) {
        show.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    loadUserData();
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