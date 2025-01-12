<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
<h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Profile Details</h1>
<hr class="border-[#ccc] mb-[2rem]">
<div class="bg-base rounded-lg p-[1.5rem]">
    <div class="flex flex-col items-center justify-center">
        <img :src="user.image" class="h-[5rem] w-[5rem] rounded-[50%] object-cover mb-[1rem] [@media(max-width:550px)]:w-[3rem] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:mb-0">
        <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.2rem]">{{ user.name }}</h3>
        <p class="text-[1.3rem] text-text_light px-[.3rem] mb-[.5rem] [@media(max-width:550px)]:text-[1rem]">student</p>
        <router-link to="/update" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[15rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">Update Profile</router-link>
    </div>
    <div class="flex flex-wrap gap-[1rem] mt-[2rem]">
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <i class="fas fa-bookmark text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">{{ bookmarksAmount }}</span>
                    <p class="text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Saved Playlist</p>
                </div>
            </div>
            <router-link to="/bookmarks" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">View Bookmarks</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <i class="fas fa-heart text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">{{ likesAmount }}</span>
                    <p class="text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Videos Liked</p>
                </div>
            </div>
            <router-link to="/likes" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">View Liked</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                    <i class="fas fa-comment text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button [@media(max-width:550px)]:text-[1.2rem]">2</span>
                    <p class="text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Videos Comments</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[6rem]">View Comments</router-link>
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
export default{
    components: {
        Header,
        Sidebar,
        Preloader,
    },
    data: () => {
        return{
            width,
            user: null,
            likesAmount: null,
            bookmarksAmount: null,  
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.getUser()
        this.countLikes()
        this.countBookmarks()
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },

    created(){
        this.getUser()
    },

    methods: {
        async getUser(){
            axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.user = response.data
            this.user.image = new URL(this.user.image, import.meta.url)
        })
        },

        async countLikes() {
            axios.get('/api/likes/count_user/' + this.user.id).then((response) => {
                this.likesAmount = response.data
            }).catch((err) => {
                console.log(err)
            })
        },

        async countBookmarks() {
            axios.get('/api/bookmarks/count_user/' + this.user.id).then((response) => {
                this.bookmarksAmount = response.data
            }).catch((err) => {
                console.log(err)
            })
        }
    }
}
</script>