<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Komentāri
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadComments"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Mēģināt vēlreiz
                </button>
            </div>

            <div v-else-if="!comments.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                Jūsu saturam nav komentāri.
            </div>

            <div v-else class="flex flex-col gap-[1rem] justify-start items-start pr-[1rem] [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="comment in comments" 
                    :key="comment.id" 
                    class="bg-base rounded-lg p-[2rem] w-full hover:shadow-lg transition-shadow duration-300"
                >
                    <div class="flex items-center justify-between text-[1.2rem] [@media(max-width:550px)]:text-[1rem] mb-[1rem]">
                        <div class="flex items-center gap-4">
                            <img 
                                :src="comment.user?.image || '/storage/default-avatar.png'" 
                                :alt="comment.user?.name"
                                class="h-[3rem] w-[3rem] rounded-full object-cover [@media(max-width:550px)]:h-[2rem] [@media(max-width:550px)]:w-[2rem]"
                            >
                            <div class="flex flex-col">
                                <span class="text-text_dark font-medium">{{ comment.user?.name }}</span>
                                <span class="text-text_light text-sm">{{ formatDate(comment.date) }}</span>
                            </div>
                        </div>
                        <router-link 
                            :to="'/admin_watch_content/' + comment.content?.encrypted_id"
                            class="text-button hover:text-button1 transition-colors duration-200"
                        >
                            Skatīt video
                        </router-link>
                    </div>

                    <div class="mb-[1rem] text-text_dark">
                        <span class="font-medium">Video:</span> {{ comment.content?.title }}
                    </div>

                    <div class="relative">
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">
                            {{ comment.comment }}
                        </div>
                        <div class="absolute top-[-0.5rem] left-[1.5rem] w-[1rem] h-[1rem] bg-background transform rotate-45"></div>
                    </div>

                    <div class="flex justify-start mt-[1rem]">
                        <button 
                            @click="() => deleteComment(comment.id)"
                            :disabled="isDeleting === comment.id"
                            class="max-w-[12rem] bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] px-[2rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            {{ isDeleting === comment.id ? 'Dzēšana...' : 'Dzēst komentāru' }}
                        </button>
                    </div>
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

const isDeleting = ref(null);
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const comments = computed(() => store.getters.getComments);
const isLoading = computed(() => store.getters.getIsLoading);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
};

const loadComments = async () => {
    try {
        isLoading.value = true;
        error.value = null;

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
            error.value = 'Nav pieejama lietotāja informācija';
            return;
        }

        console.log('Loading comments for teacher:', user.encrypted_id);
        const response = await axios.get(`/api/comments/teacher/${user.encrypted_id}`, { headers });
        console.log('Comments response:', response.data);

        if (!response.data.comments) {
            console.error('No comments data in response');
            error.value = 'Neizdevās ielādēt komentārus';
            return;
        }

        comments.value = response.data.comments.map(comment => {
            const cleanImagePath = comment.user?.image
                ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                ?.replace(/^\//, '');

            return {
                ...comment,
                user: {
                    ...comment.user,
                    image: cleanImagePath ? `/storage/${cleanImagePath}` : '/storage/default-avatar.png'
                },
                content: comment.content ? {
                    ...comment.content,
                    thumb: comment.content.thumb ? `/storage/${comment.content.thumb.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}` : '/storage/default-thumb.png'
                } : null
            };
        });

        console.log('Processed comments:', comments.value);
    } catch (err) {
        console.error('Error loading comments:', err);
        error.value = 'Neizdevās ielādēt komentārus';
    } finally {
        isLoading.value = false;
    }
};

const deleteComment = async (commentId) => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Šī darbība nav atsaukama.',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Jā, dzēst',
            cancelButtonText: 'Atcelt'
        });

        if (result.isConfirmed) {
            isDeleting.value = commentId;
            await axios.delete(`/api/comments/delete/${commentId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Komentārs ir dzēsts.',
                icon: 'success',
                color: text_dark,
                background: background
            });

            await loadComments();
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst komentāru',
            icon: 'error',
            color: text_dark,
            background: background
        });
    } finally {
        isDeleting.value = null;
    }
};

onMounted(() => {
    loadComments();
});
</script> 