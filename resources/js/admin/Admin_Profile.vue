<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark">Skolotāja profils</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                
                <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[2rem]">
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
                                <p class="text-[1.2rem] text-text_light">Kursi</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.contents }}</h3>
                                <p class="text-[1.2rem] text-text_light">Video</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.likes }}</h3>
                                <p class="text-[1.2rem] text-text_light">Favorītvideo</p>
                            </div>
                            <div class="text-center p-4 bg-background rounded-lg">
                                <h3 class="text-[1.8rem] text-button">{{ statistics.comments }}</h3>
                                <p class="text-[1.2rem] text-text_light">Komentāri</p>
    </div>
            </div>
        </div>
                    
                    <div class="bg-base rounded-lg p-[2rem]">
                        <h3 class="text-[1.5rem] text-text_dark mb-4">Rediģēt profilu</h3>
                        
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
                                    Profila attēls
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
                                    Vārds <span class="text-button4">*</span>
                                </label>
                                <input 
                                    v-model="formData.name"
                                    type="text"
                                    required
                                    maxlength="50"
                                    placeholder="Ievadiet savu pilno vārdu..."
                                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                    E-pasts <span class="text-button4">*</span>
                                </label>
                                <input 
                                    v-model="formData.email"
                                    type="email"
                                    required
                                    maxlength="50"
                                    placeholder="Ievadiet savu e-pastu..."
                                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                    Vecā parole
                                </label>
                                <input 
                                    v-model="formData.old_password"
                                    type="password"
                                    placeholder="Ievadiet savu veco paroli..."
                                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                    Jauna parole
                                </label>
                                <input 
                                    v-model="formData.new_password"
                                    type="password"
                                    placeholder="Ievadiet jauno paroli..."
                                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
            </div>

                            <div class="mb-4">
                                <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                    Apstiprināt paroli
                                </label>
                                <input 
                                    v-model="formData.confirm_password"
                                    type="password"
                                    placeholder="Apstipriniet jauno paroli..."
                                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                >
        </div>

                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                {{ isSubmitting ? 'Rediģēšana...' : 'Rediģēt profilu' }}
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

const defaultAvatar = '/images/default-avatar.png';

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

        const [profileResponse, statsResponse] = await Promise.all([
            axios.get('/api/user', { headers }),
            axios.get('/api/admin/statistics', { headers })
        ]);

        if (!profileResponse.data.profession) {
            throw new Error('Unauthorized access');
        }

        console.log('API Response Image:', profileResponse.data.image);

        let imagePath = profileResponse.data.image;
        if (imagePath && imagePath.includes('/storage/app/public/')) {
            imagePath = imagePath.replace('/storage/app/public/', '');
        }

        adminData.value = {
            ...profileResponse.data,
            image: imagePath
        };

        console.log('Stored Image URL:', adminData.value.image);

        statistics.value = {
            playlists: await fetchPlaylistCount(profileResponse.data.id),
            contents: await fetchContentCount(profileResponse.data.id),
            likes: statsResponse.data.likes || 0,
            comments: statsResponse.data.comments || 0
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
        if (file.size > 2 * 1024 * 1024) {
            errorList.value = ['Attēla izmērs nedrīkst pārsniegt 2MB'];
            event.target.value = '';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errorList.value = ['Lūdzu, izvēlieties derīgu attēla failu (JPG, PNG, GIF)'];
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

        const hasOldPassword = !!formData.value.old_password;
        const hasNewPassword = !!formData.value.new_password;
        const hasConfirmPassword = !!formData.value.confirm_password;

        if (hasOldPassword || hasNewPassword || hasConfirmPassword) {
            if (!hasOldPassword || !hasNewPassword || !hasConfirmPassword) {
                errorList.value = ['Visas paroles jābūt aizpildītām, lai mainītu paroli'];
                return;
            }
            if (formData.value.new_password !== formData.value.confirm_password) {
                errorList.value = ['Jauna parole un apstiprinātā parole nesakrīt'];
                return;
            }
        }

        const data = new FormData();

        if (formData.value.name !== adminData.value.name) {
            data.append('name', formData.value.name);
        }
        if (formData.value.email !== adminData.value.email) {
            data.append('email', formData.value.email);
        }

        if (hasOldPassword && hasNewPassword && hasConfirmPassword) {
            data.append('old_password', formData.value.old_password);
            data.append('new_password', formData.value.new_password);
            data.append('confirm_password', formData.value.confirm_password);
        }

        if (formData.value.image instanceof File) {
            data.append('image', formData.value.image);
        }

        if ([...data.entries()].length === 0) {
            errorList.value = ['Nav jāmaina'];
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

        store.commit('setUser', response.data.user);

        formData.value.old_password = '';
        formData.value.new_password = '';
        formData.value.confirm_password = '';
        if (fileInput.value) {
            fileInput.value.value = '';
        }
        formData.value.image = null;

        await loadAdminData();

        store.commit('setReloadSidebar', true);

        await Swal.fire({
            title: 'Veiksmīgi!',
            text: 'Jūsu profils ir veiksmīgi rediģēts',
            icon: 'success'
        });
    } catch (error) {
        console.error('Error updating profile:', error);
        if (error.response?.data?.message) {
            errorList.value = Array.isArray(error.response.data.message) 
                ? error.response.data.message 
                : [error.response.data.message];
        } else {
            errorList.value = ['Radās kļūda, rediģējot profilu'];
        }
    } finally {
        isSubmitting.value = false;
    }
};

const getImageUrl = (image) => {
    if (!image) {
        return defaultAvatar;
    }
    
    if (image.startsWith('data:')) {
        return image;
    }
    
    if (image.startsWith('http')) {
        return image;
    }

    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.includes('storage/app/public/')) {
        cleanPath = cleanPath.replace('storage/app/public/', '');
    }

    const url = `${window.location.origin}/storage/${cleanPath}`;
    return url;
};

const handleImageError = (event) => {
    event.target.src = defaultAvatar;
};

onMounted(() => {
    loadAdminData();
});
</script>