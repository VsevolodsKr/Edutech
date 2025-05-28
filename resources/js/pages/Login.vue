<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem] max-w-[95%] transition-all duration-300">
                <h3 class="text-[1.5rem] capitalize text-text_dark text-center mb-[1.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Ielogoties
                </h3>

                <TransitionGroup 
                    name="list" 
                    tag="ul" 
                    v-if="validationMessages.length"
                    :class="[
                        isError ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]',
                        'rounded-xl mb-[1rem] overflow-hidden'
                    ]"
                >
                    <li 
                        v-for="message in validationMessages" 
                        :key="message"
                        class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem] flex items-center gap-2"
                    >
                        <i :class="[isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle']"></i>
                        {{ message }}
                    </li>
                </TransitionGroup>

                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Jūsu e-pasts <span class="text-[#ff0000]">*</span>
                    </label>
                    <input 
                        v-model="email"
                        type="email" 
                        placeholder="Ievadiet savu e-pastu..."
                        :disabled="isLoading"
                        maxlength="50"
                        class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                        :class="{ 'border-button4': validationMessages.length && isError }"
                    >
                </div>

                <div class="mb-[1.5rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Jūsu parole <span class="text-[#ff0000]">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Ievadiet savu paroli..."
                            :disabled="isLoading"
                            maxlength="50"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                            :class="{ 'border-button4': validationMessages.length && isError }"
                        >
                        <button 
                            type="button"
                            @click="togglePassword"
                            class="absolute right-3 top-[60%] transform -translate-y-1/2 text-text_light hover:text-text_dark transition-colors"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button 
                    type="submit"
                    :disabled="isLoading || !isFormValid"
                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.8rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:py-[.5rem] [@media(max-width:550px)]:text-[.7rem]"
                >
                    <span v-if="isLoading" class="flex items-center justify-center gap-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-base"></div>
                        Ielogojoties...
                    </span>
                    <span v-else>Ielogoties</span>
                </button>

                <p class="text-center mt-4 text-text_light">
                    Nav jums konts? 
                    <router-link to="/register" class="text-button hover:text-button1 transition-colors">
                        Reģistrēties
                    </router-link>
                </p>
            </form>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const validationMessages = ref([]);
const isError = ref(false);

const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const isFormValid = computed(() => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return email.value.trim() && emailRegex.test(email.value) && password.value.length >= 6;
});

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const handleSubmit = async () => {
    try {
        isLoading.value = true;
        validationMessages.value = [];
        isError.value = false;

        const formData = new FormData();
        formData.append('email', email.value);
        formData.append('password', password.value);

        const response = await axios.post('/api/login/send', formData);

        if (response.data.status === 200) {
            localStorage.setItem('token', response.data.token);
            
            const userData = {
                ...response.data.data,
                is_teacher: response.data.is_teacher,
                is_developer: response.data.is_developer,
                encrypted_id: response.data.data.encrypted_id
            };
            localStorage.setItem('user', JSON.stringify(userData));
            
            store.commit('setUser', userData);
            store.commit('setUserLoaded', true);
            store.commit('setLastUserLoad', Date.now());
            
            let targetRoute = '/';
            if (response.data.is_developer) {
                const redirectTo = localStorage.getItem('developerRedirectTo');
                if (redirectTo) {
                    localStorage.removeItem('developerRedirectTo');
                    targetRoute = redirectTo;
                } else {
                    targetRoute = '/developer_dashboard';
                }
            } else if (response.data.is_teacher) {
                const redirectTo = localStorage.getItem('adminRedirectTo');
                if (redirectTo) {
                    localStorage.removeItem('adminRedirectTo');
                    targetRoute = redirectTo;
                } else {
                    targetRoute = '/dashboard';
                }
            }
            
            if (userData.encrypted_id) {
                await store.dispatch('loadUserStats', userData.encrypted_id);
            }
            
            await router.push(targetRoute);
        } else {
            validationMessages.value = [response.data.message];
            isError.value = true;
        }
    } catch (error) {
        console.error('Login error:', error);
        if (error.response?.data?.message) {
            validationMessages.value = [error.response.data.message];
        } else {
            validationMessages.value = ['Nederīgs e-pasts vai parole'];
        }
        isError.value = true;
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>