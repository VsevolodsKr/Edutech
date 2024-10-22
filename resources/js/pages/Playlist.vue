<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Playlist Details</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <div class="flex items-center gap-[3rem] flex-wrap bg-base p-[1rem] rounded-lg">
        <div class="flex-[1_1_20rem]">
            <form method="post" class="mb-[1rem]">
                <button type="submit" class="rounded-lg bg-background px-[1rem] py-[.5rem] text-text_light cursor-pointer transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-base hover:bg-text_light [@media(max-width:550px)]:px-[.5rem] [@media(max-width:550px)]:py-[.2rem]"><i class="far fa-bookmark text-[1rem] mr-[.8rem] [@media(max-width:550px)]:text-[.7rem]"></i> <span class="text-1rem [@media(max-width:550px)]:text-[.7rem]">Save Playlist</span></button>
            </form>
            <div class="relative">
                <img src="../images/thumb-2.png" class="h-[30rem] w-full object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
                <span class="absolute top-[1rem] left-[1rem] rounded-lg py-[.5rem] px-[1.5rem] bg-[rgba(0,0,0,.3)] text-[#fff] text-[1rem] [@media(max-width:550px)]:text-[.7rem]">10 videos</span>
            </div>
        </div>
        <div class="flex-[1_1_40rem]">
            <div class="flex items-center gap-[1rem] mb-[1rem]">
                <img src="../images/pic-1.png" class="h-[5rem] w-[5rem] rounded-[50%] object-cover [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div>
                    <h3 class="text-[1.3rem] text-text_dark [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">02-09-2024</span>
                </div>
            </div>
            <div>
                <h3 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Complete CSS Tutorial</h3>
                <p class="py-[1rem] leading-2 text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, alias. Assumenda vero ad mollitia iusto, nobis quidem laudantium amet consequuntur placeat distinctio explicabo sit, odio fugiat. Asperiores vel at quam.</p>
                <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
            </div>
        </div>
    </div>
</section>
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background pr-[2rem] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Playlist Videos</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pb-[2rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-1.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 01)</h3>
        </router-link>
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-2.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 02)</h3>
        </router-link>
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-3.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 03)</h3>
        </router-link>
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-4.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 04)</h3>
        </router-link>
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-5.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 05)</h3>
        </router-link>
        <router-link to="/watch_video" class="bg-base rounded-lg p-[1rem] relative w-full">
            <div class="absolute top-[1rem] left-[1rem] right-[1rem] h-[20rem] rounded-lg bg-[rgba(0,0,0,.3)] flex items-center justify-center text-[3rem] text-[#fff] hover:flex [@media(max-width:550px)]:h-[13rem]">
                <i class="fas fa-play"></i>
            </div>
            <img src="../images/post-2-6.png" class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[13rem]">
            <h3 class="mt-[2rem] text-[1.5rem] text-text_dark hover:text-button1 [@media(max-width:550px)]:text-[1.2rem]">Complete CSS tutorial (part 06)</h3>
        </router-link>
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
          width  
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    }
}
</script>