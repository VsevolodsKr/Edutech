<template>
<Preloader />
<Admin_Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize">Create Playlist</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
        <div class="flex items-center justify-center">
            <form method="post" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
                <ul :class="[errorStatus == 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]' , 'rounded-xl mb-[1rem]']" v-if="errorList.length > 0">
                    <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i :class="[errorStatus == 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}</li>
                </ul>
                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">Playlist status <span class="text-button4">*</span></p>
                <select v-model="status" name="status" class="text-[1rem] text-text_light rounded-lg p-[.5rem] my-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full" required>
                    <option value="" selected>Select status...</option>
                    <option value="active">active</option>
                    <option value="deactive">deactive</option>
                </select>
                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">Playlist title <span class="text-button4">*</span></p>
                <input v-model="title" type="name" name="text" placeholder="Enter playlist title..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] my-[1rem] h-[3rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">Playlist description <span class="text-button4">*</span></p>
                <textarea v-model="description" placeholder="Enter playlist description..." required maxlength="1000" cols="30" rows="10" class="h-[20rem] resize-none w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0"></textarea>
                <p class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">Playlist thumbnail <span class="text-button4">*</span></p>
                <input id="img" type="file" accept="image/*" required class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none my-[1rem] h-[3rem] [@media(max-width:550px)]:text-[.7rem]">
                <button type="submit" @click="handleSubmit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Create Playlist</button>
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
            errorList: '',
            errorStatus: '',
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
        }
    },
    mounted(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.teacher = response.data
            this.teacher.image = new URL(this.teacher.image, import.meta.url)
        })
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
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
            data.append('status', this.status)
            data.append('title', this.title)
            data.append('description', this.description)
            if(document.querySelector('#img').files[0]){
                data.append('image', document.querySelector('#img').files[0])
            }
            try{
                const response = await axios.post('api/add_playlist/send', data, config)
                console.log(response.data.message)
                this.errorList = response.data.message
                this.errorStatus = response.data.status
                setTimeout(() => {
                    this.$router.push('/admin_playlists')
                }, 500)            
            }catch(err){
                console.log(err.response.data)
                this.errorList = err.response.data.message
                this.errorStatus = err.response.data.status
            }
        }
    }
}
</script>