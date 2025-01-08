<template>
    <Preloader />
    <Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Edit Comment</h1>
        <hr class="border-[#ccc] mb-[2rem]">
        <div class="bg-base rounded-lg px-[1rem] py-[.5rem] mb-[2rem]">
            <ul class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]" v-if="errorList.length > 0">
                <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i class="fa fa-warning"></i> {{ error }}</li>
            </ul>
            <textarea id="comment" cols="30" rows="10" :value="comment.comment" required maxlength="1000" class="h-[20rem] resize-none bg-background rounded-lg border-line p-[1rem] text-[1rem] text-text_light w-full my-[.5rem] outline-none hover:outline-none [@media(max-width:550px)]:text-[.7rem]"></textarea>
            <div v-if="comment.user_id == this.user.id" class="flex justify-between">
                <button type="submit" @click="this.$router.push('/watch_video/' + this.content.id)" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Cancel Edit</button>
                <button type="submit" @click="editComment(this.$route.params.id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Edit Now</button>
            </div>
        </div>
        <div class="bg-base rounded-lg px-[1rem] py-[.5rem]">
            <div class="relative mb-[1rem]">
                <video :src="content.video" controls :poster="content.thumb" class="rounded-lg w-full object-contain bg-[#000]"></video>
            </div>
        </div>
    </section>
    <Sidebar />
</template>
<script>
import Header from '../components/Header.vue'
import Sidebar from '../components/Sidebar.vue'
import store from '../store/store'
import { useWindowSize } from '@vueuse/core'
import Swal from 'sweetalert2'
import Preloader from '../components/Preloader.vue'

const {width} = useWindowSize()
export default {
    components: {
        Header,
        Sidebar, 
        Preloader
    },

    data: () => {
        return{
            width,
            comment: '',
            content: '',
            errorList: '',
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
        this.getComment()
    },
    
    created(){
        this.getUser()
        this.getComment()
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

        async getComment(){
            axios.get('/api/comments/find/' + this.$route.params.id).then((response) => {
                this.comment = response.data.comment
                this.content = response.data.content
                this.content.thumb = new URL(this.content.thumb, import.meta.url)
                this.content.video = new URL(this.content.video, import.meta.url)
            }).catch((err) => {
                console.error(err);
            });
        },

        async editComment(id, e){
            if(e && e.preventDefeault){
                e.preventDefeault()
            }
            let data = new FormData()
            data.append('comment_id', id)
            data.append('comment', document.querySelector('#comment').value)
            await axios.post('/api/comments/' + id + '/edit', data).then(() => {
                Swal.fire({
                    title: "Done!",
                    text: "Comment has been corrected!",
                    icon: "success",
                }).then((result => {
                    if(result.isConfirmed){
                        this.$router.push('/watch_video/' + this.content.id).then(() =>{this.$router.go(0)})
                    }
                }));
            }).catch((err) => {
                this.errorList = err.response.data.message
            })
        }
    }
}
</script>