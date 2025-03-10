<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <template v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Update Playlist</h1>
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
                                Playlist status <span class="text-button4">*</span>
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
                                Playlist title <span class="text-button4">*</span>
                            </label>
                            <input v-model="formData.title"
                                   type="text"
                                   placeholder="Enter playlist title..."
                                   required
                                   maxlength="50"
                                   class="text-[1rem] text-text_dark rounded-lg p-[.5rem] mt-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
                        </div>

                        <!-- Description Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Playlist description <span class="text-button4">*</span>
                            </label>
                            <textarea v-model="formData.description"
                                    placeholder="Enter playlist description..."
                                    required
                                    maxlength="1000"
                                    class="h-[20rem] resize-none w-full rounded-lg bg-background mt-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
                            </textarea>
                        </div>

                        <!-- Thumbnail Field -->
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Playlist thumbnail
                                <span v-if="!playlist.thumb" class="text-button4">*</span>
                            </label>
                            <div v-if="playlist.thumb" class="relative mt-[1rem]">
                                <img :src="playlist.thumb" 
                                     :alt="formData.title"
                                     class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]">
                                <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[1rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1.2rem] [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.7rem]">
                                    {{ contentCount }}
                                </span>
                            </div>
                            <input ref="fileInput"
                                   type="file"
                                   accept="image/*"
                                   :required="!playlist.thumb"
                                   @change="handleFileChange"
                                   class="text-[1rem] text-text_dark rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none mt-[1rem] h-[3rem] [@media(max-width:550px)]:text-[.7rem]">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isSubmitting ? 'Updating...' : 'Update Playlist' }}
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
const fileInput = ref(null);

// State
const isLoading = ref(true);
const isSubmitting = ref(false);
const playlist = ref(null);
const contentCount = ref(0);
const messages = ref([]);
const errorStatus = ref(null);
const formData = ref({
    status: '',
    title: '',
    description: '',
});

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadPlaylist = async () => {
    try {
        const response = await axios.get(`/api/playlists/find/${route.params.id}`);
        if (response.data.playlist) {
            playlist.value = {
                ...response.data.playlist,
                thumb: new URL(response.data.playlist.thumb, window.location.origin).href
            };
            
            // Initialize form data
            formData.value = {
                status: playlist.value.status || '',
                title: playlist.value.title || '',
                description: playlist.value.description || '',
            };

            // Get content count
            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.id}/amount`);
            contentCount.value = countResponse.data;
        }
    } catch (error) {
        console.error('Error loading playlist:', error);
        messages.value = ['Failed to load playlist'];
        errorStatus.value = 500;
    } finally {
        isLoading.value = false;
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            messages.value = ['Please select an image file'];
            errorStatus.value = 500;
            fileInput.value.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            messages.value = ['Image size should be less than 2MB'];
            errorStatus.value = 500;
            fileInput.value.value = '';
            return;
        }
    }
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        messages.value = [];
        errorStatus.value = null;

        const formDataToSend = new FormData();
        formDataToSend.append('status', formData.value.status);
        formDataToSend.append('title', formData.value.title);
        formDataToSend.append('description', formData.value.description);

        if (fileInput.value?.files[0]) {
            formDataToSend.append('image', fileInput.value.files[0]);
        }

        const response = await axios.post(
            `/api/playlists/update/${route.params.id}/send`,
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
                router.push('/admin_playlists');
            }, 1000);
        }
    } catch (error) {
        console.error('Error updating playlist:', error);
        messages.value = error.response?.data?.message || ['An error occurred while updating the playlist'];
        errorStatus.value = error.response?.data?.status || 500;
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle
onMounted(() => {
    loadPlaylist();
});
</script>