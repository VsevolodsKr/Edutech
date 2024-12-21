<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Profile Details</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <div class="flex flex-col items-center justify-center bg-base rounded-lg py-[1rem]">
        <div class="text-center">
            <div class="flex justify-center">
                <img :src="teacher.image" class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:w-[3rem] [@media(max-width:550px)]:h-[3rem]">
            </div>
            <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">{{ teacher.name }}</h3>
            <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ teacher.profession }}</span>
        </div>
        <div class="flex flex-row flex-wrap gap-[6rem] mt-[2rem] w-full px-[2rem] [@media(max-width:1750px)]:gap-[1rem]">
            <p class="flex-[1_1_20rem] rounded-lg bg-background py-[1rem] px-[.8rem] text-[1rem] text-text_light text-center [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">Total Playlists: <span class="text-button">{{ countPlaylist }}</span></p>
            <p class="flex-[1_1_20rem] rounded-lg bg-background py-[1rem] px-[.8rem] text-[1rem] text-text_light text-center [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">Total Videos: <span class="text-button">{{ countTotalContents }}</span></p>
            <p class="flex-[1_1_20rem] rounded-lg bg-background py-[1rem] px-[.8rem] text-[1rem] text-text_light text-center [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">Total Likes: <span class="text-button">540</span></p>
            <p class="flex-[1_1_20rem] rounded-lg bg-background py-[1rem] px-[.8rem] text-[1rem] text-text_light text-center [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">Total Comments: <span class="text-button">12</span></p>
        </div>
    </div>
    <div class="pt-[2rem] bg-background">
    <h1 class="text-[1.5rem] text-text_dark capitalize">{{ teacher.name }}'s Courses</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
        <div v-for="(playlist, index) in playlists" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
            <div class="relative">
                <img :src="playlist.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.5rem]">{{ countPlaylistContents[index] }} videos</span>
            </div>
            <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ playlist.title }}</h3>
            <button @click="this.$router.push('/playlist/' + playlist.id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Playlist</button>
        </div>
    </div>
</div>
</section>
<Sidebar />
</template>
<script>
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';
import { useWindowSize } from '@vueuse/core'
import Preloader from '../components/Preloader.vue'

const {width} = useWindowSize()
export default {
    data(){
        return{
            width,
            teacher: null,
            countPlaylist: 0,
            countTotalContents: 0,
            countPlaylistContents: [],
            playlists: [],
        }
    },
    components: {
        Header,
        Sidebar,
        Preloader
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        axios.get('/api/playlists/'+ this.$route.params.id +'/teacher').then((response) => {
            this.teacher = response.data
            this.teacher.image = new URL(this.teacher.image, import.meta.url)
            axios.get('/api/playlists/' + this.teacher.id).then((response) => {
                this.playlists = response.data;
                this.playlists.forEach((playlist) => {
                    playlist.thumb = new URL(playlist.thumb, import.meta.url)
                    axios.get('/api/contents/playlist/'+ playlist.id +'/amount').then((response) => {
                        this.countPlaylistContents.push(response.data)
                    })
                })
            })
        })
        axios.get('/api/playlists/amount/' + this.$route.params.id).then((response) => {
            this.countPlaylist = response.data
        })
        axios.get('/api/contents/amount/' + this.$route.params.id).then((response) => {
            this.countContents = response.data
        })
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },
    created(){
        axios.get('/api/playlists/'+ this.$route.params.id +'/teacher').then((response) => {
            this.teacher = response.data
            this.teacher.image = new URL(this.teacher.image, import.meta.url)
        })
        axios.get('/api/playlists/amount/' + this.$route.params.id).then((response) => {
            this.countPlaylist = response.data
            console.log(this.countPlaylist)
        })
        axios.get('/api/contents/amount/' + this.$route.params.id).then((response) => {
            this.countContents = response.data
        })
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    }
}
</script>