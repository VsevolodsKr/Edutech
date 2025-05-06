<template>
    <div>
    <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex flex-col items-center justify-center min-h-[50vh] space-y-4">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-button border-t-transparent"></div>
                <p class="text-text_light">Ielādē...</p>
            </div>

            <template v-else>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl text-text_dark">Pievienot jaunu video</h1>
                    <button 
                        @click="router.push('/admin_contents')"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-text_light hover:text-button transition-colors duration-200"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Atpakaļ uz video
                    </button>
                </div>
                
            <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="bg-base rounded-lg p-8 w-full max-w-3xl shadow-lg">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Video statuss <span class="text-button4">*</span>
                                    </label>
                                    <select 
                                        v-model="formData.status"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                        required
                                    >
                                        <option value="" disabled>Izvēlieties statusu...</option>
                                        <option value="active">Aktīvs</option>
                                        <option value="deactive">Neaktīvs</option>
                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Video nosaukums <span class="text-button4">*</span>
                                    </label>
                                    <input 
                                        v-model="formData.title"
                                        type="text"
                                        placeholder="Ievadiet video nosaukumu..."
                                        required
                                        maxlength="50"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                    >
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.title.length }}/50 simboli
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Video kurss <span class="text-button4">*</span>
                                    </label>
                                    <div class="relative">
                                        <select 
                                            v-model="formData.playlist_id"
                                            class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                            required
                                            :disabled="isLoadingPlaylists"
                                        >
                                            <option value="" disabled>{{ isLoadingPlaylists ? 'Ielādē...' : 'Izvēlieties kursu...' }}</option>
                                            <option v-for="playlist in playlists" 
                                                    :key="playlist.id" 
                                                    :value="playlist.id">
                                                {{ playlist.title }} (<span :class="playlist.status === 'active' ? 'text-green-600' : 'text-red-600'">{{ playlist.status }}</span>)
                                            </option>
                    </select>
                                        <div v-if="isLoadingPlaylists" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <div class="animate-spin rounded-full h-5 w-5 border-2 border-button border-t-transparent"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Video apraksts <span class="text-button4">*</span>
                                    </label>
                                    <textarea 
                                        v-model="formData.description"
                                        placeholder="Ievadiet video aprakstu..."
                                        required
                                        maxlength="1000"
                                        rows="4"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200 resize-none"
                                    ></textarea>
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.description.length }}/1000 simboli
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    <div v-if="videoSourceType === 'file'">
                                        <label class="block text-sm font-medium text-text_dark mb-2">
                                            Attēls <span class="text-button4">*</span>
                                        </label>
                                        <div class="relative">
                                            <div v-if="thumbPreview" 
                                                 class="relative mb-4 rounded-lg overflow-hidden group">
                                                <img :src="thumbPreview"
                                                     :alt="formData.title"
                                                     class="w-full h-40 object-cover"
                                                     @error="messages = ['Failed to load thumbnail']; errorStatus = 500">
                                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                                    <span class="text-white text-sm">Izvēlētais attēls</span>
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
                                                Maksimālais izmērs: 2MB. Atļautie formāti: JPG, PNG
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-text_dark mb-2">
                                            Video avots <span class="text-button4">*</span>
                                        </label>
                                        <div class="space-y-4">
                                            <div class="flex gap-4">
                                                <label class="flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        v-model="videoSourceType" 
                                                        value="file"
                                                        class="mr-2"
                                                    >
                                                    Video fails
                                                </label>
                                                <label class="flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        v-model="videoSourceType" 
                                                        value="youtube"
                                                        class="mr-2"
                                                    >
                                                    YouTube saite
                                                </label>
                                            </div>

                                            <div v-if="videoSourceType === 'file'" class="relative">
                                                <div v-if="videoPreview" 
                                                     class="relative mb-4 rounded-lg overflow-hidden">
                                                    <video 
                                                        class="w-full h-40 object-cover"
                                                        controls
                                                        @error="handleVideoError"
                                                    >
                                                        <source :src="videoPreview" type="video/mp4">
                                                        Jūsu pārlūkā nav atļauts video tagu.
                                                    </video>
                                                </div>
                                                <input 
                                                    ref="videoInput"
                                                    type="file"
                                                    accept="video/*"
                                                    @change="handleVideoChange"
                                                    @error="handleVideoError"
                                                    class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                                    :required="videoSourceType === 'file'"
                                                >
                                                <p class="text-xs text-text_light mt-1">
                                                    Maksimālais izmērs: 10MB. Atļautie formāti: MP4, WebM
                                                </p>
                                            </div>

                                            <div v-else class="relative">
                                                <input 
                                                    v-model="youtubeLink"
                                                    type="text"
                                                    placeholder="Enter YouTube video URL (e.g., https://www.youtube.com/watch?v=...)"
                                                    class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                                    :required="videoSourceType === 'youtube'"
                                                >
                                                <p class="text-xs text-text_light mt-1">
                                                    Ievadiet derīgu YouTube video URL
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full bg-button text-base py-3 border-2 border-button rounded-lg font-medium transition-all duration-200 hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-opacity-100 disabled:hover:shadow-none flex items-center justify-center gap-2"
                            >
                                <span v-if="isSubmitting" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>
                                {{ isSubmitting ? 'Pievienošana...' : 'Pievienot saturu' }}
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
import Swal from 'sweetalert2';

const router = useRouter();
const { width } = useWindowSize();
const thumbInput = ref(null);
const videoInput = ref(null);

const isLoading = ref(true);
const isLoadingPlaylists = ref(false);
const isSubmitting = ref(false);
const playlists = ref([]);
const messages = ref([]);
const errorStatus = ref(null);
const thumbPreview = ref(null);
const videoPreview = ref(null);
const videoSourceType = ref('file');
const youtubeLink = ref('');
const formData = ref({
    status: '',
    title: '',
    description: '',
    playlist_id: '',
    youtube_link: '',
    video_source_type: 'file',
    thumb: null
});

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const loadPlaylists = async () => {
    try {
        isLoadingPlaylists.value = true;
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        const playlistsResponse = await axios.get(`/api/playlists/teacher_playlists/${userResponse.data.id}`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });
        
        if (Array.isArray(playlistsResponse.data)) {
            playlists.value = playlistsResponse.data;
        } else if (playlistsResponse.data.playlists && Array.isArray(playlistsResponse.data.playlists)) {
            playlists.value = playlistsResponse.data.playlists;
        } else if (playlistsResponse.data.data && Array.isArray(playlistsResponse.data.data)) {
            playlists.value = playlistsResponse.data.data;
        }

    } catch (error) {
        messages.value = ['Failed to load playlists'];
        errorStatus.value = 500;
        if (error.response?.status === 401) {
            router.push('/login');
        }
    } finally {
        isLoadingPlaylists.value = false;
    }
};

const handleThumbChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            messages.value = ['Lūdzu, izvēlieties JPG vai PNG attēla failu'];
            errorStatus.value = 500;
            thumbInput.value.value = '';
            thumbPreview.value = null;
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            messages.value = ['Attēla izmērs nedrīkst pārsniegt 2MB'];
            errorStatus.value = 500;
            thumbInput.value.value = '';
            thumbPreview.value = null;
            return;
        }

        thumbPreview.value = URL.createObjectURL(file);
    }
};

const handleVideoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (!file.type.startsWith('video/')) {
            messages.value = ['Lūdzu, izvēlieties video failu'];
            errorStatus.value = 500;
            videoInput.value.value = '';
            videoPreview.value = null;
            return;
        }
        
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            messages.value = ['Video izmērs nedrīkst pārsniegt 10MB'];
            errorStatus.value = 500;
            videoInput.value.value = '';
            videoPreview.value = null;
            return;
        }

        videoPreview.value = URL.createObjectURL(file);
    }
};

const handleVideoError = (event) => {
    messages.value = ['Neizdevās ielādēt video. Lūdzu, pārbaudiet faila ceļu.'];
    errorStatus.value = 500;
};

const getYouTubeThumbnail = (url) => {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    if (match && match[2].length === 11) {
        return `https://img.youtube.com/vi/${match[2]}/maxresdefault.jpg`;
    }
    return null;
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        messages.value = [];
        errorStatus.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        const formDataToSend = new FormData();
        formDataToSend.append('teacher_id', userResponse.data.id);
        formDataToSend.append('playlist_id', formData.value.playlist_id);
        formDataToSend.append('title', formData.value.title);
        formDataToSend.append('description', formData.value.description);
        formDataToSend.append('status', formData.value.status);
        formDataToSend.append('video_source_type', videoSourceType.value);

        if (!formData.value.playlist_id || !formData.value.title || !formData.value.description || !formData.value.status) {
            messages.value = ['Lūdzu, aizpildiet visas obligātās laukus'];
            errorStatus.value = 500;
            return;
        }

        if (videoSourceType.value === 'file') {
            if (!thumbInput.value?.files[0]) {
                messages.value = ['Lūdzu, izvēlieties attēla failu'];
                errorStatus.value = 500;
                return;
            }
            if (!videoInput.value?.files[0]) {
                messages.value = ['Lūdzu, izvēlieties video failu'];
                errorStatus.value = 500;
                return;
            }
            formDataToSend.append('thumb', thumbInput.value.files[0]);
            formDataToSend.append('video', videoInput.value.files[0]);
        } else {
            if (!youtubeLink.value) {
                messages.value = ['Lūdzu, ievadiet YouTube video URL'];
                errorStatus.value = 500;
                return;
            }
            formDataToSend.append('youtube_link', youtubeLink.value);
            
            const thumbnailUrl = getYouTubeThumbnail(youtubeLink.value);
            if (thumbnailUrl) {
                formDataToSend.append('youtube_thumb', thumbnailUrl);
            }
        }

        const response = await axios.post(
            '/api/add_content/send',
            formDataToSend,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        );

        if (response.data.status === 'success') {
            messages.value = ['Video pievienots'];
            errorStatus.value = 200;
            
            await Swal.fire({
                title: 'Pievienots!',
                text: 'Video ir pievienots',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            
            router.push('/admin_contents');
        } else {
            messages.value = Array.isArray(response.data.message) 
                ? response.data.message 
                : [response.data.message];
            errorStatus.value = response.data.status;
        }
    } catch (error) {
        messages.value = error.response?.data?.message || ['Kļūda, ievadot video'];
        errorStatus.value = error.response?.data?.status || 500;
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(async () => {
    try {
        await loadPlaylists();
    } finally {
        isLoading.value = false;
    }
});
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