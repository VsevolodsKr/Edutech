<template>
    <Preloader />
    <Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Liked Content</h1>
        <hr class="border-[#ccc] mb-[2rem]">
        <div class="flex gap-[1rem] justify-start items-start pr-[1rem] [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
            <div v-for="(content, index) in contents" :key="index" class="bg-base rounded-lg p-[2rem] w-[33rem]">
                <div class="flex items-center gap-[1.5rem] mb-[2rem]">
                    <img :src="teachers[index].image" class="h-[4rem] w-[4rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <div>
                        <h3 class="text-[1.3rem] text-text_dark mb-[.2rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:mb-0">{{ teachers[index].name }}</h3>
                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ content.date }}</span>
                    </div>
                </div>
                <div class="relative">
                    <img :src="content.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
                </div>
                <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ content.title }}</h3>
                <div class="flex justify-center gap-[1rem]">
                    <button @click="this.$router.push('/watch_video/' + content.id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Watch Video</button>
                    <button @click="deleteLike(content.id)" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Remove</button>
                </div>
            </div>
        </div>
    </section>
    <Sidebar />
</template>
<script>
import Header from '../components/Header.vue'
import Sidebar from '../components/Sidebar.vue'
import store from '../store/store';
import { useWindowSize } from '@vueuse/core'
import Swal from 'sweetalert2';
import Preloader from '../components/Preloader.vue'

const {width} = useWindowSize()
export default {
    components: {
        Header,
        Sidebar,
        Preloader,
    },
    data: () => {
        return{
            width,
            user: null,
            contents: [],
            teachers: [],
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        },
        showUser: function (){
            return store.getters.getUser
        }
    },
    mounted(){
        this.getUser()
        this.getContents()
    },
    methods: {
        async getUser(){
            await axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
                this.user = response.data
                this.user.image = new URL(this.user.image, import.meta.url)
            })
            if(localStorage.getItem('token') == ''){
                this.$router.push('/').then(() =>{this.$router.go(0)})
            }
            return this.user
        },

        async getContents() {
            await this.getUser().then((value) => {
                axios.get('/api/likes/user/' + value.id).then((response) => {
                    this.teachers = response.data.teachers
                    this.contents = response.data.contents
                    this.teachers.forEach((teacher) => {
                        teacher.image = new URL(teacher.image, import.meta.url)
                    })
                    this.contents.forEach((content) => {
                        content.thumb = new URL(content.thumb, import.meta.url)
                    })
                })
            })
        },

        deleteLike(id) {
            const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
            const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
            const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                color: text_dark,
                background: background,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: button4,
                confirmButtonText: "Yes, remove it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/likes/delete/' + id)
                    Swal.fire({
                    title: "Removed!",
                    text: "Liked video has been removed.",
                    icon: "success",
                }).then((result => {
                    if(result.isConfirmed){
                        this.$router.push('/likes').then(() =>{this.$router.go(0)})
                    }
                }));
            }
            });
        }
    }
}
</script>