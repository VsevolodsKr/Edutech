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
                                :src="getImageUrl(adminData.image)" 
                                :alt="adminData.name"
                                @error="handleImageError"
                                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover"
                            >
                            <h2 class="text-[2rem] text-text_dark mb-2">{{ adminData.name }}</h2>
                            <p class="text-[1.3rem] text-text_light">{{ adminData.profession }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.playlists }}</h3>
                                <p class="text-[1.2rem] text-text_light">Playlists</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.contents }}</h3>
                                <p class="text-[1.2rem] text-text_light">Contents</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.likes }}</h3>
                                <p class="text-[1.2rem] text-text_light">Likes</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.comments }}</h3>
                                <p class="text-[1.2rem] text-text_light">Comments</p>
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
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// Constants
const defaultAvatar = '/images/default-avatar.png';

// State
const adminData = ref({
    name: '',
    email: '',
    profession: '',
    image: null
});
const statistics = ref({
    playlists: 0,
    contents: 0,
    likes: 0,
    comments: 0
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
        isLoading.value = true;
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const headers = {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        };

        // Fetch user data and statistics in parallel
        const [profileResponse, statsResponse] = await Promise.all([
            axios.get('/api/user', { headers }),
            axios.get('/api/admin/statistics', { headers })
        ]);

        // Handle user data
        if (!profileResponse.data.profession) {
            throw new Error('Unauthorized access');
        }

        console.log('API Response Image:', profileResponse.data.image);

        // Clean up the image path before storing
        let imagePath = profileResponse.data.image;
        if (imagePath && imagePath.includes('/storage/app/public/')) {
            imagePath = imagePath.replace('/storage/app/public/', '');
        }

        adminData.value = {
            ...profileResponse.data,
            image: imagePath
        };

        console.log('Stored Image URL:', adminData.value.image);

        // Handle statistics
        statistics.value = {
            playlists: await fetchPlaylistCount(profileResponse.data.id),
            contents: await fetchContentCount(profileResponse.data.id),
            likes: statsResponse.data.likes || 0,
            comments: statsResponse.data.comments || 0
        };

        // Initialize form data
        formData.value.name = adminData.value.name;
        formData.value.email = adminData.value.email;
    } catch (error) {
        console.error('Error loading admin data:', error);
        if (error.response?.status === 401 || error.message === 'Unauthorized access') {
            localStorage.removeItem('token');
            router.push('/');
        } else {
            errorList.value = ['Failed to load admin data'];
        }
    } finally {
        isLoading.value = false;
    }
};

const fetchPlaylistCount = async (userId) => {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`/api/playlists/amount/${userId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        return response.data.data || 0;
    } catch (error) {
        console.error('Error fetching playlist count:', error);
        return 0;
    }
};

const fetchContentCount = async (userId) => {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`/api/contents/amount/${userId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        return response.data || 0;
    } catch (error) {
        console.error('Error fetching content count:', error);
        return 0;
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            errorList.value = ['Image size should not exceed 2MB'];
            event.target.value = '';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errorList.value = ['Please select a valid image file (JPEG, PNG, or GIF)'];
            event.target.value = '';
            return;
        }

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

        // Password validation
        const hasOldPassword = !!formData.value.old_password;
        const hasNewPassword = !!formData.value.new_password;
        const hasConfirmPassword = !!formData.value.confirm_password;

        // Check if any password field is filled
        if (hasOldPassword || hasNewPassword || hasConfirmPassword) {
            // If any password field is filled, all password fields must be filled
            if (!hasOldPassword || !hasNewPassword || !hasConfirmPassword) {
                errorList.value = ['All password fields must be filled to change password'];
                return;
            }
            // Check if new passwords match
            if (formData.value.new_password !== formData.value.confirm_password) {
                errorList.value = ['New password and confirmation do not match'];
                return;
            }
        }

        // Create FormData object
        const data = new FormData();

        // Add basic fields if they have changed
        if (formData.value.name !== adminData.value.name) {
            data.append('name', formData.value.name);
        }
        if (formData.value.email !== adminData.value.email) {
            data.append('email', formData.value.email);
        }

        // Add password fields only if all password fields are filled
        if (hasOldPassword && hasNewPassword && hasConfirmPassword) {
            data.append('old_password', formData.value.old_password);
            data.append('new_password', formData.value.new_password);
            data.append('confirm_password', formData.value.confirm_password);
        }

        // Add image only if a new one is selected
        if (formData.value.image instanceof File) {
            data.append('image', formData.value.image);
        }

        // Only proceed with the update if there are changes
        if ([...data.entries()].length === 0) {
            errorList.value = ['No changes to update'];
            return;
        }

        const token = localStorage.getItem('token');
        const response = await axios.post('/api/admin/update-profile', data, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });

        // Update the store with new user data
        store.commit('setUser', response.data.user);

        // Reset form fields
        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        if (fileInput.value) {
            fileInput.value.value = '';
        }
        formData.value.image = null;

        // Update local admin data
        await loadAdminData();

        // Emit an event to update the sidebar
        store.commit('setReloadSidebar', true);

        await Swal.fire({
            title: 'Success!',
            text: 'Your profile has been updated successfully',
            icon: 'success'
        });
    } catch (error) {
        console.error('Error updating profile:', error);
        if (error.response?.data?.message) {
            errorList.value = Array.isArray(error.response.data.message) 
                ? error.response.data.message 
                : [error.response.data.message];
        } else {
            errorList.value = ['An error occurred while updating your profile'];
        }
    } finally {
        isSubmitting.value = false;
    }
};

const getImageUrl = (image) => {
    console.log('Input Image:', image);
    if (!image) {
        console.log('No image, using default:', defaultAvatar);
        return defaultAvatar;
    }
    
    if (image.startsWith('data:')) {
        console.log('Data URL detected');
        return image;
    }
    
    if (image.startsWith('http')) {
        console.log('Absolute URL detected');
        return image;
    }

    // Clean up the storage path
    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.includes('storage/app/public/')) {
        cleanPath = cleanPath.replace('storage/app/public/', '');
    }

    // Construct the proper storage URL
    const url = `${window.location.origin}/storage/${cleanPath}`;
    console.log('Final URL:', url);
    return url;
};

const handleImageError = (event) => {
    console.log('Image failed to load, using default avatar');
    event.target.src = defaultAvatar;
};

// Lifecycle
onMounted(() => {
    loadAdminData();
});
</script>