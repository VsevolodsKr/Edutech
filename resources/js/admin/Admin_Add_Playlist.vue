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
                    <h1 class="text-2xl text-text_dark">Pievienot jaunu kursu</h1>
                    <button 
                        @click="router.push('/admin_playlists')"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-text_light hover:text-button transition-colors duration-200"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Atpakaļ uz kursiem
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
                                        Kursa statuss <span class="text-button4">*</span>
                                    </label>
                                    <select 
                                        v-model="formData.status"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                        required
                                    >
                                        <option value="" disabled>Izvēlieties statusu...</option>
                                        <option value="Aktīvs">Aktīvs</option>
                                        <option value="Neaktīvs">Neaktīvs</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Kursa nosaukums <span class="text-button4">*</span>
                                    </label>
                                    <input 
                                        v-model="formData.title"
                                        type="text"
                                        placeholder="Ievadiet kursa nosaukumu..."
                                        required
                                        maxlength="50"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200"
                                    >
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.title.length }}/50 simboli
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Kursa apraksts <span class="text-button4">*</span>
                                    </label>
                                    <textarea 
                                        v-model="formData.description"
                                        placeholder="Ievadiet kursa aprakstu..."
                                        required
                                        maxlength="1000"
                                        rows="4"
                                        class="w-full px-4 py-2 rounded-lg bg-background border border-gray-200 focus:border-button focus:ring-1 focus:ring-button transition-colors duration-200 resize-none"
                                    ></textarea>
                                    <span class="text-xs text-text_light mt-1 block">
                                        {{ formData.description.length }}/1000 simboli
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-text_dark mb-2">
                                        Kursa attēls <span class="text-button4">*</span>
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
                            </div>
                        </div>

                        <div class="mt-8">
                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full bg-button text-base py-3 border-2 border-button rounded-lg font-medium transition-all duration-200 hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-opacity-100 disabled:hover:shadow-none flex items-center justify-center gap-2"
                            >
                                <span v-if="isSubmitting" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>
                                {{ isSubmitting ? 'Pievienošana...' : 'Pievienot kursu' }}
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
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();
const thumbInput = ref(null);

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

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const handleThumbChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        messages.value = ['Lūdzu, izvēlieties derīgu attēla failu (JPG vai PNG)'];
        errorStatus.value = 500;
        event.target.value = '';
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        messages.value = ['Attēla izmērs nedrīkst pārsniegt 2MB'];
        errorStatus.value = 500;
        event.target.value = '';
        return;
    }

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

        if (!formData.value.status || !formData.value.title || !formData.value.description || !thumbInput.value.files[0]) {
            messages.value = ['Lūdzu, aizpildiet visus obligātus laukus'];
            errorStatus.value = 500;
            return;
        }

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
        formDataToSend.append('status', formData.value.status);
        formDataToSend.append('title', formData.value.title);
        formDataToSend.append('description', formData.value.description);
        formDataToSend.append('teacher_id', userResponse.data.id);

        const thumbFile = thumbInput.value.files[0];
        formDataToSend.append('thumb', thumbFile);

        const response = await axios.post('/api/playlists/add', formDataToSend, {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        messages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message || 'Playlist added successfully'];
        errorStatus.value = response.data.status;

        if (response.data.status !== 500) {
            formData.value = {
                status: '',
                title: '',
                description: '',
            };
            thumbPreview.value = null;
            if (thumbInput.value) {
                thumbInput.value.value = '';
            }

            setTimeout(() => {
                router.push('/admin_playlists');
            }, 1000);
        }
    } catch (error) {
        if (error.response) {
            messages.value = error.response.data.message || ['Kļūda, ievadot kursu'];
            errorStatus.value = error.response.data.status || 500;
        } else if (error.request) {
            messages.value = ['Nav iegūta atbilde no servera'];
            errorStatus.value = 500;
        } else {
            messages.value = ['Neizdevās nosūtīt pieprasījumu'];
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