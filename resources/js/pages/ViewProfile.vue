<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-[1.5rem] text-text_dark capitalize">{{ isEditing ? 'Rediģēt profilu' : 'Profils' }}</h1>
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

                <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                    {{ error }}
                    <button 
                        @click="loadUserData"
                        class="block mx-auto mt-4 text-button hover:text-text_dark"
                    >
                        Mēģināt vēlreiz
                    </button>
                </div>

                <div v-else class="flex items-center justify-center">
                    <!-- View Mode -->
                    <div v-if="!isEditing" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div class="flex justify-center mb-6">
                            <img 
                                :src="userData.image"
                                :alt="userData.name"
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
                                    {{ userData.name || 'Vārda nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    E-pasts
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.email || 'E-pasta nav' }}
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Loma
                                </h3>
                                <p class="text-[1.2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">
                                    students
                                </p>
                            </div>

                            <div class="bg-background rounded-lg p-4">
                                <h3 class="text-[1rem] text-text_light mb-1 [@media(max-width:550px)]:text-[.8rem]">
                                    Izveidots
                                </h3>
                                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">
                                    {{ userData.created_at }}
                                </p>
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
                                    :src="imagePreview || userData.image"
                                    alt="Profile image"
                                    @error="handleImageError"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-button"
                                >
                                <input 
                                    type="file"
                                    ref="imageInput"
                                    @change="handleImageChange"
                                    accept="image/*"
                                    class="hidden"
                                >
                                <button 
                                    @click.prevent="$refs.imageInput.click()"
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
                                    v-model="form.name"
                                    type="text"
                                    required
                                    maxlength="50"
                                    class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-text_dark mb-2">E-pasts <span class="text-button4">*</span></label>
                                <input 
                                    v-model="form.email"
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
                                            v-model="form.old_password"
                                            type="password"
                                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-text_dark mb-2">Jaunā parole <span class="text-button4">*</span></label>
                                        <input 
                                            v-model="form.new_password"
                                            type="password"
                                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-text_dark mb-2">Apstiprināt jauno paroli <span class="text-button4">*</span></label>
                                        <input 
                                            v-model="form.confirm_password"
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
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const { width } = useWindowSize();

const isLoading = ref(true);
const error = ref(null);
const isEditing = ref(false);
const showPasswordFields = ref(false);
const isSubmitting = ref(false);
const imagePreview = ref(null);
const imageInput = ref(null);
const errorList = ref([]);

const form = ref({
    name: '',
    email: '',
    old_password: '',
    new_password: '',
    confirm_password: '',
    image: null
});

const showSidebar = computed(() => store.getters.getShowSidebar);

const getImageUrl = (image) => {
    if (!image) return '/storage/default-avatar.png';
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;

    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.startsWith('/storage/')) {
        cleanPath = cleanPath.replace('/storage/', '');
    }
    
    cleanPath = cleanPath.replace(/^\/+/, '').replace(/\/+$/, '');
    
    // Ensure uploads directory is in the path
    if (!cleanPath.startsWith('uploads/')) {
        cleanPath = `uploads/${cleanPath}`;
    }

    return `/storage/${cleanPath}`;
};

const userData = computed(() => {
    const user = store.getters.getUser;
    if (!user) {
        return {
            name: 'Vārda nav',
            email: 'E-pasta nav',
            image: '/storage/default-avatar.png',
            created_at: 'Nav pieejams'
        };
    }

    return {
        name: user.name || 'Vārda nav',
        email: user.email || 'E-pasta nav',
        image: getImageUrl(user.image),
        created_at: user.created_at ? formatDate(user.created_at) : 'Nav pieejams'
    };
});

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const handleImageError = (event) => {
    if (!event.target.src.includes('default-avatar.png')) {
        event.target.src = '/storage/default-avatar.png';
    }
};

const handleImageChange = (event) => {
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

        form.value.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'Nav pieejams';
    try {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        return `${day}.${month}.${year}`;
    } catch (err) {
        console.error('Error formatting date:', err);
        return 'Nav pieejams';
    }
};

const toggleEditing = () => {
    if (isEditing.value) {
        isEditing.value = false;
        showPasswordFields.value = false;
        imagePreview.value = null;
        errorList.value = [];
        form.value = {
            name: userData.value.name,
            email: userData.value.email,
            old_password: '',
            new_password: '',
            confirm_password: '',
            image: null
        };
    } else {
        isEditing.value = true;
        form.value.name = userData.value.name;
        form.value.email = userData.value.email;
    }
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        errorList.value = [];

        if (showPasswordFields.value) {
            if (!form.value.old_password) {
                throw new Error('Lūdzu, ievadiet pašreizējo paroli');
            }
            if (!form.value.new_password) {
                throw new Error('Lūdzu, ievadiet jauno paroli');
            }
            if (form.value.new_password !== form.value.confirm_password) {
                throw new Error('Jaunās paroles nesakrīt');
            }
            if (form.value.new_password.length < 6) {
                throw new Error('Parolei jābūt vismaz 6 rakstzīmes garai');
            }
        }

        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        
        if (showPasswordFields.value) {
            formData.append('old_password', form.value.old_password);
            formData.append('new_password', form.value.new_password);
            formData.append('confirm_password', form.value.confirm_password);
        }

        if (form.value.image) {
            formData.append('image', form.value.image);
        }

        console.log('Sending form data:', Object.fromEntries(formData));

        const response = await axios.post('/api/user/update-profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        console.log('Server response:', response.data);

        if (response.data.status === 200) {
            // Update store with new user data
            store.commit('setUser', response.data.data);

            // Reset password fields and image
            if (showPasswordFields.value) {
                form.value.old_password = '';
                form.value.new_password = '';
                form.value.confirm_password = '';
                showPasswordFields.value = false;
            }
            form.value.image = null;
            imagePreview.value = null;
            if (imageInput.value) {
                imageInput.value.value = '';
            }

            await Swal.fire({
                title: 'Veiksmīgi!',
                text: 'Jūsu profils ir veiksmīgi rediģēts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });

            isEditing.value = false;
        }
    } catch (error) {
        console.error('Profile update error:', error);
        if (error.response) {
            console.log('Error response:', error.response.data);
        }
        errorList.value = [error.response?.data?.message || error.message || 'Radās kļūda, rediģējot profilu'];
    } finally {
        isSubmitting.value = false;
    }
};

const loadUserData = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        const token = localStorage.getItem('token');
        if (!token) {
            console.error('No token found');
            router.push('/login');
            return;
        }

        const response = await axios.get('/api/user', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.data) {
            store.commit('setUser', {
                ...response.data,
                _updateKey: Date.now()
            });
        } else {
            error.value = 'No user data received';
        }
    } catch (err) {
        console.error('Error loading user data:', err);
        if (err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/login');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else {
            error.value = 'Failed to load profile data. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadUserData();
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