<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <div class="bg-base rounded-lg p-[1rem]">
        <div class="relative mb-[1rem]">
            <video :src="content.video" controls :poster="content.thumb" class="rounded-lg w-full object-contain bg-[#000]"></video>
        </div>
        <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">{{ content.title }}</h3>
        <div class="flex mt-[.5rem] mb-[1rem] border-b border-line pb-[1rem] gap-[1.5rem] items-center">
            <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]"><i class="fas fa-calendar text-button"></i> <span class="text-text_light">{{ content.date }}</span></p>
            <p class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]"><i class="fas fa-heart text-button"></i> <span class="text-text_light">4 likes</span></p>
        </div>
        <div class="flex items-center gap-[1rem] mb-[1rem]">
            <img :src="teacher.image" class="rounded-[50%] h-[5rem] w-[5rem] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
            <div>
                <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">{{ teacher.name }}</h3>
                <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">{{ teacher.profession }}</span>
            </div>
        </div>
        <form class="flex items-center justify-between gap-[1rem]">
            <button @click="this.$router.push('/playlist/' + content.playlist_id)" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">View Playlist</button>
            <div v-if="status && user">
                <button @click="deleteLike" class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[4.4rem]"><i class="far fa-heart text-[1rem] [@media(max-width:550px)]:text-[.7rem]"></i> <span class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">Unlike</span></button>
            </div>
            <div v-if="!status && user">
                <button @click="addLike" class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[4.4rem]"><i class="far fa-heart text-[1rem] [@media(max-width:550px)]:text-[.7rem]"></i> <span class="text-[1rem] [@media(max-width:550px)]:text-[.7rem]">Like</span></button>
            </div>
        </form>
        <p class="leading-1.5 text-[1rem] text-text_light mt-[1.5rem] text-justify [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-1">{{ content.description }}</p>
    </div>
</section>
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">2 Comments</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <form method="post" class="bg-base rounded-lg p-[1rem] mb-[1rem]">
        <h3 class="text-[1.5rem] text-text_dark mb-[.5rem] [@media(max-width:550px)]:text-[1.2rem]">Add Comments</h3>
        <textarea name="comment_box" cols="30" rows="10" placeholder="Enter your comment..." required maxlength="1000" class="h-[20rem] resize-none bg-background rounded-lg border-line p-[1rem] text-[1rem] text-text_light w-full my-[.5rem] outline-none hover:outline-none [@media(max-width:550px)]:text-[.7rem]"></textarea>
        <button type="submit" name="add_comment" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[6rem]">Add Comment</button>
    </form>
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
                <button type="submit" name="edit_comment" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Edit Comment</button>
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
            user: null,
            teacher: null,
            content: null,
            status: null,
            likeID: null,
        }
    },
    components: {
        Header,
        Sidebar,
        Preloader,  
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.getContents()
        this.checkLike()
    },
    created(){
        this.getContents()
    },
    methods: {
        async getContents(){
            axios.get('/api/contents/find/' + this.$route.params.id).then((response) => {
                this.content = response.data;
                this.content.thumb = new URL(this.content.thumb, import.meta.url)
                this.content.video = new URL(this.content.video, import.meta.url)
                axios.get('/api/playlists/'+ this.content.teacher_id +'/teacher').then((response) => {
                    this.teacher = response.data
                    this.teacher.image = new URL(this.teacher.image, import.meta.url)
                })
            }).catch((err) => {
                console.error(err);
            });
        },
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
        async addLike(e){
            if(e && e.preventDefault){
                e.preventDefault()
            }
            let data = new FormData()
            axios.get('/api/contents/find/' + this.$route.params.id).then((response1) => {
                this.getUser().then(value => {
                    data.append('user_id', value.id)
                    axios.get('/api/playlists/'+ this.content.teacher_id +'/teacher').then((response) => {
                        data.append('teacher_id', response.data.id)
                        data.append('content_id', response1.data.id)
                        axios.post('/api/likes/add', data)
                        this.likeID = this.checkLike()
                    })
                })
            }).catch((err) => {
                console.error(err);
            });
        },
        async checkLike(){
            let data = new FormData()
            axios.get('/api/contents/find/' + this.$route.params.id).then((response1) => {
                this.getUser().then(value => {
                    data.append('user_id', value.id)
                    axios.get('/api/playlists/'+ this.content.teacher_id +'/teacher').then((response) => {
                        data.append('teacher_id', response.data.id)
                        data.append('content_id', response1.data.id)
                        axios.post('/api/likes/check', data).then((response2) => {
                            this.status = response2.data.status
                            this.likeID = response2.data.id
                            return response2.data.id
                        })
                    })
                })
            }).catch((err) => {
                console.error(err);
            });
        },
        async deleteLike(e){
            if(e && e.preventDefault){
                e.preventDefault()
            }
            let data = new FormData()
            axios.get('/api/contents/find/' + this.$route.params.id).then((response1) => {
                this.getUser().then(value => {
                    data.append('user_id', value.id)
                    axios.get('/api/playlists/'+ this.content.teacher_id +'/teacher').then((response) => {
                        data.append('teacher_id', response.data.id)
                        data.append('content_id', response1.data.id)
                        axios.delete('/api/likes/delete/'+ this.likeID)
                        this.checkLike()
                    })
                })
            }).catch((err) => {
                console.error(err);
            });
        }
    }
}
</script>