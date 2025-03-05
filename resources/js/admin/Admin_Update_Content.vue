<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
            </div>

            <div v-else-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg">
                <p class="[@media(max-width:550px)]:text-[.7rem]">
                    <i class="fa fa-warning"></i> {{ error }}
                </p>
            </div>

            <div v-else>
                <h1 class="text-[1.5rem] text-text_dark capitalize">Update Content</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <!-- Success Message -->
                        <div v-if="successMessage" class="bg-[#b6f5b5] text-[#378c35] p-4 rounded-lg mb-4">
                            <p class="[@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-check"></i> {{ successMessage }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Video status <span class="text-button4">*</span>
                            </label>
                            <select 
                                v-model="formData.status"
                                :disabled="isSubmitting"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                required
                            >
                                <option value="" disabled>Select status...</option>
                                <option value="active">Active</option>
                                <option value="deactive">Deactive</option>
                            </select>
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Video title <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.title"
                                type="text"
                                placeholder="Enter content title..."
                                :disabled="isSubmitting"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                required
                                maxlength="50"
                            >
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Video description <span class="text-button4">*</span>
                            </label>
                            <textarea 
                                v-model="formData.description"
                                placeholder="Enter content description..."
                                :disabled="isSubmitting"
                                class="h-[20rem] resize-none w-full rounded-lg bg-background p-[.8rem] text-[1rem] text-text_light outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                required
                                maxlength="1000"
                            ></textarea>
                        </div>

                        <!-- Playlist -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Video playlist <span class="text-button4">*</span>
                            </label>
                            <select 
                                v-model="formData.playlist_id"
                                :disabled="isSubmitting"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                                required
                            >
                                <option value="" disabled>Select playlist...</option>
                                <option 
                                    v-for="playlist in playlists" 
                                    :key="playlist.id" 
                                    :value="playlist.id"
                                >
                                    {{ playlist.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Thumbnail -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Video thumbnail
                            </label>
                            <div class="relative mb-4">
                                <img 
                                    :src="thumbnailPreview || content?.thumb" 
                                    class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                                    alt="Thumbnail preview"
                                >
                            </div>
                            <input 
                                ref="thumbnailInput"
                                type="file"
                                accept="image/*"
                                @change="handleThumbnailChange"
                                :disabled="isSubmitting"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                            <p class="text-sm text-text_light mt-2">Leave empty to keep current thumbnail</p>
                        </div>

                        <!-- Video -->
                        <div class="form-group">
                            <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                                Select video
                            </label>
                            <div class="relative mb-4">
                                <video 
                                    :src="videoPreview || content?.video"
                                    controls
                                    class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                                ></video>
                            </div>
                            <input 
                                ref="videoInput"
                                type="file"
                                accept="video/*"
                                @change="handleVideoChange"
                                :disabled="isSubmitting"
                                class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                            <p class="text-sm text-text_light mt-2">Leave empty to keep current video</p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            :disabled="isSubmitting || !isFormValid"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.8rem] block w-full transition hover:bg-transparent hover:text-button disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:py-[.5rem] [@media(max-width:550px)]:text-[.8rem]"
                        >
                            <span v-if="isSubmitting" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                            <span v-else>Update Content</span>
                        </button>
                    </form>
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
import Swal from 'sweetalert2'
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'
import store from '../store/store'

const router = useRouter()
const route = useRoute()
const { width } = useWindowSize()

// Refs
const thumbnailInput = ref(null)
const videoInput = ref(null)

// State
const content = ref(null)
const playlists = ref([])
const error = ref(null)
const successMessage = ref(null)
const isLoading = ref(true)
const isSubmitting = ref(false)
const thumbnailPreview = ref(null)
const videoPreview = ref(null)

const formData = ref({
    status: '',
    title: '',
    description: '',
    playlist_id: '',
    thumb: null,
    video: null
})

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
])

const isFormValid = computed(() => {
    return formData.value.status &&
           formData.value.title &&
           formData.value.description &&
           formData.value.playlist_id
})

// Methods
const fetchContent = async () => {
    try {
        isLoading.value = true
        error.value = null
        
        const response = await axios.get(`/api/contents/find/${route.params.id}`)
        content.value = response.data.content
        
        if (content.value) {
            content.value.thumb = new URL(content.value.thumb, import.meta.url)
            content.value.video = new URL(content.value.video, import.meta.url)
            
            // Initialize form data
            formData.value = {
                status: content.value.status,
                title: content.value.title,
                description: content.value.description,
                playlist_id: content.value.playlist_id,
                thumb: null,
                video: null
            }
        }
    } catch (err) {
        console.error('Error fetching content:', err)
        error.value = 'Failed to load content. Please try again.'
    } finally {
        isLoading.value = false
    }
}

const fetchPlaylists = async () => {
    try {
        const user = await getUser()
        if (!user) return

        const response = await axios.get(`/api/playlists/${user.id}`)
        playlists.value = response.data
        playlists.value.forEach(playlist => {
            playlist.thumb = new URL(playlist.thumb, import.meta.url)
        })
    } catch (err) {
        console.error('Error fetching playlists:', err)
        error.value = 'Failed to load playlists. Please try again.'
    }
}

const getUser = async () => {
    try {
        const response = await axios.get('/api/user', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        return response.data
    } catch (err) {
        console.error('Error fetching user:', err)
        error.value = 'Failed to load user data'
        if (!localStorage.getItem('token')) {
            router.push('/')
        }
        return null
    }
}

const handleThumbnailChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.thumb = file
        const reader = new FileReader()
        reader.onload = e => {
            thumbnailPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleVideoChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.video = file
        const reader = new FileReader()
        reader.onload = e => {
            videoPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleSubmit = async () => {
    try {
        isSubmitting.value = true
        error.value = null
        successMessage.value = null

        const user = await getUser()
        if (!user) return

        const data = new FormData()
        data.append('teacher_id', user.id)
        data.append('playlist_id', formData.value.playlist_id)
        data.append('title', formData.value.title)
        data.append('description', formData.value.description)
        data.append('status', formData.value.status)

        if (formData.value.thumb) {
            data.append('thumb', formData.value.thumb)
        }
        if (formData.value.video) {
            data.append('video', formData.value.video)
        }

        const response = await axios.post(`/api/contents/update/${route.params.id}/send`, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        successMessage.value = 'Content updated successfully!'
        
        await Swal.fire({
            title: 'Success!',
            text: 'Content has been updated successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        })

        router.push('/admin_contents')
    } catch (err) {
        console.error('Update error:', err)
        error.value = err.response?.data?.message || 'Failed to update content. Please try again.'
        
        await Swal.fire({
            title: 'Error',
            text: error.value,
            icon: 'error'
        })
    } finally {
        isSubmitting.value = false
    }
}

// Lifecycle
onMounted(async () => {
    if (!localStorage.getItem('token')) {
        router.push('/')
        return
    }
    await Promise.all([fetchContent(), fetchPlaylists()])
})
</script>

<style scoped>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group:last-of-type {
    margin-bottom: 2rem;
}

input[type="file"] {
    padding: 0.5rem;
}

input[type="file"]::-webkit-file-upload-button {
    background-color: var(--button);
    color: var(--base);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

input[type="file"]::-webkit-file-upload-button:hover {
    background-color: transparent;
    color: var(--button);
    border: 2px solid var(--button);
}
</style>