<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark capitalize">Update Profile</h1>
                    <router-link 
                        to="/profile"
                        class="bg-background text-text_dark px-4 py-2 rounded-lg hover:bg-base transition-colors duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:px-3 [@media(max-width:550px)]:py-1"
                    >
                        Back to Profile
                    </router-link>
                </div>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div v-if="errorList.length" class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]">
                            <p v-for="(error, index) in errorList" 
                               :key="index" 
                               class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Profile Picture
                            </label>
                            <input 
                                type="file"
                                ref="fileInput"
                                accept="image/*"
                                @change="handleFileChange"
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                            <img 
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Profile Preview"
                                class="mt-4 w-32 h-32 rounded-full object-cover"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Full Name <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.name"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="Enter your full name..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Email Address <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.email"
                                type="email"
                                required
                                maxlength="50"
                                placeholder="Enter your email..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Old Password
                            </label>
                            <input 
                                v-model="formData.old_password"
                                type="password"
                                placeholder="Enter old password..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                New Password
                            </label>
                            <input 
                                v-model="formData.new_password"
                                type="password"
                                placeholder="Enter new password..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Confirm Password
                            </label>
                            <input 
                                v-model="formData.confirm_password"
                                type="password"
                                placeholder="Confirm new password..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <button 
                            type="submit"
                            :disabled="isLoading"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            {{ isLoading ? 'Updating...' : 'Update Profile' }}
                        </button>
                    </form>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const formData = ref({
    name: '',
    email: '',
    old_password: '',
    new_password: '',
    confirm_password: '',
    image: null
});
const errorList = ref([]);
const isLoading = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.image = file;
        const reader = new FileReader();
        reader.onload = e => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const loadUserData = async () => {
    try {
        const response = await axios.get('/api/user/profile');
        const { name, email, image } = response.data;
        formData.value.name = name;
        formData.value.email = email;
        if (image) {
            imagePreview.value = `/storage/${image}`;
        }
    } catch (error) {
        console.error('Error loading user data:', error);
        errorList.value = ['Failed to load user data'];
    }
};

const handleSubmit = async () => {
    try {
        isLoading.value = true;
        errorList.value = [];

        // Validate password fields only if any password field is filled
        const isChangingPassword = formData.value.old_password || formData.value.new_password || formData.value.confirm_password;
        
        if (isChangingPassword) {
            // Check if all password fields are filled when changing password
            if (!formData.value.old_password || !formData.value.new_password || !formData.value.confirm_password) {
                errorList.value = ['All password fields are required when changing password'];
                return;
            }
            // Validate passwords match
            if (formData.value.new_password !== formData.value.confirm_password) {
                errorList.value = ['New password and confirmation do not match'];
                return;
            }
        }

        const data = new FormData();
        
        // Always include name and email
        data.append('name', formData.value.name);
        data.append('email', formData.value.email);
        
        // Only include password fields if changing password
        if (isChangingPassword) {
            data.append('old_password', formData.value.old_password);
            data.append('new_password', formData.value.new_password);
            data.append('confirm_password', formData.value.confirm_password);
        }
        
        // Only include image if a new file was selected
        if (formData.value.image instanceof File) {
            data.append('image', formData.value.image);
        }

        await axios.post('/api/user/update-profile', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        await Swal.fire({
            title: 'Success!',
            text: 'Your profile has been updated successfully',
            icon: 'success'
        });

        // Reset password fields
        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        
        // Navigate back to profile view
        router.push('/profile');
    } catch (error) {
        if (error.response?.data?.errors) {
            // Handle validation errors from the server
            errorList.value = Object.values(error.response.data.errors).flat();
        } else {
            errorList.value = [error.response?.data?.message || 'An error occurred while updating your profile'];
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