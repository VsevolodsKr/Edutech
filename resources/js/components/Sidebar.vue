<template>
    <Transition name="slide">
        <div 
            v-if="showSidebar" 
            class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120 [@media(max-width:550px)]:w-[15rem]"
        >
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

            <div v-if="isLoading" class="py-[3rem] px-[2rem] text-center">
                <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                <p class="text-text_light">Ielādē...</p>
            </div>

            <template v-else-if="user">
                <div class="py-[3rem] px-[2rem] text-center">
                    <div class="flex justify-center">
                        <img 
                            :src="user.image" 
                            :alt="user.name"
                            @error="handleImageError"
                            class="h-[9rem] w-[9rem] rounded-full object-cover mb-[1rem] [@media(max-width:550px)]:h-[4rem] [@media(max-width:550px)]:w-[4rem]"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">
                        {{ user.name }}
                    </h3>
                    <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">
                        {{ user.role || 'students' }}
                    </p>
                    <router-link 
                        to="/profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Skatīt profilu
                    </router-link>
                </div>
            </template>

            <template v-else>
                <div class="py-[2rem] px-[2rem]">
                    <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                        Lūdzu ielogojieties vai reģistrējieties
                    </h3>
                    <div class="w-full flex gap-[.5rem] pt-[.5rem] mb-[2rem]">
                        <router-link 
                            to="/login"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Ielogoties
                        </router-link>
                        <router-link 
                            to="/register"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Reģistrēties
                        </router-link>
                    </div>
                </div>
            </template>

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
                    v-if="user"
                    @click="handleLogout"
                    :disabled="isLoggingOut"
                    class="block w-full p-[2rem] text-[1.3rem] text-left [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"
                >
                    <span class="text-text_light hover:text-button hover:border-b hover:border-button hover:pb-[.5rem] transition-colors duration-200 flex items-center">
                        <i class="fa-solid fa-right-from-bracket mr-[1.5rem]"></i>
                        <span v-if="isLoggingOut" class="flex items-center gap-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-button"></div>
                            Izlogošana...
                        </span>
                        <span v-else>Iziet</span>
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
const isLoggingOut = ref(false);
const isLoading = ref(false);

// Navigation items
const navigationItems = [
    { path: '/', icon: 'fa fa-home', label: 'Sākumlapa' },
    { path: '/about', icon: 'fa fa-question', label: 'Par mums' },
    { path: '/courses', icon: 'fa fa-graduation-cap', label: 'Kursi' },
    { path: '/teachers', icon: 'fas fa-chalkboard-user', label: 'Pasniedzēji' },
    { path: '/contact', icon: 'fas fa-handshake', label: 'Sazinies ar mums' }
];

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const user = computed(() => {
    const storedUser = store.getters.getUser;
    if (!storedUser) return null;

    // Format image URL
    let imageUrl = storedUser.image;
    if (imageUrl) {
        if (imageUrl.startsWith('http')) {
            // Keep external URLs as is
        } else if (imageUrl.includes('/storage/app/public/') || imageUrl.includes('storage/app/public/')) {
            // Clean up storage path
            imageUrl = imageUrl.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '');
            imageUrl = `/storage/${imageUrl}`;
        } else if (!imageUrl.startsWith('/storage/')) {
            // Add storage prefix if missing
            imageUrl = `/storage/${imageUrl}`;
        }
    } else {
        imageUrl = '/storage/default-avatar.png';
    }

    return {
        ...storedUser,
        image: imageUrl
    };
});
const storeIsLoading = computed(() => store.getters.getIsLoading);

// Methods
const toggleSidebar = () => {
    store.commit('setShowSidebar', false);
};

const handleImageError = (event) => {
    if (!event.target.src.includes('default-avatar.png')) {
        event.target.src = '/storage/default-avatar.png';
    }
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        localStorage.removeItem('token');
        store.commit('setUser', null);
        await router.push('/login');
    } catch (err) {
        console.error('Error during logout:', err);
    } finally {
        isLoggingOut.value = false;
    }
};

// Watchers
watch(width, (value) => {
    store.commit('setShowSidebar', value >= 1180);
});

watch(storeIsLoading, (newValue) => {
    isLoading.value = newValue;
});

// Initialize
onMounted(() => {
    // No need to load user data here as it's already in the store
    isLoading.value = false;
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