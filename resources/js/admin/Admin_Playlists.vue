<template>
<Admin_Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize">Playlists</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
        <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
            <div class="bg-base rounded-lg p-[2rem] w-full">
                <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Create new playlist</h3>
                <router-link to="/admin_add_playlist" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add Playlist</router-link>
            </div>
            <div v-for="(playlist, index) in playlists" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
                <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                    <div><i :class="[playlist.status == 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot  text-[1.2rem]']"></i> <span :class="[playlist.status == 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">{{ playlist.status }}</span></div>
                    <div><i class="fas fa-calendar text-button text-[1.2rem]"></i> <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ playlist.date }}</span></div>
                </div>
                <div class="relative">
                    <img :src="playlist.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                    <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[1rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1.2rem] [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.7rem]">5</span>
                </div>
                <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ playlist.title }}</h3>
                <p class="text-text_light mb-[1rem]">{{ playlist.description }}</p>
                <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                    <router-link to="/playlist" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Update</router-link>
                    <router-link to="/playlist" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Delete</router-link>
                </div>
                <router-link to="/playlist" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Playlist</router-link>
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
            playlists: [],
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
        async getPlaylists() {
            this.getUser().then(value => {
                axios.get('/api/playlists/' + value.id).then((response) => {
                    this.playlists = response.data;
                    this.playlists.forEach((playlist) => {
                        playlist.thumb = new URL(playlist.thumb, import.meta.url)
                    })
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
        }
    }
}
</script>