<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">
                Jūsu komentāri
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
                Jūs vēl neesat izveidojis nevienu komentāru.
            </div>

            <div v-else class="flex flex-col gap-[1rem] justify-start items-start pr-[1rem] [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="comment in comments" 
                    :key="comment.id" 
                    class="bg-base rounded-lg p-[2rem] w-full hover:shadow-lg transition-shadow duration-300"
                >
                    <div class="flex items-center justify-between text-[1.2rem] [@media(max-width:550px)]:text-[1rem] mb-[1rem]">
                        <div class="flex items-center gap-2">
                            <span class="text-text_light">{{ formatDate(comment.date) }}</span>
                            <span class="text-text_dark">{{ comment.content?.title }}</span>
                        </div>
                        <router-link 
                            :to="'/watch_video/' + comment.content?.encrypted_id"
                            class="text-button hover:text-button1 transition-colors duration-200"
                        >
                            Skatīt saturu
                        </router-link>
                    </div>

                    <div class="relative">
                        <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-7 [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">
                            {{ comment.comment }}
                        </div>
                        <div class="absolute top-[-0.5rem] left-[1.5rem] w-[1rem] h-[1rem] bg-background transform rotate-45"></div>
                    </div>

                    <div class="flex justify-start gap-[1rem] mt-[1rem]">
                        <router-link 
                            :to="'/edit_comment/' + comment.encrypted_id"
                            class="flex-1 max-w-[6rem] bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button2 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            Rediģēt
                        </router-link>
                        <button 
                            @click="() => deleteComment(comment.id)"
                            :disabled="isDeleting === comment.id"
                            class="flex-1 max-w-[6rem] bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:max-w-[7rem]"
                        >
                            {{ isDeleting === comment.id ? 'Dzēšana...' : 'Dzēst' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

const comments = ref([]);
const isDeleting = ref(null);
const error = ref(null);
const isLoading = ref(true);

const showSidebar = computed(() => store.getters.getShowSidebar);
const user = computed(() => {
    const storedUser = store.getters.getUser;
    if (!storedUser) return null;

    const imageUrl = storedUser.image ? 
        (storedUser.image.startsWith('http') ? 
            storedUser.image : 
            `${window.location.origin}/storage/${storedUser.image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`) :
        `${window.location.origin}/storage/default-avatar.png`;

    return {
        ...storedUser,
        image: imageUrl
    };
});

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

        if (!user.value?.id) {
            await store.dispatch('loadUserData');
            if (!user.value?.id) {
                router.push('/');
                return;
            }
        }

        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/');
            return;
        }

        const headers = { 
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        };

        if (!user.value.encrypted_id) {
            console.error('No encrypted ID available');
            error.value = 'Neizdevās ielādēt lietotāja datus. Lūdzu, mēģiniet vēlreiz.';
            return;
        }

        console.log('Loading comments for user:', user.value.encrypted_id);
        const response = await axios.get(`/api/comments/user/${user.value.encrypted_id}`, { headers });
        console.log('Comments response:', response.data);

        if (!response.data.comments) {
            console.error('No comments data in response');
            error.value = 'Neizdevās ielādēt komentārus. Lūdzu, mēģiniet vēlreiz.';
            return;
        }

        comments.value = response.data.comments.map(comment => {
            let cleanThumbPath = comment.content?.thumb;
            if (cleanThumbPath) {
                cleanThumbPath = cleanThumbPath
                    .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                    .replace(/^\//, '');
                cleanThumbPath = `/storage/${cleanThumbPath}`;
            }

            return {
                ...comment,
                content: comment.content ? {
                    ...comment.content,
                    thumb: cleanThumbPath || '/storage/default-thumbnail.png'
                } : null
            };
        });

        console.log('Processed comments:', comments.value);
    } catch (err) {
        console.error('Error loading comments:', err);
        error.value = 'Neizdevās ielādēt komentārus. Lūdzu, mēģiniet vēlreiz.';
    } finally {
        isLoading.value = false;
    }
};

const deleteComment = async (commentId) => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
            text: 'Šis komentārs tiks dzēsts.',
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
            const token = localStorage.getItem('token');
            const headers = { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            };

            await axios.delete(`/api/comments/delete/${commentId}`, { headers });

            comments.value = comments.value.filter(comment => comment.id !== commentId);
            
            store.commit('decrementStat', 'comments');
            await store.dispatch('loadUserStats', user.value.encrypted_id);

            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Jūsu komentārs ir dzēsts.',
                icon: 'success',
                color: text_dark,
                background: background,
            });
        }
    } catch (err) {
        console.error('Error deleting comment:', err);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst komentāru. Mēģiniet vēlreiz.',
            icon: 'error',
            color: text_dark,
            background: background,
        });
    } finally {
        isDeleting.value = null;
    }
};

watch(() => user.value?.id, async (newId) => {
    if (newId) {
        await loadComments();
    }
}, { immediate: true });
</script>