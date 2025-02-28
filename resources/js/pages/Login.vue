<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <!-- Login Form -->
            <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem] max-w-[95%] transition-all duration-300">
                <h3 class="text-[1.5rem] capitalize text-text_dark text-center mb-[1.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Login Now
                </h3>

                <!-- Validation Messages -->
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

                <!-- Email Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Your email <span class="text-[#ff0000]">*</span>
                    </label>
                    <input 
                        v-model="email"
                        type="email" 
                        placeholder="Enter your email..."
                        :disabled="isLoading"
                        maxlength="50"
                        class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                        :class="{ 'border-button4': validationMessages.length && isError }"
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-[1.5rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Your password <span class="text-[#ff0000]">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter your password..."
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

                <!-- Submit Button -->
                <button 
                    type="submit"
                    :disabled="isLoading || !isFormValid"
                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.8rem] block w-full transition-all duration-200 hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:py-[.5rem] [@media(max-width:550px)]:text-[.7rem]"
                >
                    <span v-if="isLoading" class="flex items-center justify-center gap-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-base"></div>
                        Logging in...
                    </span>
                    <span v-else>Login</span>
                </button>

                <!-- Register Link -->
                <p class="text-center mt-4 text-text_light">
                    Don't have an account? 
                    <router-link to="/register" class="text-button hover:text-button1 transition-colors">
                        Register now
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

// State
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const validationMessages = ref([]);
const isError = ref(false);

// Computed
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

// Methods
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const handleSubmit = async () => {
    try {
        validationMessages.value = [];
        isError.value = false;
        isLoading.value = true;

        // Client-side validation
        if (!email.value.trim()) {
            validationMessages.value.push('Email is required');
            isError.value = true;
            return;
        }

        if (!password.value) {
            validationMessages.value.push('Password is required');
            isError.value = true;
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('email', email.value.trim());
        formData.append('password', password.value);

        // Submit login request
        const response = await axios.post('api/login/send', formData);

        // Handle successful login
        localStorage.setItem('token', response.data.token);
        store.commit('setUser', response.data.data);

        validationMessages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message];
        isError.value = false;

        // Redirect based on user role
        setTimeout(() => {
            router.push(response.data.is_teacher ? '/dashboard' : '/', response.data);
        }, 500);
    } catch (err) {
        console.error('Login error:', err);
        
        validationMessages.value = Array.isArray(err.response?.data?.message)
            ? err.response.data.message
            : [err.response?.data?.message || 'An error occurred during login. Please try again.'];
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