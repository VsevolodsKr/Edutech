<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Update Content</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
                        <!-- Error/Success Messages -->
                        <div v-if="messages.length" 
                             :class="[errorStatus === 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]', 'rounded-xl mb-[1rem]']">
                            <div v-for="(message, index) in messages" 
                                 :key="index"
                                 class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                                <i :class="[errorStatus === 500 ? 'fa fa-warning' : 'fa fa-check']"></i>
                                {{ message }}
                            </div>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Video status <span class="text-button4">*</span>
                            </label>
                            <select v-model="formData.status" 
                                    class="text-[1rem] text-text_dark rounded-lg p-[.5rem] mt-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full" 
                                    required>
                                <option value="" disabled>Select status...</option>
                                <option value="active">active</option>
                                <option value="deactive">deactive</option>
                            </select>
                        </div>

                        <!-- Title Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Video title <span class="text-button4">*</span>
                            </label>
                            <input v-model="formData.title"
                                   type="text"
                                   placeholder="Enter content title..."
                                   required
                                   maxlength="50"
                                   class="text-[1rem] text-text_dark rounded-lg p-[.5rem] mt-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
                        </div>

                        <!-- Description Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Video description <span class="text-button4">*</span>
                            </label>
                            <textarea v-model="formData.description"
                                    placeholder="Enter content description..."
                                    required
                                    maxlength="1000"
                                    class="h-[20rem] resize-none w-full rounded-lg bg-background mt-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
                            </textarea>
                        </div>

                        <!-- Playlist Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Video playlist <span class="text-button4">*</span>
                            </label>
                            <select v-model="formData.playlist_id"
                                    class="text-[1rem] text-text_light rounded-lg p-[.5rem] mt-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full" 
                                    required>
                                <option value="" disabled>Select playlist...</option>
                                <option v-for="playlist in playlists" 
                                        :key="playlist.id" 
                                        :value="playlist.id">
                                    {{ playlist.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Thumbnail Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Video thumbnail
                                <span v-if="!content?.thumb" class="text-button4">*</span>
                            </label>
                            <div v-if="content?.thumb" class="relative mt-[1rem]">
                                <img :src="content.thumb" 
                                     :alt="formData.title"
                                     class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]">
                            </div>
                            <input ref="thumbInput"
                                   type="file"
                                   accept="image/*"
                                   :required="!content?.thumb"
                                   @change="handleThumbChange"
                                   class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none mt-[1rem] h-[3rem] [@media(max-width:550px)]:text-[.7rem]">
                        </div>

                        <!-- Video Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Select video
                                <span v-if="!content?.video" class="text-button4">*</span>
                            </label>
                            <div v-if="content?.video" class="relative mt-[1rem]">
                                <video class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]" 
                                       controls>
                                    <source :src="content.video">
                                </video>
                            </div>
                            <input ref="videoInput"
                                   type="file"
                                   accept="video/*"
                                   :required="!content?.video"
                                   @change="handleVideoChange"
                                   class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none mt-[1rem] h-[3rem] [@media(max-width:550px)]:text-[.7rem]">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isSubmitting ? 'Updating...' : 'Update Content' }}
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
import { useRouter, useRoute } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const route = useRoute();
const { width } = useWindowSize();
const thumbInput = ref(null);
const videoInput = ref(null);

// State
const isLoading = ref(true);
const isSubmitting = ref(false);
const content = ref(null);
const playlists = ref([]);
const messages = ref([]);
const errorStatus = ref(null);
const formData = ref({
    status: '',
    title: '',
    description: '',
    playlist_id: '',
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadContent = async () => {
    try {
        const response = await axios.get(`/api/contents/find/${route.params.id}`);
        if (response.data) {
            content.value = {
                ...response.data,
                thumb: new URL(response.data.thumb, window.location.origin).href,
                video: new URL(response.data.video, window.location.origin).href
            };
            
            // Initialize form data
            formData.value = {
                status: content.value.status || '',
                title: content.value.title || '',
                description: content.value.description || '',
                playlist_id: content.value.playlist_id || '',
            };
        }
    } catch (error) {
        console.error('Error loading content:', error);
        messages.value = ['Failed to load content'];
        errorStatus.value = 500;
    }
};

const loadPlaylists = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        const playlistsResponse = await axios.get(`/api/playlists/${userResponse.data.id}`);
        playlists.value = playlistsResponse.data.map(playlist => ({
            ...playlist,
            thumb: new URL(playlist.thumb, window.location.origin).href
        }));
    } catch (error) {
        console.error('Error loading playlists:', error);
        messages.value = ['Failed to load playlists'];
        errorStatus.value = 500;
    }
};

const handleThumbChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            messages.value = ['Please select an image file'];
            errorStatus.value = 500;
            thumbInput.value.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            messages.value = ['Image size should be less than 2MB'];
            errorStatus.value = 500;
            thumbInput.value.value = '';
            return;
        }
    }
};

const handleVideoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('video/')) {
            messages.value = ['Please select a video file'];
            errorStatus.value = 500;
            videoInput.value.value = '';
            return;
        }
        
        // Validate file size (max 100MB)
        if (file.size > 100 * 1024 * 1024) {
            messages.value = ['Video size should be less than 100MB'];
            errorStatus.value = 500;
            videoInput.value.value = '';
            return;
        }
    }
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        messages.value = [];
        errorStatus.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${token}` }
        });

        const formDataToSend = new FormData();
        formDataToSend.append('teacher_id', userResponse.data.id);
        formDataToSend.append('playlist_id', formData.value.playlist_id);
        formDataToSend.append('title', formData.value.title);
        formDataToSend.append('description', formData.value.description);
        formDataToSend.append('status', formData.value.status);

        if (thumbInput.value?.files[0]) {
            formDataToSend.append('thumb', thumbInput.value.files[0]);
        }

        if (videoInput.value?.files[0]) {
            formDataToSend.append('video', videoInput.value.files[0]);
        }

        const response = await axios.post(
            `/api/contents/update/${route.params.id}/send`,
            formDataToSend,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        );

        messages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message];
        errorStatus.value = response.data.status;

        if (response.data.status !== 500) {
            setTimeout(() => {
                router.push('/admin_contents');
            }, 1000);
        }
    } catch (error) {
        console.error('Error updating content:', error);
        messages.value = error.response?.data?.message || ['An error occurred while updating the content'];
        errorStatus.value = error.response?.data?.status || 500;
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle
onMounted(async () => {
    try {
        await Promise.all([
            loadContent(),
            loadPlaylists()
        ]);
    } catch (error) {
        console.error('Error initializing component:', error);
    } finally {
        isLoading.value = false;
    }
});
</script>