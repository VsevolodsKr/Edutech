<template>
    <div v-if="showSidebar == true" class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120 [@media(max-width:550px)]:w-[15rem]">
        <div class="text-right p-[2rem] hidden [@media(max-width:1180px)]:block">
            <div class="flex justify-end">
                <button @click="updateSidebar" class="flex items-center justify-center text-[#fff] text-[1.5rem] bg-button4 rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] hover:text-base transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200"><i class="fas fa-times"></i></button>
            </div>
        </div>  
        <div v-if="teacher != null" class="py-[3rem] px-[2rem] text-center">
            <div class="flex justify-center">
                <img :src="teacher.image" class="h-[9rem] w-[9rem] rounded-full object-contain mb-[1rem] [@media(max-width:550px)]:h-[4rem] [@media(max-width:550px)]:w-[4rem]">
            </div>
            <h3 class="text-[1.5rem] text-text_dark overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">{{ teacher.name }}</h3>
            <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">{{ teacher.profession }}</p>
            <router-link to="/admin_profile" class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:text-button hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Profile</router-link>
        </div>
        <div v-else>
            <h3 class="text-[1.3rem] text-text_dark text-center mt-[2rem] overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">Please login or register</h3>
            <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem] mb-[5rem]">
                <router-link to="/login" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Login</router-link>
                <router-link to="/register" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Register</router-link>
            </div>
        </div>
        <nav>
            <router-link to="/dashboard" class="block pt-[.5rem] pb-[2rem] px-[2rem] text-[1.3rem] cursor-default [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa fa-home mr-[1.5rem] transition ease-linear duration-200"></i>Home</span></router-link>
            <router-link to="/admin_playlists" class="block p-[2rem] text-[1.3rem] cursor-default [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"> <span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa-solid fa-bars-staggered block mr-[1.5rem] transition ease-linear duration-200"></i>Playlists</span></router-link>
            <router-link to="/courses" class="block p-[2rem] text-[1.3rem] cursor-default [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa fa-graduation-cap block mr-[1.5rem] transition ease-linear duration-200"></i>Contents</span></router-link>
            <router-link to="/teachers" class="block p-[2rem] text-[1.3rem] cursor-default [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fas fa-comment block mr-[1.5rem] transition ease-linear duration-200"></i>Comments</span></router-link>
            <button v-if="teacher != null" @click="logout" class="block p-[2rem] text-[1.3rem] cursor-default [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:py-[1rem] [@media(max-width:550px)]:pl-[2rem]"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa-solid fa-right-from-bracket block mr-[1.5rem] transition ease-linear duration-200"></i>Logout</span></button>

        </nav>
    </div>
</template>
<script>
import { useWindowSize } from '@vueuse/core'
import store from '../store/store'

const {width} = useWindowSize()

export default {
    data: () => {
        return{
            width,
            teacher: null,
        }
    },
    methods: {
        updateSidebar: function (){
            store.commit('setShowSidebar', false)
        },
        logout: function (){
            localStorage.setItem('token', '')
            window.location.reload()
        },
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        },
    },
    mounted(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.teacher = response.data
            this.teacher.image = new URL(this.teacher.image, import.meta.url)
        })
    },
    watch: {
        width(value){
            if(value < 1180){
                store.commit('setShowSidebar', false)
            }else{
                store.commit('setShowSidebar', true)
            }
        }
    }
}
</script>