<template>
    <div v-if="showSidebar == true" class="fixed top-0 left-0 w-[20rem] bg-base h-[100vh] border-r-2 border-[#ccc] z-120">
        <div class="text-right p-[2rem] hidden [@media(max-width:1180px)]:block">
            <div class="flex justify-end">
                <button class="flex items-center justify-center text-[#fff] text-[1.5rem] bg-button4 rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200"><i class="fas fa-times"></i></button>
            </div>
        </div>  
        <div class="py-[3rem] px-[2rem] text-center">
            <div class="flex justify-center">
            <img src="../images/pic-9.png" class="h-[8rem] w-[8rem] rounded-[50%] object-contain mb-[1rem]"></div>
            <h3 class="text-[1.5rem] text-text_dark overflow-hidden text-ellipsis whitespace-nowrap">username</h3>
            <p class="text-[1.3rem] text-text_light">role</p>
            <router-link to="/profile" class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:text-button hover:bg-base hover:transition hover:ease-linear hover:duration-200">View Profile</router-link>
        </div>
        <nav>
            <router-link to="/" class="block pt-[.5rem] pb-[2rem] px-[2rem] text-[1.3rem] cursor-default"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa fa-home mr-[1.5rem] transition ease-linear duration-200"></i>Home</span></router-link>
            <router-link to="/about" class="block p-[2rem] text-[1.3rem] cursor-default"> <span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa fa-question block mr-[1.5rem] transition ease-linear duration-200"></i>About</span></router-link>
            <router-link to="/courses" class="block p-[2rem] text-[1.3rem] cursor-default"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fa fa-graduation-cap block mr-[1.5rem] transition ease-linear duration-200"></i>Courses</span></router-link>
            <router-link to="/teachers" class="block p-[2rem] text-[1.3rem] cursor-default"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fas fa-chalkboard-user block mr-[1.5rem] transition ease-linear duration-200"></i>Teachers</span></router-link>
            <router-link to="/contact" class="block p-[2rem] text-[1.3rem] cursor-default"><span class="text-text_light hover:text-button hover:border-b hover:border-button hover: pb-[.5rem] hover:cursor-pointer"><i class="fas fa-handshake block mr-[1.5rem] transition ease-linear duration-200"></i>Contact Us</span></router-link>
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
            width
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        },
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