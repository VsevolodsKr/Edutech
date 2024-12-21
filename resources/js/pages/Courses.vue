<template>
<Preloader />
<Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize">Our Courses</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
        <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
            <div v-for="(playlist, index) in playlists" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
                <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                    <img :src="teachers[index].image" class="h-[4rem] w-[4rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <div>
                        <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">{{ teachers[index].name }}</h3>
                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ playlist.date }}</span>
                    </div>
                </div>
                <div class="relative">
                    <img :src="playlist.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                    <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:left-[.5rem] [@media(max-width:550px)]:top-[.5rem]">{{ contentsCount[index] }} videos</span>
                </div>
                <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ playlist.title }}</h3>
                <button @click="this.$router.push('/playlist/' + playlist.id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Playlist</button>
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
export default{
    components: {
        Header,
        Sidebar,
        Preloader,
    },
    data: () => {
        return{
            width,
            playlists: [], 
            teachers: [],
            contentsCount: [], 
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.getPlaylists()
    },
    methods: {
        async getPlaylists(){
            axios.get('/api/playlists/all').then((response) => {
            this.playlists = response.data
            this.playlists.forEach((playlist) => {
                playlist.thumb = new URL(playlist.thumb, import.meta.url)
                axios.get('/api/playlists/'+ playlist.teacher_id +'/teacher').then((response) => {
                    this.teachers.push(response.data)
                    this.teachers.forEach(teacher => {
                        teacher.image = new URL(teacher.image, import.meta.url)
                    });
                })
                axios.get('/api/contents/playlist/'+ playlist.id +'/amount').then((response) => {
                    this.contentsCount.push(response.data)
                })
            })
        })
        },
    }
}
</script>