<template>
    <Preloader />
    <Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Your comments</h1>
        <hr class="border-[#ccc] mb-[2rem]">
        <div class="flex flex-col gap-[1rem] justify-start items-start pr-[1rem] [@media(max-width:550px)]:pr-0">
            <div v-for="(comment, index) in comments" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
                <div class="text-[1.2rem] [@media(max-width:550px)]:text-[1rem]">
                    <span class="text-text_light">{{ comment.date }}</span> <span class="text-text_dark">- {{ contents[index].title }} -</span> <button @click="this.$router.push('/watch_video/' + contents[index].id)" class="text-button">View Content</button> 
                </div>
                <div class="rounded-lg bg-background p-[1rem] whitespace-pre-line my-[.5rem] text-[1rem] text-text_light leading-1.5 relative z-0 before:content-none before:absolute before:top-[-1rem] before:left-[1.5rem] before:bg-base before:h-[1rem] before:w-[2rem] before:[clip-path:polygon(50%_0%,_0%_100%,_100%_100%)] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.5rem]">{{ comment.comment }}</div>
                <div class="flex justify-start gap-[1rem]">
                    <button @click="this.$router.push('/edit_comment/' + comment.id)" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Edit Comment</button>
                    <button @click="deleteComment(comment.id)" class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Delete Comment</button>
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
            user: '',
            comments: [],
            contents: [],
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
        this.getComments()
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

        async getComments() {
            await this.getUser().then((value) => {
                axios.get('/api/comments/user/' + value.id).then((response) => {
                    this.comments = response.data.comments
                    this.contents = response.data.contents
                })
            })
        },

        deleteComment(id) {
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
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/comments/delete/' + id)
                    Swal.fire({
                        title: "Deleted!",
                        text: "Comment has been deleted!",
                        icon: "success",
                    }).then((result => {
                        if(result.isConfirmed){
                            this.getComments()
                        }
                    }));
                }
            });
        }
    }
}
</script>