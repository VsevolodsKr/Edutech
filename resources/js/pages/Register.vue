<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <!-- Register Form -->
            <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem] max-w-[95%] transition-all duration-300">
                <h3 class="text-[1.5rem] capitalize text-text_dark text-center mb-[1.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Register
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

                <!-- Name Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Your name <span class="text-[#ff0000]">*</span>
                    </label>
                    <input 
                        v-model="name"
                        type="text"
                        placeholder="Enter your name..."
                        :disabled="isLoading"
                        maxlength="50"
                        class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                        :class="{ 'border-button4': validationMessages.length && isError }"
                    >
                </div>

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
                <div class="mb-[1rem]">
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

                <!-- Confirm Password Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Confirm password <span class="text-[#ff0000]">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            v-model="confirmPassword"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            placeholder="Confirm your password..."
                            :disabled="isLoading"
                            maxlength="50"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                            :class="{ 'border-button4': validationMessages.length && isError }"
                        >
                        <button 
                            type="button"
                            @click="toggleConfirmPassword"
                            class="absolute right-3 top-[60%] transform -translate-y-1/2 text-text_light hover:text-text_dark transition-colors"
                        >
                            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="mb-[1.5rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Select image <span class="text-[#ff0000]">*</span>
                    </label>
                    <div class="mt-2 relative">
                        <input 
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            @change="handleImageChange"
                            :disabled="isLoading"
                            class="hidden"
                        >
                        <div 
                            @click="$refs.fileInput.click()"
                            class="cursor-pointer flex items-center justify-center p-4 border-2 border-dashed border-text_light rounded-lg hover:border-button transition-colors"
                        >
                            <div v-if="!selectedImage" class="text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-text_light"></i>
                                <p class="mt-2 text-text_light">Click to upload image</p>
                            </div>
                            <div v-else class="relative w-full">
                                <img 
                                    :src="selectedImage" 
                                    alt="Selected image"
                                    class="w-full h-[200px] object-cover rounded-lg"
                                >
                                <button 
                                    @click.stop="clearImage"
                                    class="absolute top-2 right-2 bg-button4 text-white p-2 rounded-full hover:bg-opacity-80 transition-opacity"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
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
                        Registering...
                    </span>
                    <span v-else>Register</span>
                </button>

                <!-- Login Link -->
                <p class="text-center mt-4 text-text_light">
                    Already have an account? 
                    <router-link to="/login" class="text-button hover:text-button1 transition-colors">
                        Login now
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
import axios from 'axios';

const router = useRouter();
const { width } = useWindowSize();

// State
const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const selectedImage = ref(null);
const fileInput = ref(null);
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
    return name.value.trim() && 
           email.value.trim() && 
           emailRegex.test(email.value) && 
           password.value.length >= 6 && 
           password.value === confirmPassword.value;
});

// Methods
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            validationMessages.value = ['Image size should not exceed 2MB'];
            isError.value = true;
            event.target.value = '';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            validationMessages.value = ['Please select a valid image file (JPEG, PNG, or GIF)'];
            isError.value = true;
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            selectedImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const clearImage = () => {
    selectedImage.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const validateForm = () => {
    const errors = [];

    if (!name.value.trim()) {
        errors.push('Name is required');
    }

    if (!email.value.trim()) {
        errors.push('Email is required');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        errors.push('Please enter a valid email address');
    }

    if (!password.value) {
        errors.push('Password is required');
    } else if (password.value.length < 6) {
        errors.push('Password must be at least 6 characters long');
    }

    if (password.value !== confirmPassword.value) {
        errors.push('Passwords do not match');
    }

    if (!fileInput.value?.files[0]) {
        errors.push('Please select a profile image');
    }

    return errors;
};

const handleSubmit = async () => {
    try {
        validationMessages.value = [];
        isError.value = false;

        // Validate form
        const errors = validateForm();
        if (errors.length > 0) {
            validationMessages.value = errors;
            isError.value = true;
            return;
        }

        isLoading.value = true;

        // Prepare form data
        const formData = new FormData();
        formData.append('name', name.value.trim());
        formData.append('email', email.value.trim());
        formData.append('password', password.value);
        formData.append('conf_password', confirmPassword.value);
        
        if (fileInput.value?.files[0]) {
            formData.append('image', fileInput.value.files[0]);
        }

        // Submit registration request
        const response = await axios.post('api/register/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        validationMessages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message];
        isError.value = false;

        // Redirect to login page
        setTimeout(() => {
            router.push('/login');
        }, 500);
    } catch (err) {
        console.error('Registration error:', err);
        
        validationMessages.value = Array.isArray(err.response?.data?.message)
            ? err.response.data.message
            : [err.response?.data?.message || 'An error occurred during registration. Please try again.'];
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