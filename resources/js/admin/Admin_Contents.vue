<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Jūsu video</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <div v-if="loading && !contents.length && contents.length != 0" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>
            
            <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                {{ error }}
            </div>
            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h3 class="text-[2rem] text-text_dark text-center pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">
                        Izveidot jaunu video
                    </h3>
                    <router-link 
                        to="/admin_add_content" 
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Pievienot video
                    </router-link>
                </div>

                <template v-if="contents.length === 0">
                    <div class="bg-base rounded-lg p-[2rem] w-full">
                        <p class="text-center text-text_light">Jums vēl nav pievienotu video. Sāciet, pievienojot savu pirmo video!</p>
                    </div>
                </template>

                <template v-else>
                    <div v-for="content in paginatedContents" 
                         :key="content.id" 
                         class="bg-base rounded-lg p-[2rem] w-full">
                        <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                            <div>
                                <i :class="[
                                    content.status === 'Aktīvs' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                    'fas fa-circle-dot text-[1.2rem] mr-[.3rem]'
                                ]"></i>
                                <span :class="[
                                    content.status === 'Aktīvs' ? 'text-[#0eed46]' : 'text-[#e83731]',
                                    'text-[1.2rem]'
                                ]">
                                    {{ content.status }}
                                </span>
                            </div>
                            <div>
                                <i class="fas fa-calendar text-button text-[1.2rem] mr-[.3rem]"></i>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                    {{ formatDate(content.date) }}
                                </span>
                            </div>
                        </div>

                        <div class="relative">
                            <img 
                                :src="getImageUrl(content.thumb)" 
                                :alt="content.title"
                                @error="handleImageError"
                                class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                        </div>

                        <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                            {{ content.title }}
                        </h3>

                        <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem] line-clamp-3">
                            {{ content.description }}
                        </div>

                        <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                            <button 
                                @click="router.push(`/admin_contents/update/${content.encrypted_id}`)"
                                class="bg-button3 text-base text-center border-2 border-button3 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button3 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Rediģēt
                            </button>
                            <button 
                                @click="handleDelete(content.encrypted_id)"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Dzēst
                            </button>
                        </div>

                        <button 
                            @click="router.push(`/admin_watch_content/${content.encrypted_id}`)"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Skatīt video
                        </button>
                    </div>
                </template>
            </div>

            <div v-if="contents.length > itemsPerPage" class="flex justify-center items-center gap-4 mt-8">
                <button 
                    @click="currentPage > 1 && (currentPage--)"
                    :disabled="currentPage === 1"
                    class="px-4 py-2 rounded-lg bg-button text-base border-2 border-button disabled:opacity-50 disabled:cursor-not-allowed hover:bg-base hover:text-button transition-colors duration-200"
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="text-text_dark">
                    Lapa {{ currentPage }} no {{ totalPages }}
                </span>
                <button 
                    @click="currentPage < totalPages && (currentPage++)"
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 rounded-lg bg-button text-base border-2 border-button disabled:opacity-50 disabled:cursor-not-allowed hover:bg-base hover:text-button transition-colors duration-200"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
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

const isLoading = ref(true);
const error = ref(null);
const contents = computed(() => store.getters.getContents);
const storeIsLoading = computed(() => store.getters.getIsLoading);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const currentPage = ref(1);
const itemsPerPage = 9;

const totalPages = computed(() => Math.ceil(contents.value.length / itemsPerPage));

const paginatedContents = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return contents.value.slice(start, end);
});

const getImageUrl = (image) => {
    if (!image) return '/storage/default-thumbnail.png';
    if (image.startsWith('data:')) return image;
    if (image.startsWith('http')) return image;
    return `/storage/${image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`;
};

const handleImageError = (event) => {
    event.target.src = '/storage/default-thumbnail.png';
};

const handleDelete = async (encryptedId) => {
    const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
    const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
    const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

    try {
        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Video tiks dzēsts!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Jā, dzēst!',
            cancelButtonText: 'Atcelt'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/contents/delete/${encryptedId}`);
            
            const updatedContents = contents.value.filter(content => content.encrypted_id !== encryptedId);
            store.commit('setContents', updatedContents);
            
            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Video ir dzēsts.',
                icon: 'success'
            });
        }
    } catch (error) {
        console.error('Error deleting content:', error);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst video',
            icon: 'error'
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
};

onMounted(async () => {
    try {
        isLoading.value = true;
        error.value = null;
        
        const user = store.getters.getUser;
        if (!user || !user.encrypted_id) {
            router.push('/login');
            return;
        }

        await store.dispatch('loadContents', user.encrypted_id);
    } catch (err) {
        console.error('Error in onMounted:', err);
        error.value = 'Neizdevās ielādēt video: ' + (err.message || 'Nezināma kļūda');
    } finally {
        isLoading.value = false;
    }
});

watch(storeIsLoading, (newValue) => {
    isLoading.value = newValue;
});

watch(contents, (newContents) => {
    if (!newContents || newContents.length === 0) {
        error.value = null;
    }
}, { deep: true });

watch(contents, () => {
    currentPage.value = 1;
});
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

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