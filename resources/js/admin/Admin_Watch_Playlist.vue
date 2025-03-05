<template>
<div>
    <Admin_Header />
    <section :class="sectionClasses">
        <div v-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
            {{ error }}
        </div>

        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Playlist Details</h1>
        <hr class="border-[#ccc] mb-[2rem]">

        <div v-if="isLoading" class="flex justify-center items-center min-h-[200px]">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
        </div>

        <div v-else-if="playlist" class="flex items-center gap-[3rem] flex-wrap bg-base p-[1rem] rounded-lg">
            <div class="flex-[1_1_20rem]">
                <form method="post" class="mb-[1rem]">
                    <button type="submit" class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:px-[.5rem] [@media(max-width:550px)]:py-[.2rem]">
                        <i class="far fa-bookmark text-[1rem] mr-[.8rem] [@media(max-width:550px)]:text-[.7rem]"></i>
                        <span class="text-1rem [@media(max-width:550px)]:text-[.7rem]">Save Playlist</span>
                    </button>
                </form>
                <div class="relative">
                    <img :src="playlist.playlist.thumb" class="h-[20rem] w-full object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]" :alt="playlist.playlist.title">
                    <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1rem] [@media(max-width:550px)]:text-[.7rem]">{{ countContents }}</span>
                </div>
            </div>
            <div class="flex-[1_1_40rem]">
                <div>
                    <h3 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">{{ playlist.playlist.title }}</h3>
                    <p class="py-[1rem] leading-2 text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ playlist.playlist.description }}</p>
                </div>
                <div class="flex gap-[1rem]">
                    <button 
                        @click="navigateToUpdate(playlist.playlist.id)"
                        class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Update Playlist
                    </button>
                    <button 
                        @click="deletePlaylist(playlist.playlist.id)"
                        :disabled="isDeleting"
                        class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isDeleting" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-base" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Deleting...
                        </span>
                        <span v-else>Delete Playlist</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contents Section -->
        <div class="pt-[4rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Playlist Videos</h1>
            <hr class="border-[#ccc] mb-[2rem]">

            <div v-if="isLoadingContents" class="flex justify-center items-center min-h-[200px]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
            </div>

            <div v-else class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <TransitionGroup name="content-list" tag="div" class="contents">
                    <div v-for="content in contents" :key="content.id" class="bg-base rounded-lg p-[2rem] w-full">
                        <!-- Status and Date -->
                        <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                            <div>
                                <i :class="[content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot text-[1.2rem]']"></i>
                                <span :class="[content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">{{ content.status }}</span>
                            </div>
                            <div>
                                <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ content.date }}</span>
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div class="relative">
                            <img :src="content.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]" :alt="content.title">
                        </div>

                        <!-- Title -->
                        <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ content.title }}</h3>

                        <!-- Action Buttons -->
                        <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                            <button 
                                @click="navigateToContentUpdate(content.id)"
                                class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                            >
                                Update
                            </button>
                            <button 
                                @click="deleteContent(content.id)"
                                :disabled="isDeletingContent === content.id"
                                class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="isDeletingContent === content.id" class="inline-flex items-center">
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
                            @click="navigateToContentView(content.id)"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            View Content
                        </button>
                    </div>
                </TransitionGroup>
            </div>
        </div>
    </section>
    <Admin_Sidebar />
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useWindowSize } from '@vueuse/core'
import store from '../store/store'
import Swal from 'sweetalert2'
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'

// Router
const router = useRouter()
const route = useRoute()

// State
const { width } = useWindowSize()
const isLoading = ref(true)
const isLoadingContents = ref(true)
const error = ref(null)
const playlist = ref(null)
const contents = ref([])
const countContents = ref(0)
const isDeleting = ref(false)
const isDeletingContent = ref(null)

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] bg-background pr-[2rem] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
])

// Methods
const fetchPlaylist = async () => {
    try {
        isLoading.value = true
        error.value = null
        const response = await axios.get(`/api/playlists/find/${route.params.id}`)
        playlist.value = response.data
        playlist.value.playlist.thumb = new URL(playlist.value.playlist.thumb, import.meta.url)
        
        // Fetch content count
        const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.playlist.id}/amount`)
        countContents.value = countResponse.data
    } catch (err) {
        console.error('Error fetching playlist:', err)
        error.value = 'Failed to load playlist. Please try again.'
    } finally {
        isLoading.value = false
    }
}

const fetchContents = async () => {
    if (!playlist.value) return

    try {
        isLoadingContents.value = true
        const response = await axios.get(`/api/playlists/${playlist.value.playlist.id}/contents`)
        contents.value = response.data
        contents.value.forEach(content => {
            content.thumb = new URL(content.thumb, import.meta.url)
        })
    } catch (err) {
        console.error('Error fetching contents:', err)
        error.value = 'Failed to load playlist contents. Please try again.'
    } finally {
        isLoadingContents.value = false
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
            isDeleting.value = true
            await axios.delete(`/api/playlists/delete/${id}`)
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Your playlist has been deleted.',
                icon: 'success',
                color: text_dark,
                background: background
            })

            router.push('/admin_playlists')
        }
    } catch (err) {
        console.error('Error deleting playlist:', err)
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete playlist. Please try again.',
            icon: 'error'
        })
    } finally {
        isDeleting.value = false
    }
}

const deleteContent = async (id) => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')

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
        })

        if (result.isConfirmed) {
            isDeletingContent.value = id
            await axios.delete(`/api/contents/delete/${id}`)
            
            await Swal.fire({
                title: 'Deleted!',
                text: 'Content has been deleted.',
                icon: 'success',
                color: text_dark,
                background: background
            })

            // Refresh contents and count
            await fetchContents()
            const countResponse = await axios.get(`/api/contents/playlist/${playlist.value.playlist.id}/amount`)
            countContents.value = countResponse.data
        }
    } catch (err) {
        console.error('Error deleting content:', err)
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete content. Please try again.',
            icon: 'error'
        })
    } finally {
        isDeletingContent.value = null
    }
}

const navigateToUpdate = (id) => {
    router.push(`/admin_playlists/update/${id}`)
}

const navigateToContentUpdate = (id) => {
    router.push(`/admin_contents/update/${id}`)
}

const navigateToContentView = (id) => {
    router.push(`/admin_contents/${id}`)
}

// Lifecycle
onMounted(async () => {
    await fetchPlaylist()
    await fetchContents()
})
</script>

<style scoped>
.content-list-move,
.content-list-enter-active,
.content-list-leave-active {
    transition: all 0.5s ease;
}

.content-list-enter-from,
.content-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.content-list-leave-active {
    position: absolute;
}
</style>