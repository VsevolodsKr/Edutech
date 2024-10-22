<template>
<Preloader />
<Admin_Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize">Your contents</h1>
    <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
        <div class="bg-base rounded-lg p-[2rem] w-full">
            <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Create new content</h3>
            <router-link to="/admin_add_content" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add Content</router-link>
        </div>
        <div v-for="(content, index) in contents" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
            <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                <div><i :class="[content.status == 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'fas fa-circle-dot  text-[1.2rem]']"></i> <span :class="[content.status == 'active' ? 'text-[#0eed46]' : 'text-[#e83731]', 'text-[1.2rem]']">{{ content.status }}</span></div>
                <div><i class="fas fa-calendar text-button text-[1.2rem]"></i> <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ content.date }}</span></div>
            </div>
            <div class="relative">
                <img :src="content.thumb" class="w-full h-[20rem] object-cover rounded-lg z-[-120] [@media(max-width:550px)]:h-[12rem]">
            </div>
            <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem]">{{ content.title }}</h3>
            <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                <button @click="this.$router.push('/admin_contents/update/' + content.id)" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Update</button>
                <button @click="deleteContent(content.id)" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Delete</button>
            </div>
            <router-link to="/playlist" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Content</router-link>
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
import Swal from 'sweetalert2';
import Preloader from '../components/Preloader.vue'

const {width} = useWindowSize()

export default {
    data(){
        return{
            width,
            teacher: null,
            contents: [],
        }
    },
    components: {
        Admin_Header,
        Admin_Sidebar,
        Preloader,  
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.getUser()
        this.getContents()
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
        async getContents() {
            this.getUser().then(value => {
                axios.get('/api/contents/' + value.id).then((response) => {
                    this.contents = response.data;
                    this.contents.forEach((content) => {
                        content.thumb = new URL(content.thumb, import.meta.url)
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
        },
        deleteContent(id){
            const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
            const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
            const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')
            Swal.fire({
                title: "Are you sure?",
                text: "Content will be deleted permanently!",
                icon: "warning",
                color: text_dark,
                background: background,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: button4,
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('/api/contents/delete/' + id)
                Swal.fire({
                title: "Deleted!",
                text: "Your file has been deleted.",
                icon: "success",
            }).then((result => {
                if(result.isConfirmed){
                    this.$router.push('/admin_contents').then(() =>{this.$router.go(0)})
                }
            }));
        }
        });
        },
    }
}
</script>