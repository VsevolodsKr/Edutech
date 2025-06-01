<template>
    <Transition name="slide">
        <div v-if="showSidebar" class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120 [@media(max-width:550px)]:w-[15rem]">
            <div v-if="isLoading" class="py-[3rem] px-[2rem] text-center">
                <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                <p class="text-text_light">Ielādē...</p>
            </div>

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

            <template v-if="!isLoading && adminData">
                <div class="py-[3rem] px-[2rem] text-center">
                    <div class="flex justify-center">
                        <img 
                            :src="adminData.image" 
                            :alt="adminData.name"
                            class="h-[9rem] w-[9rem] rounded-full object-cover mb-[1rem] [@media(max-width:550px)]:h-[4rem] [@media(max-width:550px)]:w-[4rem]"
                            @error="handleImageError"
                        >
                    </div>
                    <h3 class="text-[1.5rem] text-text_dark overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">
                        {{ adminData.name }}
                    </h3>
                    <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">
                        {{ adminData.profession }}
                    </p>
                    <router-link 
                        to="/admin_profile"
                        class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Skatīt profilu
                    </router-link>
                </div>
            </template>

            <template v-if="!isLoading && !adminData">
                <div class="py-[2rem] px-[2rem]">
                    <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">
                        Lūdzu, ielogojieties, lai piekļūtu vadības panelim
                    </h3>
                    <div class="w-full pt-[.5rem] mb-[2rem]">
                        <router-link 
                            to="/login"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Ielogoties
                        </router-link>
                    </div>
                </div>
            </template>

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
                            Izlogošana...
                        </span>
                        <span v-else>Izlogoties</span>
                    </span>
                </button>
            </nav>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

const isLoading = ref(false);
const isLoggingOut = ref(false);

const adminData = computed(() => {
    const storedUser = store.getters.getUser;
    if (!storedUser) return null;

    let imageUrl = storedUser.image;
    if (!imageUrl) {
        imageUrl = '/storage/default-avatar.png';
    } else if (!imageUrl.startsWith('data:') && !imageUrl.startsWith('http')) {
        let cleanPath = imageUrl;
        if (cleanPath.includes('/storage/app/public/')) {
            cleanPath = cleanPath.replace('/storage/app/public/', '');
        } else if (cleanPath.startsWith('/storage/')) {
            cleanPath = cleanPath.replace('/storage/', '');
        }
        
        cleanPath = cleanPath.replace(/^\/+/, '').replace(/\/+$/, '');
        
        if (!cleanPath.startsWith('uploads/')) {
            cleanPath = `uploads/${cleanPath}`;
        }

        imageUrl = `/storage/${cleanPath}`;
    }

    return {
        ...storedUser,
        image: imageUrl,
        _updateKey: Date.now()
    };
});

const navigationItems = [
    { path: '/dashboard', icon: 'fa fa-home', label: 'Vadības panelis' },
    { path: '/admin_playlists', icon: 'fa-solid fa-bars-staggered', label: 'Kursi' },
    { path: '/admin_contents', icon: 'fa fa-graduation-cap', label: 'Video' },
    { path: '/admin_comments', icon: 'fas fa-comment', label: 'Komentāri' }
];

const showSidebar = computed(() => store.getters.getShowSidebar);

const toggleSidebar = () => {
    store.commit('setShowSidebar', false);
};

const handleLogout = async () => {
    try {
        isLoggingOut.value = true;
        localStorage.removeItem('token');
        store.commit('setUser', null); 
        router.push('/login');
    } catch (err) {
        console.log(err);
    } finally {
        isLoggingOut.value = false;
    }
};

watch(width, (value) => {
    store.commit('setShowSidebar', value >= 1180);
});

watch(() => store.getters.getUser?._updateKey, () => {
    adminData.value = {
        ...adminData.value,
        _updateKey: Date.now()
    };
});

const handleImageError = (event) => {
    if (!event.target.src.includes('default-avatar.png')) {
        event.target.src = '/storage/default-avatar.png';
    }
};

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