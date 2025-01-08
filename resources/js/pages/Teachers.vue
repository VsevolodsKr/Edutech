<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1.5rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Expert Teachers</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <div class="w-full rounded-lg bg-[#eee] py-[.5rem] px-[1.5rem] flex gap-[2rem] bg-base">
        <input v-model="searchTeacher" class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none [@media(max-width:550px)]:text-[1rem]"type="text" name="search_box" required placeholder="search teachers..." maxlength="100">
        <button type="submit" @click="searchTeachers" class="bg-transparent text-[1rem] cursor-pointer text-text_light fa fa-search hover:text-button"></button>
    </div>
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start mt-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
        <div class="bg-base rounded-lg p-[2rem] text-center" v-if="show">
            <h3 class="text-[2rem] text-text_dark capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Become A Teacher</h3>
            <p class="leading-1.7 py-[.5rem] text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Step into a network of passionate educators, innovators, and mentors. Share your insights and make a meaningful difference by helping students achieve their goals</p>
            <div class="flex justify-center">
                <router-link to="/register" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[.7rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Get Started</router-link>
            </div>
        </div>
        <div v-for="(teacher, index) in teachers" :key="index" class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img :src="teacher.image" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">{{ teacher.name }}</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ teacher.profession }}</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Total playlists: <span class="text-button">{{ countPlaylists[index] }}</span></p>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Total contents: <span class="text-button">{{ countContents[index] }}</span></p>
            <button @click="this.$router.push('/teacher_profile/' + teacher.id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[7rem]">View Profile</button>
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
            teachers: [],
            countPlaylists: [],
            countContents: [],
            searchTeacher: '',
            show: true,  
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.getTeachers()
    },
    methods: {
        async getTeachers(){
            this.show = true
            axios.get('/api/teachers/all').then((response) => {
                this.teachers = response.data
                this.teachers.forEach((teacher) => {
                    teacher.image = new URL(teacher.image, import.meta.url)
                    axios.get('/api/playlists/amount/' + teacher.id).then((response) => {
                        this.countPlaylists.push(response.data)
                    })
                    axios.get('/api/contents/amount/' + teacher.id).then((response) => {
                        this.countContents.push(response.data)
                    })
                })
            })
        },

        searchTeachers(){
            if(this.searchTeacher){
                this.show = false
                let data = new FormData()
                data.append('name', this.searchTeacher)
                axios.post('/api/teachers/search', data).then((response) => {
                this.teachers = response.data
                this.teachers.forEach((teacher) => {
                    teacher.image = new URL(teacher.image, import.meta.url)
                    axios.get('/api/playlists/amount/' + teacher.id).then((response1) => {
                        this.countPlaylists.push(response1.data)
                    })
                    axios.get('/api/contents/amount/' + teacher.id).then((response1) => {
                        this.countContents.push(response1.data)
                    })
                })
            })
            } else {
                this.getTeachers()
            }
        }
    }
}
</script>