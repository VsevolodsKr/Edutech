<template>
    <Preloader />
    <Admin_Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Upload Content</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            <div class="flex items-center justify-center">
                <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                    <!-- Error/Success Messages -->
                    <div v-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
                        <p class="[@media(max-width:550px)]:text-[.7rem]">
                            <i class="fa fa-warning"></i> {{ error }}
                        </p>
                    </div>
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
                            Video thumbnail <span class="text-button4">*</span>
                        </label>
                        <div v-if="thumbnailPreview" class="mb-4">
                            <img 
                                :src="thumbnailPreview" 
                                alt="Thumbnail preview"
                                class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                            >
                        </div>
                        <input 
                            ref="thumbnailInput"
                            type="file"
                            accept="image/*"
                            @change="handleThumbnailChange"
                            :disabled="isSubmitting"
                            class="text-[1rem] text-text_light rounded-lg p-[.8rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            required
                        >
                    </div>

                    <!-- Video -->
                    <div class="form-group">
                        <label class="text-[1.2rem] text-text_dark block mb-2 [@media(max-width:550px)]:text-[.9rem]">
                            Select video <span class="text-button4">*</span>
                        </label>
                        <div v-if="videoPreview" class="mb-4">
                            <video 
                                :src="videoPreview"
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
                            required
                        >
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
                            Uploading...
                        </span>
                        <span v-else>Upload Content</span>
                    </button>
                </form>
            </div>
        </section>
    <Admin_Sidebar />
</template>
<script>
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';
import { useWindowSize } from '@vueuse/core';
import Preloader from '../components/Preloader.vue';
    
const {width} = useWindowSize()
export default {
    data(){
        return{
            width,
            teacher: null,
            status: '',
            title: '',
            description: '',
            playlist: '',
            errorList: '',
            errorStatus: '',
            playlists: [],
            error: null,
            successMessage: null,
            isSubmitting: false,
            thumbnailPreview: null,
            videoPreview: null,
            formData: {
                status: '',
                title: '',
                description: '',
                playlist_id: '',
                thumb: null,
                video: null
            }
        }
    },
    components: {
        Admin_Header,
        Admin_Sidebar,
        Preloader
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        },
        isFormValid() {
            return this.formData.status &&
                   this.formData.title &&
                   this.formData.description &&
                   this.formData.playlist_id &&
                   this.formData.thumb &&
                   this.formData.video
        }
    },
    mounted(){
        this.getUser()
        this.getPlaylists()
    },
    created(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.teacher = response.data
            this.teacher.image = new URL(this.teacher.image, import.meta.url)
        })
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },
    methods: {
        async handleSubmit(e){
            if(e && e.preventDefault){
                e.preventDefault()
            }
            const config = {
                headers: {
                    'description-Type': 'multipart/form-data'
                }
            }
            let data = new FormData()
            data.append('teacher_id', this.teacher.id)
            data.append('playlist_id', this.formData.playlist_id)
            data.append('title', this.formData.title)
            data.append('description', this.formData.description)
            data.append('status', this.formData.status)
            data.append('thumb', this.formData.thumb)
            data.append('video', this.formData.video)
            try{
                this.isSubmitting = true
                const response = await axios.post('api/add_content/send', data, config)
                console.log(response.data.message)
                this.successMessage = response.data.message
                setTimeout(() => {
                    this.$router.push('/admin_contents')
                }, 500)            
            }catch(err){
                console.log(err.response.data)
                this.error = err.response.data.message
                this.errorStatus = err.response.data.status
            } finally {
                this.isSubmitting = false
            }
        },
        async getPlaylists() {
            this.getUser().then(value => {
                axios.get('/api/playlists/' + value.id).then((response) => {
                    console.log(response)
                    this.playlists = response.data;
                    this.playlists.forEach((playlist) => {
                        playlist.thumb = new URL(playlist.thumb, import.meta.url)
                    })
                    console.log(this.playlists)
                }).catch((err) => {
                    console.error(err);
                });
            })
        },
        async getUser(){
            await axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
                this.teacher = response.data
                this.teacher.image = new URL(this.teacher.image, import.meta.url)
            })
            if(localStorage.getItem('token') == ''){
                this.$router.push('/').then(() =>{this.$router.go(0)})
            }
            return this.teacher
        },
        handleThumbnailChange(event) {
            const file = event.target.files[0]
            if (file) {
                this.formData.thumb = file
                const reader = new FileReader()
                reader.onload = e => {
                    this.thumbnailPreview = e.target.result
                }
                reader.readAsDataURL(file)
            }
        },
        handleVideoChange(event) {
            const file = event.target.files[0]
            if (file) {
                this.formData.video = file
                const reader = new FileReader()
                reader.onload = e => {
                    this.videoPreview = e.target.result
                }
                reader.readAsDataURL(file)
            }
        }
    }
}
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