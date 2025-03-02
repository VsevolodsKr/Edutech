<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark capitalize">Profile</h1>
                    <router-link 
                        to="/update-profile"
                        class="bg-button text-base border-2 border-button px-4 py-2 rounded-lg hover:bg-button1 transition-colors duration-200 hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:px-3 [@media(max-width:550px)]:py-1"
                    >
                        Edit Profile
                    </router-link>
                </div>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                    {{ error }}
                    <button 
                        @click="loadUserData"
                        class="block mx-auto mt-4 text-button hover:text-text_dark"
                    >
                        Try Again
                    </button>
                </div>

                <!-- Profile Content -->
                <div v-else class="flex items-center justify-center">
                    <div class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <!-- Profile Picture -->
                        <div class="flex justify-center mb-6">
                            <img 
                                :src="userData.image"
                                :alt="userData.name"
                                class="w-32 h-32 rounded-full object-cover border-4 border-button"
                            >
                        </div>

                        <!-- User Information -->
                        <div class="space-y-6">
                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Full Name
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.name || 'Not set' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Email Address
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.email || 'Not set' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Role
                                </h3>
                                <p class="text-[1.2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">
                                    Student
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Account Created
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

// State
const isLoading = ref(true);
const error = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);

const userData = computed(() => {
    try {
        const user = store.getters.getUser?.data || {};
        return {
            name: user.name || 'Not set',
            email: user.email || 'Not set',
            image: user.image ? new URL(user.image, import.meta.url) : '/images/avatar.png',
            created_at: user.created_at ? formatDate(user.created_at) : 'Not available'
        };
    } catch (err) {
        console.error('Error accessing user data:', err);
        return {
            name: 'Not set',
            email: 'Not set',
            image: '/images/avatar.png',
            created_at: 'Not available'
        };
    }
});

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const formatDate = (dateString) => {
    if (!dateString) return 'Not available';
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (err) {
        console.error('Error formatting date:', err);
        return 'Not available';
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
        } else {
            console.error('No data in response');
            error.value = 'No user data received';
        }
    } catch (err) {
        console.error('Error loading user data:', err);
        if (err.response?.status === 401) {
            router.push('/login');
        } else {
            error.value = 'Failed to load profile data. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

// Lifecycle
onMounted(() => {
    loadUserData();
});
</script> 