<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Admin Profile</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <svg class="animate-spin h-12 w-12 text-button" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Admin Info Card -->
                <div class="bg-base rounded-lg p-[2rem]">
                    <div class="text-center mb-6">
                        <img 
                            :src="adminData.image || defaultAvatar" 
                            :alt="adminData.name"
                            class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-button"
                        >
                        <h2 class="text-[2rem] text-text_dark mb-2">{{ adminData.name }}</h2>
                        <p class="text-[1.3rem] text-text_light">Administrator</p>
                    </div>

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-background rounded-lg p-4 text-center">
                            <h3 class="text-[1.2rem] text-text_dark mb-2">Total Teachers</h3>
                            <p class="text-[2rem] text-button font-bold">{{ statistics.totalTeachers }}</p>
                        </div>
                        <div class="bg-background rounded-lg p-4 text-center">
                            <h3 class="text-[1.2rem] text-text_dark mb-2">Total Students</h3>
                            <p class="text-[2rem] text-button font-bold">{{ statistics.totalStudents }}</p>
                        </div>
                        <div class="bg-background rounded-lg p-4 text-center">
                            <h3 class="text-[1.2rem] text-text_dark mb-2">Total Courses</h3>
                            <p class="text-[2rem] text-button font-bold">{{ statistics.totalCourses }}</p>
                        </div>
                        <div class="bg-background rounded-lg p-4 text-center">
                            <h3 class="text-[1.2rem] text-text_dark mb-2">Total Contents</h3>
                            <p class="text-[2rem] text-button font-bold">{{ statistics.totalContents }}</p>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Form -->
                <div class="bg-base rounded-lg p-[2rem]">
                    <h3 class="text-[1.5rem] text-text_dark mb-6">Update Profile</h3>
                    
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div v-if="error" class="bg-[#fcb6b6] text-[#912020] rounded-xl p-4 mb-4">
                            <p class="[@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Profile Picture
                            </label>
                            <input 
                                type="file"
                                ref="fileInput"
                                accept="image/*"
                                @change="handleFileChange"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Full Name <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.name"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="Enter your full name..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Email Address <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.email"
                                type="email"
                                required
                                maxlength="50"
                                placeholder="Enter your email..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Old Password
                            </label>
                            <input 
                                v-model="formData.old_password"
                                type="password"
                                placeholder="Enter old password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                New Password
                            </label>
                            <input 
                                v-model="formData.new_password"
                                type="password"
                                placeholder="Enter new password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <div>
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Confirm Password
                            </label>
                            <input 
                                v-model="formData.confirm_password"
                                type="password"
                                placeholder="Confirm new password..."
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                :disabled="isSubmitting"
                            >
                        </div>

                        <button 
                            type="submit"
                            :disabled="isSubmitting"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.8rem] block w-full transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:py-[.5rem] [@media(max-width:550px)]:text-[.7rem]"
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
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const { width } = useWindowSize();

// Constants
const defaultAvatar = '/images/default-avatar.png';

// State
const adminData = ref({
    name: '',
    email: '',
    image: null
});
const statistics = ref({
    totalTeachers: 0,
    totalStudents: 0,
    totalCourses: 0,
    totalContents: 0
});
const formData = ref({
    name: '',
    email: '',
    old_password: '',
    new_password: '',
    confirm_password: '',
    image: null
});
const error = ref(null);
const isLoading = ref(true);
const isSubmitting = ref(false);
const fileInput = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadAdminData = async () => {
    try {
        const [profileResponse, statsResponse] = await Promise.all([
            axios.get('/api/admin/profile'),
            axios.get('/api/admin/statistics')
        ]);

        adminData.value = profileResponse.data;
        statistics.value = statsResponse.data;
        
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

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.image = file;
        const reader = new FileReader();
        reader.onload = e => {
            adminData.value.image = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const validateForm = () => {
    if (!formData.value.name.trim()) {
        error.value = 'Name is required';
        return false;
    }
    if (!formData.value.email.trim()) {
        error.value = 'Email is required';
        return false;
    }
    if (formData.value.new_password && formData.value.new_password !== formData.value.confirm_password) {
        error.value = 'New password and confirmation do not match';
        return false;
    }
    if (formData.value.new_password && !formData.value.old_password) {
        error.value = 'Old password is required when setting a new password';
        return false;
    }
    return true;
};

const handleSubmit = async () => {
    try {
        error.value = null;
        if (!validateForm()) {
            return;
        }

        isSubmitting.value = true;
        const data = new FormData();
        Object.entries(formData.value).forEach(([key, value]) => {
            if (value !== null && value !== '') {
                data.append(key, value);
            }
        });

        const response = await axios.post('/api/admin/update-profile', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        await Swal.fire({
            title: 'Success!',
            text: 'Your profile has been updated successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });

        // Reset password fields
        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        
        // Reload admin data to reflect changes
        await loadAdminData();
    } catch (err) {
        console.error('Update error:', err);
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

// Lifecycle
onMounted(() => {
    loadAdminData();
});
</script>

<style scoped>
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border-radius: 0.5rem;
    background-color: var(--background);
    color: var(--text-light);
}

.error-message {
    color: #dc2626;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}
</style>