<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Admin Profile</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                
                <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[2rem]">
                    <!-- Admin Info -->
                    <div class="bg-base rounded-lg p-[2rem]">
                        <div class="text-center mb-6">
                            <img 
                                :src="adminData.image || defaultAvatar" 
                                :alt="adminData.name"
                                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover"
                            >
                            <h2 class="text-[2rem] text-text_dark mb-2">{{ adminData.name }}</h2>
                            <p class="text-[1.3rem] text-text_light">Administrator</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.totalTeachers }}</h3>
                                <p class="text-[1.2rem] text-text_light">Teachers</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.totalStudents }}</h3>
                                <p class="text-[1.2rem] text-text_light">Students</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.totalCourses }}</h3>
                                <p class="text-[1.2rem] text-text_light">Courses</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.totalContents }}</h3>
                                <p class="text-[1.2rem] text-text_light">Contents</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Update Profile Form -->
                    <div class="bg-base rounded-lg p-[2rem]">
                        <h3 class="text-[1.5rem] text-text_dark mb-4">Update Profile</h3>
                        
                        <form @submit.prevent="handleSubmit">
                            <div v-if="errorList.length" class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem] p-2">
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
                                :disabled="isSubmitting"
                                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                {{ isSubmitting ? 'Updating...' : 'Update Profile' }}
                            </button>
                        </form>
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
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
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
const errorList = ref([]);
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
    } catch (error) {
        console.error('Error loading admin data:', error);
        errorList.value = ['Failed to load admin data'];
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

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        errorList.value = [];

        // Validate passwords match if new password is being set
        if (formData.value.new_password && formData.value.new_password !== formData.value.confirm_password) {
            errorList.value = ['New password and confirmation do not match'];
            return;
        }

        const data = new FormData();
        Object.entries(formData.value).forEach(([key, value]) => {
            if (value !== null && value !== '') {
                data.append(key, value);
            }
        });

        await axios.post('/api/admin/update-profile', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        Swal.fire({
            title: 'Success!',
            text: 'Your profile has been updated successfully',
            icon: 'success'
        });

        // Reset password fields
        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        
        // Reload admin data to reflect changes
        await loadAdminData();
    } catch (error) {
        errorList.value = error.response?.data?.message || ['An error occurred while updating your profile'];
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle
onMounted(() => {
    loadAdminData();
});
</script>