<template>
<div>
    <Admin_Header />
    <section :class="sectionClasses">
        <h1 class="text-[1.5rem] text-text_dark capitalize">Create Playlist</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
        
        <div class="flex items-center justify-center">
            <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[1rem] w-[50rem]">
                <!-- Error/Success Messages -->
                <div v-if="messages.length > 0" :class="[
                    'rounded-xl mb-[1rem]',
                    errorStatus === 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]'
                ]">
                    <div v-for="(message, index) in messages" 
                         :key="index" 
                         class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]"
                    >
                        <i :class="[errorStatus === 500 ? 'fa fa-warning' : 'fa fa-check']"></i>
                        {{ message }}
                    </div>
                </div>

                <!-- Status Field -->
                <div class="form-group">
                    <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                        Playlist status <span class="text-button4">*</span>
                    </label>
                    <select 
                        v-model="formData.status"
                        :disabled="isSubmitting"
                        class="text-[1rem] text-text_light rounded-lg p-[.5rem] my-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full" 
                        required
                    >
                        <option value="" disabled>Select status...</option>
                        <option value="active">Active</option>
                        <option value="deactive">Deactive</option>
                    </select>
                </div>

                <!-- Title Field -->
                <div class="form-group">
                    <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                        Playlist title <span class="text-button4">*</span>
                    </label>
                    <input 
                        v-model="formData.title"
                        type="text"
                        :disabled="isSubmitting"
                        placeholder="Enter playlist title..."
                        maxlength="50"
                        class="text-[1rem] text-text_light rounded-lg p-[.5rem] my-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        required
                    >
                </div>

                <!-- Description Field -->
                <div class="form-group">
                    <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                        Playlist description <span class="text-button4">*</span>
                    </label>
                    <textarea 
                        v-model="formData.description"
                        :disabled="isSubmitting"
                        placeholder="Enter playlist description..."
                        maxlength="1000"
                        class="h-[20rem] resize-none w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0"
                        required
                    ></textarea>
                </div>

                <!-- Thumbnail Field -->
                <div class="form-group">
                    <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                        Playlist thumbnail <span class="text-button4">*</span>
                    </label>
                    
                    <!-- Thumbnail Preview -->
                    <div v-if="thumbnailPreview" class="relative mb-4">
                        <img :src="thumbnailPreview" 
                             class="w-full h-[20rem] object-cover rounded-lg" 
                             alt="Thumbnail preview"
                        >
                    </div>

                    <input 
                        type="file"
                        ref="fileInput"
                        accept="image/*"
                        :disabled="isSubmitting"
                        @change="handleFileChange"
                        class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none my-[1rem] h-[3rem] [@media(max-width:550px)]:text-[.7rem]"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    :disabled="isSubmitting || !isFormValid"
                    class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="isSubmitting" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-base" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating Playlist...
                    </span>
                    <span v-else>Create Playlist</span>
                </button>
            </form>
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
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'

// Router
const router = useRouter()

// State
const { width } = useWindowSize()
const fileInput = ref(null)
const isSubmitting = ref(false)
const errorStatus = ref(null)
const messages = ref([])
const thumbnailPreview = ref(null)
const teacher = ref(null)

const formData = ref({
    status: '',
    title: '',
    description: '',
    image: null
})

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
    'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
])

const isFormValid = computed(() => {
    return formData.value.status &&
           formData.value.title &&
           formData.value.description &&
           formData.value.image
})

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
        if (!localStorage.getItem('token')) {
            router.push('/')
        }
        return null
    }
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.image = file
        const reader = new FileReader()
        reader.onload = e => {
            thumbnailPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleSubmit = async () => {
    try {
        isSubmitting.value = true
        messages.value = []
        errorStatus.value = null

        const user = await getUser()
        if (!user) return

        const data = new FormData()
        data.append('teacher_id', user.id)
        data.append('status', formData.value.status)
        data.append('title', formData.value.title)
        data.append('description', formData.value.description)
        data.append('image', formData.value.image)

        const response = await axios.post('api/add_playlist/send', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        messages.value = Array.isArray(response.data.message) 
            ? response.data.message 
            : [response.data.message]
        errorStatus.value = response.data.status

        if (response.data.status !== 500) {
            setTimeout(() => {
                router.push('/admin_playlists')
            }, 500)
        }
    } catch (err) {
        console.error('Error creating playlist:', err)
        messages.value = Array.isArray(err.response?.data?.message)
            ? err.response.data.message
            : [err.response?.data?.message || 'An error occurred while creating the playlist']
        errorStatus.value = err.response?.data?.status || 500
    } finally {
        isSubmitting.value = false
    }
}

// Lifecycle
onMounted(async () => {
    await getUser()
})
</script>

<style scoped>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group:last-child {
    margin-bottom: 1rem;
}
</style>