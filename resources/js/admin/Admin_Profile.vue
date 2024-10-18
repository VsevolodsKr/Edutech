<template>
<Admin_Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
<h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Profile Details</h1>
<hr class="border-[#ccc] mb-[2rem]">
<div class="bg-base rounded-lg p-[1.5rem]">
    <div class="flex flex-col items-center justify-center">
        <img :src="teacher.image" class="h-[5rem] w-[5rem] rounded-[50%] object-cover mb-[1rem] [@media(max-width:550px)]:w-[3rem] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:mb-0">
        <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">{{ teacher.name }}</h3>
        <p class="text-[1.3rem] text-text_light px-[.3rem] mb-[.5rem] [@media(max-width:550px)]:text-[1rem]">{{ teacher.profession }}</p>
        <router-link to="/admin_update" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[15rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">Update Profile</router-link>
    </div>
    <div class="flex flex-wrap gap-[1rem] mt-[2rem] [@media(max-width:550px)]:flex-col">
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_8rem] [@media(max-width:550px)]:w-full">
            <div class="flex items-center justify-center flex-col gap-[.5rem] mb-[1rem]">
                <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">5</span>
                <p class="text-text_light text-center text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Total Playlists</p>
                <router-link to="/admin_playlists" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">View Playlists</router-link>
            </div>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_8rem] [@media(max-width:550px)]:w-full">
            <div class="flex items-center justify-center flex-col gap-[.5rem] mb-[1rem]">
                <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">8</span>
                <p class="text-text_light text-center text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Total Videos</p>
                <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">View Contents</router-link>
            </div>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_8rem] [@media(max-width:550px)]:w-full">
            <div class="flex items-center justify-center flex-col gap-[.5rem] mb-[1rem]">
                <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">3</span>
                <p class="text-text_light text-center text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Total Likes</p>
                <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">View Contents</router-link>
            </div>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_8rem] [@media(max-width:550px)]:w-full">
            <div class="flex items-center justify-center flex-col gap-[.5rem] mb-[1rem]">
                <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">4</span>
                <p class="text-text_light text-center text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Total Comments</p>
                <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">View Comments</router-link>
            </div>
        </div>
    </div>
</div>
</section>
<Admin_Sidebar />
</template>
<script>
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';
import { useWindowSize } from '@vueuse/core';

const {width} = useWindowSize()
export default {
    data(){
        return{
            width,
            teacher: null,
        }
    },
    components: {
        Admin_Header,
        Admin_Sidebar
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
    }
}
</script>