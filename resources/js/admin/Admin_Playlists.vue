<template>
<div>
    <Admin_Header />
    <section :class="sectionClasses">
        <div v-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
            {{ error }}
        </div>

        <h1 class="text-[1.5rem] text-text_dark capitalize">Your playlists</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

        <div v-if="isLoading" class="flex justify-center items-center min-h-[200px]">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
        </div>

        <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
            <!-- Create New Playlist Card -->
            <div class="bg-base rounded-lg p-[2rem] w-full">
                <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Create new playlist</h3>
                <router-link to="/admin_add_playlist" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add Playlist</router-link>
            </div>

            <!-- Playlist Cards -->
            <TransitionGroup name="playlist-list" tag="div" class="contents">
                <div v-for="(playlist, index) in playlists" :key="playlist.id" class="bg-base rounded-lg p-[2rem] w-full">
                    <!-- Status and Date -->
                    <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                        <div>
                            <i :class="[playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot text-[1.2rem]']"></i>
                            <span :class="[playlist.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">{{ playlist.status }}</span>
                        </div>
                        <div>
                            <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ playlist.date }}</span>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="relative">
                        <img :src="playlist.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]" :alt="playlist.title">
                        <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[1rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1.2rem] [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.7rem]">
                            {{ contentsCount[index] || 0 }}
                        </span>
                    </div>

                    <!-- Title and Description -->
                    <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ playlist.title }}</h3>
                    <p class="text-text_light mb-[1rem]">{{ playlist.description }}</p>

                    <!-- Action Buttons -->
                    <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                        <button 
                            @click="navigateToUpdate(playlist.id)"
                            class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Update
                        </button>
                        <button 
                            @click="deletePlaylist(playlist.id)"
                            :disabled="isDeleting === playlist.id"
                            class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isDeleting === playlist.id" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-base" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                            <span v-else>Delete</span>
                        </button>
                    </div>

                    <button 
                        @click="navigateToView(playlist.id)"
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Playlist
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </section>
    <Admin_Sidebar />
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useWindowSize } from '@vueuse/core'
import store from '../store/store'
import Swal from 'sweetalert2'
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'

// Router
const router = useRouter()

// State
const { width } = useWindowSize()
const isLoading = ref(true)
const error = ref(null)
const teacher = ref(null)
const playlists = ref([])
const contentsCount = ref([])
const isDeleting = ref(null)

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
])

// Methods
const getUser = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        teacher.value = response.data
        teacher.value.image = new URL(teacher.value.image, import.meta.url)
        return teacher.value
    } catch (err) {
        console.error('Error fetching user:', err)
        error.value = 'Failed to load user data'
        if (!localStorage.getItem('token')) {
            router.push('/')
        }
        return null
    }
}

const getPlaylists = async () => {
    try {
        isLoading.value = true
        error.value = null
        const user = await getUser()
        if (!user) return

        const response = await axios.get(`/api/playlists/${user.id}`)
        playlists.value = response.data
        playlists.value.forEach(playlist => {
            playlist.thumb = new URL(playlist.thumb, import.meta.url)
            fetchContentsCount(playlist.id)
        })
    } catch (err) {
        console.error('Error fetching playlists:', err)
        error.value = 'Failed to load playlists. Please try again.'
    } finally {
        isLoading.value = false
    }
}

const fetchContentsCount = async (playlistId) => {
    try {
        const response = await axios.get(`/api/contents/playlist/${playlistId}/amount`)
        contentsCount.value.push(response.data)
    } catch (err) {
        console.error(`Error fetching contents count for playlist ${playlistId}:`, err)
        contentsCount.value.push(0)
    }
}

const deletePlaylist = async (id) => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')

        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'Playlist will be deleted permanently!',
            icon: 'warning',
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: button4,
            confirmButtonText: 'Yes, delete it!'
        })

        if (result.isConfirmed) {
            isDeleting.value = id
            await axios.delete(`/api/playlists/delete/${id}`)
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your playlist has been deleted.',
                icon: 'success',
                color: text_dark,
                background: background
            })

            // Refresh playlists
            await getPlaylists()
        }
    } catch (err) {
        console.error('Error deleting playlist:', err)
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete playlist. Please try again.',
            icon: 'error'
        })
    } finally {
        isDeleting.value = null
    }
}

const navigateToUpdate = (id) => {
    router.push(`/admin_playlists/update/${id}`)
}

const navigateToView = (id) => {
    router.push(`/admin_playlists/${id}`)
}

// Lifecycle
onMounted(async () => {
    await getPlaylists()
})
</script>

<style scoped>
.playlist-list-move,
.playlist-list-enter-active,
.playlist-list-leave-active {
    transition: all 0.5s ease;
}

.playlist-list-enter-from,
.playlist-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.playlist-list-leave-active {
    position: absolute;
}
</style>