<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Your contents</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <!-- Loading State -->
            <div v-if="loading && !contents.length && contents.length != 0" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>
            
            <!-- Error Message -->
            <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                {{ error }}
            </div>

            <!-- Contents Grid -->
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

                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem] line-clamp-3">
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
                        @click="router.push(`/admin_watch_content/${content.id}`)"
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
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const error = ref(null);
const isLoading = ref(true);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const contents = computed(() => store.getters.getContents);
const storeIsLoading = computed(() => store.getters.getIsLoading);
const loading = computed(() => isLoading.value || storeIsLoading.value);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
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
            
            // Update store instead of reloading
            const updatedContents = contents.value.filter(content => content.id !== contentId);
            store.commit('setContents', updatedContents);
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your content has been deleted.',
                icon: 'success'
            });
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

const formatDate = (date) => {
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    return formattedDate;
};

// Lifecycle
onMounted(async () => {
    try {
        const user = store.getters.getUser;
        if (user) {
            await store.dispatch('loadContents', user.id);
        }
    } catch (error) {
        console.error('Error loading contents:', error);
    } finally {
        isLoading.value = false;
    }
});

// Watch for store loading state changes
watch(storeIsLoading, (newValue) => {
    if (!newValue && contents.value.length === 0) {
        isLoading.value = false;
    }
});
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Add smooth transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Improve loading state */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>