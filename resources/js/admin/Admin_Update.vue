<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Update Profile</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <!-- Error/Success Messages -->
                        <div v-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
                            <p class="[@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>
                        <div v-if="successMessage" class="bg-[#b6f5b5] text-[#378c35] p-4 rounded-lg mb-4">
                            <p class="[@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-check"></i> {{ successMessage }}
                            </p>
                        </div>

                        <!-- Profile Image -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Profile Image
                            </label>
                            <div class="flex items-center gap-4">
                                <img 
                                    :src="imagePreview || adminData.image" 
                                    :alt="adminData.name"
                                    class="h-20 w-20 rounded-full object-cover"
                                >
                                <input 
                                    type="file"
                                    ref="fileInput"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Full Name
                            </label>
                            <input 
                                v-model="formData.name"
                                type="text"
                                placeholder="Enter your name..."
                                required
                                maxlength="50"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Email Address
                            </label>
                            <input 
                                v-model="formData.email"
                                type="email"
                                placeholder="Enter your email..."
                                required
                                maxlength="50"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <!-- Old Password -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Old Password
                            </label>
                            <input 
                                v-model="formData.old_password"
                                type="password"
                                placeholder="Enter old password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <!-- New Password -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                New Password
                            </label>
                            <input 
                                v-model="formData.new_password"
                                type="password"
                                placeholder="Enter new password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label class="text-[1.2rem] text-text_light block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Confirm Password
                            </label>
                            <input 
                                v-model="formData.confirm_password"
                                type="password"
                                placeholder="Confirm new password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            :disabled="isSubmitting"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.8rem] block w-full transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:py-[.5rem] [@media(max-width:550px)]:text-[.8rem]"
                        >
                            <span v-if="isSubmitting" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                            <span v-else>Update Profile</span>
                        </button>
                    </form>
                </div>
            </template>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import store from '../store/store';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';

const router = useRouter();
const { width } = useWindowSize();

// Refs
const fileInput = ref(null);

// State
const adminData = ref(null);
const isLoading = ref(true);
const isSubmitting = ref(false);
const error = ref(null);
const successMessage = ref(null);
const imagePreview = ref(null);
const formData = ref({
    name: '',
    email: '',
    old_password: '',
    new_password: '',
    confirm_password: '',
    image: null
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadAdminData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/admin_login');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        adminData.value = {
            ...response.data,
            image: new URL(response.data.image, import.meta.url)
        };

        // Initialize form data
        formData.value.name = adminData.value.name;
        formData.value.email = adminData.value.email;
    } catch (err) {
        console.error('Error loading admin data:', err);
        error.value = 'Failed to load admin data. Please try again.';
        
        await Swal.fire({
            title: 'Error',
            text: error.value,
            icon: 'error'
        });
    } finally {
        isLoading.value = false;
    }
};

const handleImageChange = (event) => {
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

const validateForm = () => {
    if (!formData.value.name.trim() || !formData.value.email.trim()) {
        error.value = 'Name and email are required.';
        return false;
    }

    if (formData.value.new_password && formData.value.new_password !== formData.value.confirm_password) {
        error.value = 'New password and confirm password do not match.';
        return false;
    }

    return true;
};

const handleSubmit = async (e) => {
    try {
        error.value = null;
        successMessage.value = null;
        
        if (!validateForm()) return;
        
        isSubmitting.value = true;

        const data = new FormData();
        data.append('name', formData.value.name);
        data.append('email', formData.value.email);
        
        if (formData.value.old_password) {
            data.append('old_password', formData.value.old_password);
            data.append('new_password', formData.value.new_password);
            data.append('confirm_password', formData.value.confirm_password);
        }
        
        if (formData.value.image) {
            data.append('image', formData.value.image);
        }

        const response = await axios.post('/api/admin/update', data, {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        });

        if (response.data.status === 200) {
            successMessage.value = 'Profile updated successfully!';
            
            // Reset password fields
            formData.value.old_password = '';
            formData.value.new_password = '';
            formData.value.confirm_password = '';
            
            // Update admin data
            await loadAdminData();
            
            await Swal.fire({
                title: 'Success',
                text: successMessage.value,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            throw new Error(response.data.message || 'Failed to update profile');
        }
    } catch (err) {
        console.error('Error updating profile:', err);
        error.value = err.response?.data?.message || 'Failed to update profile. Please try again.';
        
        await Swal.fire({
            title: 'Error',
            text: error.value,
            icon: 'error'
        });
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    loadAdminData();
});
</script>