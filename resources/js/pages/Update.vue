<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <!-- Update Form -->
            <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem] max-w-[95%] transition-all duration-300">
                <h3 class="text-[1.5rem] capitalize text-text_dark text-center mb-[1.5rem] [@media(max-width:550px)]:text-[1.2rem]">
                    Update Profile
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
                        Update name
                    </label>
                    <input 
                        v-model="name"
                        type="text"
                        :placeholder="userData?.name"
                        :disabled="isLoading"
                        maxlength="50"
                        class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                        :class="{ 'border-button4': validationMessages.length && isError }"
                    >
                </div>

                <!-- Email Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Update email
                    </label>
                    <input 
                        v-model="email"
                        type="email"
                        :placeholder="userData?.email"
                        :disabled="isLoading"
                        maxlength="50"
                        class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                        :class="{ 'border-button4': validationMessages.length && isError }"
                    >
                </div>

                <!-- Previous Password Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Previous password
                    </label>
                    <div class="relative">
                        <input 
                            v-model="prevPassword"
                            :type="showPrevPassword ? 'text' : 'password'"
                            placeholder="Enter your old password..."
                            :disabled="isLoading"
                            maxlength="50"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                            :class="{ 'border-button4': validationMessages.length && isError }"
                        >
                        <button 
                            type="button"
                            @click="togglePrevPassword"
                            class="absolute right-3 top-[60%] transform -translate-y-1/2 text-text_light hover:text-text_dark transition-colors"
                        >
                            <i :class="showPrevPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        New password
                    </label>
                    <div class="relative">
                        <input 
                            v-model="newPassword"
                            :type="showNewPassword ? 'text' : 'password'"
                            placeholder="Enter your new password..."
                            :disabled="isLoading"
                            maxlength="50"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:ring-2 focus:ring-button transition-shadow duration-200 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.7rem]"
                            :class="{ 'border-button4': validationMessages.length && isError }"
                        >
                        <button 
                            type="button"
                            @click="toggleNewPassword"
                            class="absolute right-3 top-[60%] transform -translate-y-1/2 text-text_light hover:text-text_dark transition-colors"
                        >
                            <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-[1rem]">
                    <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                        Confirm password
                    </label>
                    <div class="relative">
                        <input 
                            v-model="confirmPassword"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            placeholder="Confirm your new password..."
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
                        Update image
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
                            <div v-if="!selectedImage && !userData?.image" class="text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-text_light"></i>
                                <p class="mt-2 text-text_light">Click to upload image</p>
                            </div>
                            <div v-else class="relative w-full">
                                <img 
                                    :src="selectedImage || userData?.image" 
                                    alt="Profile image"
                                    class="w-full h-[200px] object-cover rounded-lg"
                                >
                                <button 
                                    v-if="selectedImage"
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
                        Updating...
                    </span>
                    <span v-else>Update</span>
                </button>
            </form>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const userData = ref(null);
const name = ref('');
const email = ref('');
const prevPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const showPrevPassword = ref(false);
const showNewPassword = ref(false);
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
    if (!name.value && !email.value && !prevPassword.value && !newPassword.value && !selectedImage.value) {
        return false;
    }

    if (prevPassword.value && (!newPassword.value || !confirmPassword.value)) {
        return false;
    }

    if (newPassword.value && (!prevPassword.value || !confirmPassword.value)) {
        return false;
    }

    if (newPassword.value && newPassword.value !== confirmPassword.value) {
        return false;
    }

    if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        return false;
    }

    return true;
});

// Methods
const togglePrevPassword = () => {
    showPrevPassword.value = !showPrevPassword.value;
};

const toggleNewPassword = () => {
    showNewPassword.value = !showNewPassword.value;
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

    if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        errors.push('Please enter a valid email address');
    }

    if (newPassword.value && newPassword.value.length < 6) {
        errors.push('New password must be at least 6 characters long');
    }

    if (newPassword.value && newPassword.value !== confirmPassword.value) {
        errors.push('New passwords do not match');
    }

    if (newPassword.value && !prevPassword.value) {
        errors.push('Please enter your current password');
    }

    return errors;
};

const loadUserData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        userData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };
    } catch (err) {
        console.error('Error loading user data:', err);
        router.push('/');
    }
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
        if (name.value) formData.append('name', name.value.trim());
        if (email.value) {
            formData.append('old_email', userData.value.email);
            formData.append('email', email.value.trim());
        }
        if (prevPassword.value) formData.append('p_password', prevPassword.value);
        if (newPassword.value) {
            formData.append('n_password', newPassword.value);
            formData.append('c_password', confirmPassword.value);
        }
        if (fileInput.value?.files[0]) {
            formData.append('image', fileInput.value.files[0]);
        }

        // Submit update request
        const response = await axios.post('api/update/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        validationMessages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message];
        isError.value = false;

        // Update store and redirect
        store.commit('setUser', response.data.data);
        setTimeout(() => {
            router.push('/');
        }, 500);
    } catch (err) {
        console.error('Update error:', err);
        
        validationMessages.value = Array.isArray(err.response?.data?.message)
            ? err.response.data.message
            : [err.response?.data?.message || 'An error occurred during update. Please try again.'];
        isError.value = true;
    } finally {
        isLoading.value = false;
    }
};

// Initialize
onMounted(() => {
    loadUserData();
});
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