<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">
                Favorītvideo
            </h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
            </div>

            <div v-else-if="error" class="text-center text-button4 text-[1.2rem] mt-[2rem]">
                {{ error }}
                <button 
                    @click="loadLikedContent"
                    class="block mx-auto mt-4 text-button hover:text-text_dark"
                >
                    Mēģināt vēlreiz
                </button>
            </div>

            <div v-else-if="!contents.length" class="text-center text-text_light text-[1.2rem] mt-[2rem]">
                Jūs vēl neesat iepatīkusi nevienu video.
            </div>

            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(33rem,_1fr))] gap-[1rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div 
                    v-for="content in contents" 
                    :key="content.id" 
                    class="bg-base rounded-lg p-[2rem] hover:shadow-lg transition-shadow duration-300"
                >
                    <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                        <img 
                            :src="content.teacher?.image" 
                            :alt="content.teacher?.name"
                            class="h-[4rem] w-[4rem] rounded-full object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]"
                        >
                        <div>
                            <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">
                                {{ content.teacher?.name }}
                            </h3>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">
                                {{ formatDate(content.date) }}
                            </span>
                        </div>
                    </div>

                    <div class="relative group">
                        <img 
                            :src="content.thumb" 
                            :alt="content.title"
                            class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-play text-[3rem] text-white"></i>
                        </div>
                    </div>

                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">
                        {{ content.title }}
                    </h3>
                    <div class="flex justify-center gap-[1rem]">
                        <router-link 
                            :to="'/watch_video/' + content.id"
                            class="flex-1 bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Skatīt video
                        </router-link>
                        <button 
                            @click="() => deleteLike(content.id)"
                            :disabled="isDeleting === content.id"
                            class="flex-1 bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] transition hover:bg-transparent hover:text-button4 disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            {{ isDeleting === content.id ? 'Dzēšana...' : 'Dzēst' }}
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

const contents = ref([]);
const isDeleting = ref(null);
const error = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const user = computed(() => store.getters.getUser);
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

const getThumbnailUrl = (content) => {
    if (!content.url) return content.thumb || '/storage/default-thumbnail.png';
    
    try {
        if (content.url.includes('youtube.com') || content.url.includes('youtu.be')) {
            let videoId = '';
            
            if (content.url.includes('youtu.be/')) {
                videoId = content.url.split('youtu.be/')[1].split('?')[0];
            }
            else if (content.url.includes('youtube.com')) {
                const urlObj = new URL(content.url);
                videoId = urlObj.searchParams.get('v') || urlObj.pathname.split('/').pop();
            }
            
            if (videoId) {
                return `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`;
            }
        }
        
        let cleanPath = content.thumb;
        if (cleanPath) {
            if (cleanPath.includes('/storage/app/public/')) {
                cleanPath = cleanPath.replace('/storage/app/public/', '');
            } else if (cleanPath.includes('storage/app/public/')) {
                cleanPath = cleanPath.replace('storage/app/public/', '');
            }
            cleanPath = cleanPath.replace(/^\/+/, '');
            return `/storage/${cleanPath}`;
        }
        
        return '/storage/default-thumbnail.png';
    } catch (err) {
        console.error('Error getting thumbnail URL:', err);
        return '/storage/default-thumbnail.png';
    }
};

const loadLikedContent = async () => {
    try {
        error.value = null;

        if (!user.value?.id) {
            await store.dispatch('loadUserData');
            if (!user.value?.id) {
                router.push('/');
                return;
            }
        }

        const token = localStorage.getItem('token');
        const headers = { 
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
        };

        const [likesResponse, teachersResponse] = await Promise.all([
            axios.get(`/api/likes/user/${user.value.id}`, { headers }),
            axios.get(`/api/teachers/all`, { headers })
        ]);

        console.log('Likes Response:', likesResponse.data);
        console.log('Teachers Response:', teachersResponse.data);

        const teachersMap = new Map();
        teachersResponse.data.forEach(teacher => {
            console.log('Processing teacher:', teacher);
            if (teacher.id) {
                const cleanTeacherImagePath = teacher.image
                    ?.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                    ?.replace(/^\//, '');
                
                teachersMap.set(Number(teacher.id), {
                    ...teacher,
                    image: cleanTeacherImagePath ? `/storage/${cleanTeacherImagePath}` : '/storage/default-avatar.png'
                });
            }
        });

        contents.value = likesResponse.data.contents.map(content => {
            console.log('Processing content:', content);
            const teacher = teachersMap.get(Number(content.teacher_id));
            console.log('Found teacher for content:', teacher);
            
            return {
                ...content,
                thumb: getThumbnailUrl(content),
                teacher: teacher ? {
                    ...teacher,
                    name: teacher.name || 'Unknown Teacher',
                    image: teacher.image || '/storage/default-avatar.png'
                } : {
                    id: content.teacher_id,
                    name: 'Unknown Teacher',
                    image: '/storage/default-avatar.png'
                }
            };
        });

        const likePromises = contents.value.map(content => 
            axios.post('/api/likes/check', {
                user_id: user.value.id,
                teacher_id: content.teacher.id,
                content_id: content.id
            }, { headers })
        );

        const likeResponses = await Promise.all(likePromises);
            
        contents.value = contents.value.map((content, index) => ({
            ...content,
            like_id: likeResponses[index].data.id
        }));

    } catch (err) {
        console.error('Error loading liked content:', err);
        error.value = 'Failed to load liked content. Please try again.';
    }
};

const deleteLike = async (contentId) => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        const content = contents.value.find(c => c.id === contentId);
        if (!content?.like_id) {
            console.error('Like ID not found for content:', contentId);
            return;
        }

        const result = await Swal.fire({
            title: 'Vai esat pārliecināts?',
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
            isDeleting.value = contentId;
            const token = localStorage.getItem('token');
            const headers = { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            };

            await axios.delete(`/api/likes/delete/${content.like_id}`, { headers });

            contents.value = contents.value.filter(c => c.id !== contentId);

            await store.dispatch('loadUserStats', user.value.id);

            await Swal.fire({
                title: 'Dzēsts!',
                text: 'Favorītvideo ir dzēsts.',
                icon: 'success',
                color: text_dark,
                background: background,
            });
        }
    } catch (err) {
        console.error('Error deleting like:', err);
        Swal.fire({
            title: 'Kļūda!',
            text: 'Neizdevās dzēst favorītvideo. Mēģiniet vēlreiz.',
            icon: 'error',
            color: text_dark,
            background: background,
        });
    } finally {
        isDeleting.value = null;
    }
};

watch(() => user.value?.id, (newId) => {
    if (newId) {
        loadLikedContent();
    }
});

onMounted(() => {
    loadLikedContent();
});
</script>