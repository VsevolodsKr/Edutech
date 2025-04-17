<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark capitalize">Profils</h1>
                    <router-link 
                        to="/update-profile"
                        class="bg-button text-base border-2 border-button px-4 py-2 rounded-lg hover:bg-button1 transition-colors duration-200 hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:px-3 [@media(max-width:550px)]:py-1"
                    >
                        Rediģēt profilu
                    </router-link>
                </div>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>

                <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                    {{ error }}
                    <button 
                        @click="loadUserData"
                        class="block mx-auto mt-4 text-button hover:text-text_dark"
                    >
                        Mēģināt vēlreiz
                    </button>
                </div>

                <div v-else class="flex items-center justify-center">
                    <div class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div class="flex justify-center mb-6">
                            <img 
                                :src="userData.image"
                                :alt="userData.name"
                                class="w-32 h-32 rounded-full object-cover border-4 border-button"
                            >
                        </div>

                        <div class="space-y-6">
                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Vārds
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.name || 'Vārda nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    E-pasts
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.email || 'E-pasta nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Loma
                                </h3>
                                <p class="text-[1.2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">
                                    students
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Izveidots
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.created_at }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const { width } = useWindowSize();

const isLoading = ref(true);
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);

const userData = computed(() => {
    try {
        const user = store.getters.getUser;
        console.log('Raw user data from store:', user);
        
        if (!user) {
            console.log('No user data found in store');
            return {
                name: 'Vārda nav',
                email: 'E-pasta nav',
                image: '/images/avatar.png',
                created_at: 'Nav pieejams'
            };
        }

        let imageUrl = '/images/avatar.png';
        console.log('Original image path:', user.image);
        
        if (user.image) {
            try {
                imageUrl = new URL(user.image, import.meta.url);
                console.log('Processed image URL:', imageUrl);
            } catch (err) {
                console.error('Error processing image URL:', err);
                imageUrl = '/images/avatar.png';
            }
        } else {
            console.log('No image path provided, using default avatar');
        }

        const result = {
            name: user.name || 'Vārda nav',
            email: user.email || 'E-pasta nav',
            image: imageUrl,
            created_at: user.created_at ? formatDate(user.created_at) : 'Nav pieejams'
        };
        console.log('Final userData object:', result);
        return result;
    } catch (err) {
        console.error('Error accessing user data:', err);
        return {
            name: 'Vārda nav',
            email: 'E-pasta nav',
            image: '/images/avatar.png',
            created_at: 'Nav pieejams'
        };
    }
});

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const formatDate = (dateString) => {
    if (!dateString) return 'Nav pieejams';
    try {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        return `${day}.${month}.${year}`;
    } catch (err) {
        console.error('Error formatting date:', err);
        return 'Nav pieejams';
    }
};

const loadUserData = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            console.error('No token found');
            router.push('/login');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.data) {
            store.commit('setUser', response.data);
            console.log('User data loaded:', response.data);
        } else {
            console.error('No data in response');
            error.value = 'No user data received';
        }
    } catch (err) {
        console.error('Error loading user data:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/login');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else {
            error.value = 'Failed to load profile data. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadUserData();
});
</script> 