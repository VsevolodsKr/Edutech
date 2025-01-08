<template>
    <Preloader />
    <Admin_Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <div class="bg-base rounded-lg p-[1rem]">
            <div class="relative mb-[1rem]">
                <video :src="content.video" controls :poster="content.thumb" class="rounded-lg w-full object-contain bg-[#000]"></video>
            </div>
            <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">{{ content.title }}</h3>
            <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
                <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]"><i class="fas fa-calendar text-button"></i> <span class="text-text_light">{{ content.date }}</span></p>
            </div>
            <form method="post" class="flex items-center justify-between gap-[1rem]">
                <p class="bg-background px-[1rem] py-[.5rem] text-[1rem] rounded-lg [@media(max-width:550px)]:text-[.7rem]"><i class="fas fa-thumbs-up text-button"></i> <span class="text-text_light">5</span></p>
                <p class="bg-background px-[1rem] py-[.5rem] text-[1rem] rounded-lg [@media(max-width:550px)]:text-[.7rem]"><i class="fas fa-comment text-button"></i> <span class="text-text_light">5</span></p>
            </form>
            <p class="leading-1.5 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-1">{{ content.description }}</p>
            <div class="flex justify-between w-full gap-[1rem] my-[1rem]">
                <button @click="this.$router.push('/admin_contents/update/' + content.id)" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Update</button>
                <button @click="deleteContent(content.id)" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Delete</button>
            </div>
        </div>
    </section>
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <div class="grid gap-[1rem] bg-base p-[1rem] rounded-lg [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
            <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">User Comments</h1>    
            <div class="mb-[5rem] [@media(max-width:550px)]:mb-[3rem]">
                <div class="flex items-center gap-[1rem] mb-[1rem]">
                    <img src="../images/pic-2.png" class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <div>
                        <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">02-09-2024</span>
                    </div>
                </div>
                <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-1.5 relative z-0 before:content-none before:absolute before:top-[-1rem] before:left-[1.5rem] before:bg-base before:h-[1rem] before:w-[2rem] before:[clip-path:polygon(50%_0%,_0%_100%,_100%_100%)] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">This is mine comment</div>
                <form method="post" class="flex gap-[1rem] mt-[.5rem]">
                    <button type="submit" name="delete_comment" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Delete Comment</button>
                </form>
            </div>
            <div>
                <div class="flex items-center gap-[1rem] mb-[1rem]">
                    <img src="../images/pic-3.png" class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <div>
                        <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                        <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">02-09-2024</span>
                    </div>
                </div>
                <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-1.5 relative z-0 before:content-none before:absolute before:top-[-1rem] before:left-[1.5rem] before:bg-base before:h-[1rem] before:w-[2rem] before:[clip-path:polygon(50%_0%,_0%_100%,_100%_100%)] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">Amazing tutorial</div>
                <form method="post" class="flex gap-[1rem] mt-[.5rem]">
                    <button type="submit" name="delete_comment" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Delete Comment</button>
                </form>
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
            content: null,
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
        this.getContents()
    },
    created(){
        this.getContents()
    },
    methods: {
        async getContents(){
            axios.get('/api/contents/find/' + this.$route.params.id).then((response) => {
                this.content = response.data.content;
                this.content.thumb = new URL(this.content.thumb, import.meta.url)
                this.content.video = new URL(this.content.video, import.meta.url)
            }).catch((err) => {
                console.error(err);
            });
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
        deletePlaylist(id){
            const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
            const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
            const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')
            Swal.fire({
                title: "Are you sure?",
                text: "Playlist will be deleted permanently!",
                icon: "warning",
                color: text_dark,
                background: background,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: button4,
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('/api/playlists/delete/' + id)
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