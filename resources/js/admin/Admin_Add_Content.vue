<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Upload Content</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>
            
            <div v-else class="flex items-center justify-center">
                <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[1rem] w-[50rem]">
                    <!-- Error/Success Messages -->
                    <div v-if="errorList.length" 
                         :class="[status === 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]', 'rounded-xl mb-4']">
                        <p v-for="(error, index) in errorList" 
                           :key="index" 
                           class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                            <i :class="[status === 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Video status <span class="text-button4">*</span>
                        </label>
                        <select 
                            v-model="formData.status"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            required
                        >
                            <option value="">Select status...</option>
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Video title <span class="text-button4">*</span>
                        </label>
                        <input 
                            v-model="formData.title"
                            type="text"
                            placeholder="Enter video title..."
                            required
                            maxlength="50"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Video description <span class="text-button4">*</span>
                        </label>
                        <textarea 
                            v-model="formData.description"
                            placeholder="Enter video description..."
                            required
                            maxlength="1000"
                            rows="10"
                            class="mt-2 h-[20rem] resize-none w-full rounded-lg bg-background p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        ></textarea>
                    </div>

                    <!-- Playlist -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Video playlist <span class="text-button4">*</span>
                        </label>
                        <select 
                            v-model="formData.playlist_id"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            required
                        >
                            <option value="">Select playlist...</option>
                            <option v-for="playlist in playlists" 
                                    :key="playlist.id" 
                                    :value="playlist.id">
                                {{ playlist.title }}
                            </option>
                        </select>
                    </div>

                    <!-- Thumbnail -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Video thumbnail <span class="text-button4">*</span>
                        </label>
                        <input 
                            ref="thumbInput"
                            type="file"
                            accept="image/*"
                            required
                            @change="handleThumbChange"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <!-- Video -->
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                            Select video <span class="text-button4">*</span>
                        </label>
                        <input 
                            ref="videoInput"
                            type="file"
                            accept="video/*"
                            required
                            @change="handleVideoChange"
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        {{ isSubmitting ? 'Uploading...' : 'Upload Content' }}
                    </button>
                </form>
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// Refs
const thumbInput = ref(null);
const videoInput = ref(null);

// State
const userData = ref(null);
const playlists = ref([]);
const errorList = ref([]);
const status = ref(null);
const isLoading = ref(true);
const isSubmitting = ref(false);

const formData = ref({
    status: '',
    title: '',
    description: '',
    playlist_id: '',
    thumb: null,
    video: null
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadUserData = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return null;
        }

        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        return {
            ...response.data,
            image: new URL(response.data.image, window.location.origin).href
        };
    } catch (error) {
        console.error('Error loading user data:', error);
        if (error.response?.status === 401) {
            router.push('/');
        }
        return null;
    }
};

const loadPlaylists = async () => {
    try {
        isLoading.value = true;
        const user = await loadUserData();
        if (!user) return;

        const response = await axios.get(`/api/playlists/${user.id}`);
        playlists.value = response.data.map(playlist => ({
            ...playlist,
            thumb: new URL(playlist.thumb, window.location.origin).href
        }));
    } catch (error) {
        console.error('Error loading playlists:', error);
        errorList.value = ['Failed to load playlists'];
        status.value = 500;
    } finally {
        isLoading.value = false;
    }
};

const validateFile = (file, type) => {
    const maxSize = type === 'image' ? 2 * 1024 * 1024 : 100 * 1024 * 1024; // 2MB for images, 100MB for videos
    const allowedTypes = type === 'image' 
        ? ['image/jpeg', 'image/png', 'image/gif'] 
        : ['video/mp4', 'video/webm', 'video/ogg'];

    if (file.size > maxSize) {
        errorList.value = [`${type === 'image' ? 'Image' : 'Video'} size should not exceed ${maxSize / (1024 * 1024)}MB`];
        status.value = 500;
        return false;
    }

    if (!allowedTypes.includes(file.type)) {
        errorList.value = [`Please select a valid ${type} file (${allowedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ')})`];
        status.value = 500;
        return false;
    }

    return true;
};

const handleThumbChange = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file, 'image')) {
        formData.value.thumb = file;
    } else {
        event.target.value = '';
    }
};

const handleVideoChange = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file, 'video')) {
        formData.value.video = file;
    } else {
        event.target.value = '';
    }
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        errorList.value = [];
        status.value = null;

        const user = await loadUserData();
        if (!user) return;

        const data = new FormData();
        data.append('teacher_id', user.id);
        Object.entries(formData.value).forEach(([key, value]) => {
            if (value !== null && value !== '') {
                data.append(key, value);
            }
        });

        const response = await axios.post('/api/admin/add-content', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        errorList.value = [response.data.message];
        status.value = response.data.status;

        if (status.value !== 500) {
            await Swal.fire({
                title: 'Success!',
                text: 'Content has been uploaded successfully',
                icon: 'success'
            });

            router.push('/admin_contents');
        }
    } catch (error) {
        console.error('Error uploading content:', error);
        errorList.value = error.response?.data?.message || ['An error occurred while uploading content'];
        status.value = 500;
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle
onMounted(() => {
    loadPlaylists();
});
</script>