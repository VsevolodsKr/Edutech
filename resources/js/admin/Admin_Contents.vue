<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Your contents</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>
            
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <!-- Create Content Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">
                        Create new content
                    </h3>
                    <router-link 
                        to="/admin_add_content" 
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Add Content
                    </router-link>
                </div>

                <!-- Content Cards -->
                <div v-for="content in contents" 
                     :key="content.id" 
                     class="bg-base rounded-lg p-[2rem] w-full">
                    <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                        <div>
                            <i :class="[
                                content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                'fas fa-circle-dot text-[1.2rem]'
                            ]"></i>
                            <span :class="[
                                content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                'text-[1.2rem]'
                            ]">
                                {{ content.status }}
                            </span>
                        </div>
                        <div>
                            <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ content.date }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-center mb-4">
                        <img 
                            :src="getImageUrl(content.thumb)" 
                            :alt="content.title"
                            @error="handleImageError"
                            class="w-full h-48 object-cover rounded-lg"
                        >
                    </div>

                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ content.title }}
                    </h3>

                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">
                        {{ content.description }}
                    </div>

                    <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                        <button 
                            @click="router.push(`/admin_contents/update/${content.id}`)"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Update
                        </button>
                        <button 
                            @click="handleDelete(content.id)"
                            class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Delete
                        </button>
                    </div>

                    <button 
                        @click="router.push(`/admin_contents/${content.id}`)"
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Content
                    </button>
                </div>
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

// State
const contents = ref([]);
const isLoading = ref(true);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
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

const loadContents = async () => {
    try {
        isLoading.value = true;
        const userData = await loadUserData();
        if (!userData) return;

        const response = await axios.get(`/api/contents/${userData.id}`);
        contents.value = response.data.map(content => ({
            ...content,
            thumb: getImageUrl(content.thumb)
        }));
    } catch (error) {
        console.error('Error loading contents:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load contents',
            icon: 'error'
        });
    } finally {
        isLoading.value = false;
    }
};

const handleDelete = async (contentId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Content will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/contents/delete/${contentId}`);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your content has been deleted.',
                icon: 'success'
            });

            // Reload contents instead of refreshing the page
            await loadContents();
        }
    } catch (error) {
        console.error('Error deleting content:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete content',
            icon: 'error'
        });
    }
};

const defaultThumb = '/images/default-thumb.png';

const getImageUrl = (image) => {
    if (!image) return defaultThumb;
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;

    // Clean up the storage path
    let cleanPath = image;
    if (cleanPath.includes('/storage/app/public/')) {
        cleanPath = cleanPath.replace('/storage/app/public/', '');
    } else if (cleanPath.includes('storage/app/public/')) {
        cleanPath = cleanPath.replace('storage/app/public/', '');
    }

    // Remove any leading slashes
    cleanPath = cleanPath.replace(/^\/+/, '');
    return `${window.location.origin}/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    event.target.src = defaultThumb;
};

// Lifecycle
onMounted(() => {
    loadContents();
});
</script>