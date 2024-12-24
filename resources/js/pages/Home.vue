<template>
    <Preloader />
    <Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Quick options</h1>
        <hr class="border-[#ccc] mb-[2rem]">
        <div class="grid grid-cols-[repeat(auto-fit,_24rem)] gap-[1rem] justify-center items-start [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
            <div v-if="user != null" class="bg-base rounded-lg p-[1rem] w-full">
                <h3 class="text-[1.3rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">Likes and comments</h3>
                <p class="mt-[1rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Total likes: <span class="text-button">{{ likesAmount || 0 }}</span></p>
                <router-link to="/likes" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[6rem]">View Likes</router-link>
                <p class="mt-[1rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Total comments: <span class="text-button">2</span></p>
                <router-link to="/" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[8rem]">View Comments</router-link>
                <p class="mt-[1rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Total bookmarked: <span class="text-button">{{ bookmarksAmount || 0 }}</span></p>
                <router-link to="/bookmarks" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[9rem]">View Bookmarked</router-link>
            </div>
            <div v-else class="bg-base rounded-lg p-[1rem] w-full">
                <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">Please login or register</h3>
                <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem]">
                    <router-link to="/login" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Login</router-link>
                    <router-link to="/register" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Register</router-link>
                </div>
            </div>
            <div class="bg-base rounded-lg p-[1rem] w-full">
                <h3 class="text-[1.3rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">Top Categories</h3>
                <div class="flex flex-wrap gap-[1rem] mt-[1rem]">
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa fa-code mr-[1rem]"></i><span>development</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fas fa-chart-line mr-[1rem]"></i><span>marketing</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa fa-flask mr-[1rem]"></i><span>science</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fas fa-vial mr-[1rem]"></i><span>software</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa fa-music mr-[1rem]"></i><span>music</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa fa-camera mr-[1rem]"></i><span>photography</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa fa-pencil mr-[1rem]"></i><span>art</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fas fa-chart-simple mr-[1rem]"></i><span>business</span></router-link>
                </div>
            </div>
            <div class="bg-base rounded-lg p-[1rem] w-full">
                <h3 class="text-[1.3rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">Popular Topics</h3>
                <div class="flex flex-wrap gap-[1rem] mt-[1rem]">
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fab fa-react mr-[1rem]"></i><span>React</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fab fa-js mr-[1rem]"></i><span>JavaScript</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fa-solid fa-database mr-[1rem]"></i><span>SQL</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fab fa-linux mr-[1rem]"></i><span>Linux</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fab fa-css3 mr-[1rem]"></i><span>TailwindCSS</span></router-link>
                    <router-link to="/" class="bg-background rounded-lg py-[.5rem] px-[1rem] text-[1rem] text-text_light transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:text-[.8rem] hover:[@media(max-width:550px)]:text-[.8rem]"><i class="fab fa-php mr-[1rem]"></i><span>PHP</span></router-link>
                </div>
            </div>
            <div class="bg-base rounded-lg p-[1rem] w-full">
                <h3 class="text-[1.3rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1rem]">Become a Teacher</h3>
                <p class="py-[1rem] text-[1rem] text-text_light leading-2 [@media(max-width:550px)]:text-[.7rem]">Inspire Future Generations: Become an Edutech Teacher</p>
                <router-link to="/teachers" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Get Started</router-link>
            </div>
        </div>
    </section>
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[4rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-0']">
        <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Our Courses</h1>
        <hr class="border-[#ccc] mb-[2rem] mr-[1.5rem] [@media(max-width:550px)]:mr-[.5rem]">
        <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-[.5rem]">
                <div v-for="(playlist, index) in playlists.slice(0, 7)" :key="index" class="bg-base rounded-lg p-[2rem] w-full">
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
        <div class="flex justify-center text-center mt-[2rem] pb-[2rem]">
            <router-link to="/courses" class="bg-button3 text-base text-center border-2 border-button3 rounded-lg py-[.5rem] px-[2rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button3 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View All Courses</router-link>
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
    components: {
        Header,
        Sidebar,
        Preloader,
    },
    data: () => {
        return{
            width,
            user: null,
            playlists: [],
            teachers: [],
            contentsCount: [],
            likesAmount: null,
            bookmarksAmount: null,
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
        this.getPlaylists()
        this.countLikes()
        this.countBookmarks()
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

        async countLikes() {
            this.getUser().then((value) => {
                axios.get('/api/likes/count_user/' + value.id).then((response) => {
                    this.likesAmount = response.data
                }).catch((err) => {
                    console.log(err)
                })
            })
        },

        async countBookmarks() {
            this.getUser().then((value) => {
                axios.get('/api/bookmarks/count_user/' + value.id).then((response) => {
                    this.bookmarksAmount = response.data
                }).catch((err) => {
                    console.log(err)
                })
            })
        }
    }
}
</script>