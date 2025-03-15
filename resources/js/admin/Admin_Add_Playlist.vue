<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex flex-col items-center justify-center min-h-[50vh] space-y-4">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-button border-t-transparent"></div>
                <p class="text-text_light">Loading...</p>
            </div>

            <template v-else>
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl text-text_dark">Add New Playlist</h1>
                    <button 
                        @click="router.push('/admin_playlists')"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-text_light hover:text-button transition-colors duration-200"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Playlists
                    </button>
                </div>
                
                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="bg-base rounded-lg p-8 w-full max-w-3xl shadow-lg">
                        <!-- Status Messages -->
                        <TransitionGroup 
                            name="message" 
                            tag="div" 
                            class="space-y-2 mb-6"
                        >
                            <div v-for="(message, index) in messages" 
                                 :key="index"
                                 :class="[
                                    'px-4 py-3 rounded-lg flex items-center gap-3 transition-all duration-300',
                                    errorStatus === 500 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'
                                 ]"
                            >
                                <i :class="[
                                    errorStatus === 500 ? 'fas fa-exclamation-circle' : 'fas fa-check-circle',
                                    'text-xl'
                                ]"></i>
                                <span class="text-sm">{{ message }}</span>
                            </div>
                        </TransitionGroup>

                        <!-- Form Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Status Field -->
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Playlist Status <span class="text-button4">*</span>
                                    </label>
                                    <select 
                                        v-model="formData.status"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                        required
                                    >
                                        <option value="" disabled>Select status...</option>
                                        <option value="active">Active</option>
                                        <option value="deactive">Deactive</option>
                                    </select>
                                </div>

                                <!-- Title Field -->
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Playlist Title <span class="text-button4">*</span>
                                    </label>
                                    <input 
                                        v-model="formData.title"
                                        type="text"
                                        placeholder="Enter playlist title..."
                                        required
                                        maxlength="50"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                    >
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.title.length }}/50 characters
                                    </span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Description Field -->
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Playlist Description <span class="text-button4">*</span>
                                    </label>
                                    <textarea 
                                        v-model="formData.description"
                                        placeholder="Enter playlist description..."
                                        required
                                        maxlength="1000"
                                        rows="4"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200 resize-none"
                                    ></textarea>
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.description.length }}/1000 characters
                                    </span>
                                </div>

                                <!-- Thumbnail Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Thumbnail <span class="text-button4">*</span>
                                    </label>
                                    <div class="relative">
                                        <div v-if="thumbPreview" 
                                             class="relative mb-4 rounded-lg overflow-hidden group">
                                            <img :src="thumbPreview"
                                                 :alt="formData.title"
                                                 class="w-full h-40 object-cover"
                                                 @error="messages = ['Failed to load thumbnail']; errorStatus = 500">
                                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                                <span class="text-white text-sm">Selected thumbnail</span>
                                            </div>
                                        </div>
                                        <input 
                                            ref="thumbInput"
                                            type="file"
                                            accept="image/jpeg, image/png"
                                            @change="handleThumbChange"
                                            class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                            required
                                        >
                                        <p class="text-xs text-text_light mt-1">
                                            Max size: 2MB. Supported formats: JPG, PNG
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full bg-button text-base py-3 border-2 border-button rounded-lg font-medium transition-all duration-200 hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-opacity-100 disabled:hover:shadow-none flex items-center justify-center gap-2"
                            >
                                <span v-if="isSubmitting" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>
                                {{ isSubmitting ? 'Adding Playlist...' : 'Add Playlist' }}
                            </button>
                        </div>
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
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();
const thumbInput = ref(null);

// State
const isLoading = ref(false);
const isSubmitting = ref(false);
const messages = ref([]);
const errorStatus = ref(null);
const thumbPreview = ref(null);
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
const handleThumbChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        messages.value = ['Please select a valid image file (JPG or PNG)'];
        errorStatus.value = 500;
        event.target.value = '';
        return;
    }

    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        messages.value = ['Image size should not exceed 2MB'];
        errorStatus.value = 500;
        event.target.value = '';
        return;
    }

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        thumbPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const handleSubmit = async (event) => {
    try {
        event.preventDefault();
        isSubmitting.value = true;
        messages.value = [];
        errorStatus.value = null;

        // Validate form
        if (!formData.value.status || !formData.value.title || !formData.value.description || !thumbInput.value.files[0]) {
            messages.value = ['Please fill in all required fields'];
            errorStatus.value = 500;
            return;
        }

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }

        // Get user first to get teacher_id
        const userResponse = await axios.get('/api/user', {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        console.log('User data:', userResponse.data);

        // Create form data
        const formDataToSend = new FormData();
        formDataToSend.append('status', formData.value.status);
        formDataToSend.append('title', formData.value.title);
        formDataToSend.append('description', formData.value.description);
        formDataToSend.append('teacher_id', userResponse.data.id);

        // Append thumbnail
        const thumbFile = thumbInput.value.files[0];
        formDataToSend.append('thumb', thumbFile);

        console.log('Sending form data:', {
            status: formData.value.status,
            title: formData.value.title,
            description: formData.value.description,
            teacher_id: userResponse.data.id,
            thumb: thumbFile.name
        });

        // Send request
        const response = await axios.post('/api/playlists/add', formDataToSend, {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        console.log('Response:', response.data);

        messages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message || 'Playlist added successfully'];
        errorStatus.value = response.data.status;

        if (response.data.status !== 500) {
            // Reset form
            formData.value = {
                status: '',
                title: '',
                description: '',
            };
            thumbPreview.value = null;
            if (thumbInput.value) {
                thumbInput.value.value = '';
            }

            // Redirect after success
            setTimeout(() => {
                router.push('/admin_playlists');
            }, 1000);
        }
    } catch (error) {
        console.error('Error adding playlist:', error);
        if (error.response) {
            console.error('Error response:', error.response.data);
            messages.value = error.response.data.message || ['An error occurred while adding the playlist'];
            errorStatus.value = error.response.data.status || 500;
        } else if (error.request) {
            console.error('No response received:', error.request);
            messages.value = ['No response received from server'];
            errorStatus.value = 500;
        } else {
            console.error('Error setting up request:', error.message);
            messages.value = ['Failed to send request'];
            errorStatus.value = 500;
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
.message-enter-active,
.message-leave-active {
    transition: all 0.3s ease;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>