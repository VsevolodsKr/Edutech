<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <div class="bg-base rounded-lg p-[2rem] max-w-4xl mx-auto">
                <h1 class="text-[1.5rem] text-text_dark mb-[2rem]">Mans profils</h1>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="flex justify-center mb-8">
                        <div class="relative">
                            <img 
                                :src="imagePreview || (userData ? userData.image : '/storage/default-avatar.png')" 
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

                    <TransitionGroup 
                        name="message" 
                        tag="div" 
                        class="space-y-2"
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

                    <div>
                        <label class="block text-text_dark mb-2">Vārds</label>
                        <input 
                            v-model="form.name"
                            type="text"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-text_dark mb-2">E-pasts</label>
                        <input 
                            v-model="form.email"
                            type="email"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                            required
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
                                <label class="block text-text_dark mb-2">Pašreizējā parole</label>
                                <input 
                                    v-model="form.old_password"
                                    type="password"
                                    class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-text_dark mb-2">Jaunā parole</label>
                                <input 
                                    v-model="form.new_password"
                                    type="password"
                                    class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line focus:border-button focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-text_dark mb-2">Apstiprināt jauno paroli</label>
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
                </form>
            </div>
        </section>
        <Developer_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Developer_Header from '../components/Developer_Header.vue';
import Developer_Sidebar from '../components/Developer_Sidebar.vue';
import store from '../store/store';

const { width } = useWindowSize();

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
    
    if (!cleanPath.startsWith('uploads/')) {
        cleanPath = `uploads/${cleanPath}`;
    }

    return `/storage/${cleanPath}`;
};

const userData = computed(() => {
    return store.getters.getUser;
});

const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const form = ref({
    name: '',
    email: '',
    old_password: '',
    new_password: '',
    confirm_password: '',
    image: null
});

const imageInput = ref(null);
const imagePreview = ref(null);
const showPasswordFields = ref(false);
const isSubmitting = ref(false);
const messages = ref([]);
const errorStatus = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            messages.value = ['Attēla izmērs nedrīkst pārsniegt 2MB'];
            errorStatus.value = 500;
            event.target.value = '';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            messages.value = ['Lūdzu, izvēlieties derīgu attēla failu (JPEG, PNG, vai GIF)'];
            errorStatus.value = 500;
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

const handleImageError = (event) => {
    if (!event.target.src.includes('default-avatar.png')) {
        event.target.src = '/storage/default-avatar.png';
    }
};

const handleSubmit = async () => {
    try {
        isSubmitting.value = true;
        messages.value = [];
        errorStatus.value = null;

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

        const response = await axios.post('/api/developer/profile/update', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        console.log('Server response:', response.data);

        if (response.data.status === 200) {
            messages.value = Array.isArray(response.data.message) 
                ? response.data.message 
                : [response.data.message];
            errorStatus.value = 200;

            store.commit('setUser', response.data.data);

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
        }
    } catch (error) {
        console.error('Profile update error:', error);
        if (error.response) {
            console.log('Error response:', error.response.data);
        }
        messages.value = [error.response?.data?.message || error.message || 'Kļūda atjaunojot profilu'];
        errorStatus.value = 500;
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    if (userData.value) {
        form.value.name = userData.value.name;
        form.value.email = userData.value.email;
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