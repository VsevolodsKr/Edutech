<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark capitalize">{{ isEditing ? 'Rediģēt profilu' : 'Skolotāja profils' }}</h1>
                    <button 
                        @click="toggleEditing"
                        class="bg-button text-base border-2 border-button px-4 py-2 rounded-lg hover:bg-button1 transition-colors duration-200 hover:bg-base hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:px-3 [@media(max-width:550px)]:py-1"
                    >
                        {{ isEditing ? 'Atcelt' : 'Rediģēt profilu' }}
                    </button>
                </div>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>

                <div v-else class="flex items-center justify-center">
                    <!-- View Mode -->
                    <div v-if="!isEditing" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div class="flex justify-center mb-6">
                            <img 
                                :src="getImageUrl(adminData.image)"
                                :alt="adminData.name"
                                @error="handleImageError"
                                class="w-32 h-32 rounded-full object-cover border-4 border-button"
                            >
                        </div>

                        <div class="space-y-6">
                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Vārds
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ adminData.name || 'Vārda nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    E-pasts
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ adminData.email || 'E-pasta nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Loma
                                </h3>
                                <p class="text-[1.2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">
                                    {{ adminData.profession || 'skolotājs' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-background rounded-lg">
                                    <h3 class="text-[1.8rem] text-button">{{ statistics.playlists }}</h3>
                                    <div class="flex justify-center items-center gap-2">
                                        <i class="fa-solid fa-bars-staggered text-button"></i>
                                        <p class="text-[1.2rem] text-text_light">Kursi</p>
                                    </div>
                                </div>
                                <div class="text-center p-4 bg-background rounded-lg">
                                    <h3 class="text-[1.8rem] text-button">{{ statistics.contents }}</h3>
                                    <div class="flex justify-center items-center gap-2">
                                        <i class="fas fa-graduation-cap text-button"></i>
                                        <p class="text-[1.2rem] text-text_light">Video</p>
                                    </div>
                                </div>
                                <div class="text-center p-4 bg-background rounded-lg">
                                    <h3 class="text-[1.8rem] text-button">{{ statistics.likes }}</h3>
                                    <div class="flex justify-center items-center gap-2">
                                        <i class="fas fa-heart text-button"></i>
                                        <p class="text-[1.2rem] text-text_light">Favorītvideo</p>
                                    </div>
                                </div>
                                <div class="text-center p-4 bg-background rounded-lg">
                                    <h3 class="text-[1.8rem] text-button">{{ statistics.comments }}</h3>
                                    <div class="flex justify-center items-center gap-2">
                                        <i class="fas fa-comment text-button"></i>
                                        <p class="text-[1.2rem] text-text_light">Komentāri</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <form v-else @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div v-if="errorList.length" class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]">
                            <p v-for="(error, index) in errorList" 
                               :key="index" 
                               class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>

                        <div class="flex justify-center mb-8">
                            <div class="relative">
                                <img 
                                    :src="getImageUrl(adminData.image)"
                                    alt="Profile image"
                                    @error="handleImageError"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-button"
                                >
                                <input 
                                    type="file"
                                    ref="fileInput"
                                    @change="handleFileChange"
                                    accept="image/*"
                                    class="hidden"
                                >
                                <button 
                                    @click.prevent="$refs.fileInput.click()"
                                    class="absolute bottom-0 right-0 bg-button text-base p-2 rounded-full hover:bg-opacity-80 transition-opacity"
                                >
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-text_dark mb-2">Vārds <span class="text-button4">*</span></label>
                                <input 
                                    v-model="formData.name"
                                    type="text"
                                    required
                                    maxlength="50"
                                    class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-text_dark mb-2">E-pasts <span class="text-button4">*</span></label>
                                <input 
                                    v-model="formData.email"
                                    type="email"
                                    required
                                    maxlength="50"
                                    class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                >
                            </div>

                            <div class="border-t-2 border-line pt-6">
                                <label class="flex items-center text-text_dark mb-4">
                                    <input
                                        type="checkbox"
                                        v-model="showPasswordFields"
                                        class="mr-2"
                                    >
                                    Mainīt paroli
                                </label>

                                <div v-if="showPasswordFields" class="space-y-4">
                                    <div>
                                        <label class="block text-text_dark mb-2">Pašreizējā parole <span class="text-button4">*</span></label>
                                        <input 
                                            v-model="formData.old_password"
                                            type="password"
                                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-text_dark mb-2">Jaunā parole <span class="text-button4">*</span></label>
                                        <input 
                                            v-model="formData.new_password"
                                            type="password"
                                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-text_dark mb-2">Apstiprināt jauno paroli <span class="text-button4">*</span></label>
                                        <input 
                                            v-model="formData.confirm_password"
                                            type="password"
                                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <button 
                                    type="submit"
                                    :disabled="isSubmitting"
                                    class="bg-button text-base px-6 py-2 rounded-lg hover:bg-transparent hover:text-button border-2 border-button transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <span v-if="isSubmitting" class="animate-spin rounded-full h-5 w-5 border-2 border-base border-t-transparent"></span>
                                    {{ isSubmitting ? 'Saglabā...' : 'Saglabāt izmaiņas' }}
                                </button>
                            </div>
                        </div>
                    </form>
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

const defaultAvatar = '/storage/default-avatar.png';
const isEditing = ref(false);
const showPasswordFields = ref(false);

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

const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const toggleEditing = () => {
    if (isEditing.value) {
        isEditing.value = false;
        showPasswordFields.value = false;
        formData.value = {
            name: adminData.value.name,
            email: adminData.value.email,
            old_password: '',
            new_password: '',
            confirm_password: '',
            image: null
        };
        errorList.value = [];
    } else {
        isEditing.value = true;
        formData.value = {
            name: adminData.value.name || '',
            email: adminData.value.email || '',
            old_password: '',
            new_password: '',
            confirm_password: '',
            image: null
        };
    }
};

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

        const user = store.state.user;
        if (!user || !user.encrypted_id) {
            console.error('No user data available');
            errorList.value = ['Nav pieejama lietotāja informācija'];
            return;
        }

        const [profileResponse, playlistsResponse, contentsResponse, likesResponse, commentsResponse] = await Promise.all([
            axios.get('/api/user', { headers }),
            axios.get(`/api/playlists/amount/${user.encrypted_id}`, { headers }),
            axios.get(`/api/contents/amount/${user.encrypted_id}`, { headers }),
            axios.get(`/api/likes/count_teacher/${user.encrypted_id}`, { headers }),
            axios.get(`/api/comments/count_teacher/${user.encrypted_id}`, { headers })
        ]);

        if (!profileResponse.data.profession) {
            throw new Error('Unauthorized access');
        }

        adminData.value = profileResponse.data;
        statistics.value = {
            playlists: playlistsResponse.data.data || 0,
            contents: contentsResponse.data.data || 0,
            likes: likesResponse.data.data || 0,
            comments: commentsResponse.data.data || 0
        };

        formData.value.name = adminData.value.name;
        formData.value.email = adminData.value.email;
    } catch (error) {
        console.error('Error loading admin data:', error);
        if (error.response?.status === 401 || error.message === 'Unauthorized access') {
            localStorage.removeItem('token');
            router.push('/');
        } else {
            errorList.value = ['Neizdevās ielādēt skolotāja datus'];
        }
    } finally {
        isLoading.value = false;
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            errorList.value = ['Attēla izmērs nedrīkst pārsniegt 2MB'];
            event.target.value = '';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errorList.value = ['Lūdzu, izvēlieties derīgu attēla failu (JPEG, PNG, vai GIF)'];
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

        if (showPasswordFields.value) {
            if (!formData.value.old_password) {
                throw new Error('Lūdzu, ievadiet pašreizējo paroli');
            }
            if (!formData.value.new_password) {
                throw new Error('Lūdzu, ievadiet jauno paroli');
            }
            if (formData.value.new_password !== formData.value.confirm_password) {
                throw new Error('Jaunās paroles nesakrīt');
            }
            if (formData.value.new_password.length < 6) {
                throw new Error('Parolei jābūt vismaz 6 rakstzīmes garai');
            }
        }

        const data = new FormData();

        data.append('name', formData.value.name);
        data.append('email', formData.value.email);

        if (showPasswordFields.value) {
            data.append('old_password', formData.value.old_password);
            data.append('new_password', formData.value.new_password);
            data.append('confirm_password', formData.value.confirm_password);
        }

        if (formData.value.image instanceof File) {
            data.append('image', formData.value.image);
        }

        const token = localStorage.getItem('token');
        const response = await axios.post('/api/user/update-profile', data, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });

        const userData = response.data.user || response.data.data;
        store.commit('setUser', {
            ...userData,
            _updateKey: Date.now()
        });

        adminData.value = {
            ...userData,
            _updateKey: Date.now()
        };

        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        if (fileInput.value) {
            fileInput.value.value = '';
        }
        formData.value.image = null;

        store.commit('setReloadSidebar', true);

        await Swal.fire({
            title: 'Veiksmīgi!',
            text: 'Jūsu profils ir veiksmīgi rediģēts',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });

        isEditing.value = false;
    } catch (error) {
        console.error('Profile update error:', error);
        if (error.response) {
            console.log('Error response:', error.response.data);
        }
        if (error.response?.data?.message) {
            errorList.value = Array.isArray(error.response.data.message) 
                ? error.response.data.message 
                : [error.response.data.message];
        } else {
            errorList.value = [error.message || 'Radās kļūda, rediģējot profilu'];
        }
    } finally {
        isSubmitting.value = false;
    }
};

const getImageUrl = (image) => {
    if (!image) return defaultAvatar;
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;

    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.startsWith('/storage/')) {
        cleanPath = cleanPath.replace('/storage/', '');
    }
    
    cleanPath = cleanPath.replace(/^\/+/, '').replace(/\/+$/, '');
    
    if (!cleanPath.startsWith('uploads/')) {
        cleanPath = `uploads/${cleanPath}`;
    }

    return `/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    if (!event.target.src.includes('default-avatar.png')) {
        event.target.src = defaultAvatar;
    }
};

onMounted(() => {
    loadAdminData();
});
</script>

<style scoped>
.message-enter-active,
.message-leave-active {
    transition: all 0.3s ease;
}
.message-leave-to,
.message-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
</style>