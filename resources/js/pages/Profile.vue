<template>
<Header />
<section v-if="showSidebar == true" class="pl-[22rem] pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)]">
<h1 class="text-[1.5rem] text-text_dark capitalize">Profile Details</h1>
<hr class="border-[#ccc] mb-[2rem]">
<div class="bg-base rounded-lg p-[1.5rem]">
    <div class="flex flex-col items-center justify-center">
        <img src="../images/pic-9.png" class="h-[5rem] w-[5rem] rounded-[50%] object-cover mb-[1rem]">
        <h3 class="text-[1.5rem] text-text_dark">username</h3>
        <p class="text-[1.3rem] text-text_light px-[.3rem] mb-[.5rem]">role</p>
        <router-link to="/update" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[15rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Update Profile</router-link>
    </div>
    <div class="flex flex-wrap gap-[1rem] mt-[2rem]">
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-bookmark text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">5</span>
                    <p class="text-text_light text-[1.3rem]">Saved Playlist</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Playlists</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-heart text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">27</span>
                    <p class="text-text_light text-[1.3rem]">Videos Liked</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Liked</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-comment text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">2</span>
                    <p class="text-text_light text-[1.3rem]">Videos Comments</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Comments</router-link>
        </div>
    </div>
</div>
</section>
<section v-if="showSidebar == false" class="pl-[2rem] pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)]">
<h1 class="text-[1.5rem] text-text_dark capitalize">Profile Details</h1>
<hr class="border-[#ccc] mb-[2rem]">
<div class="bg-base rounded-lg p-[1.5rem]">
    <div class="flex flex-col items-center justify-center">
        <img src="../images/pic-9.png" class="h-[5rem] w-[5rem] rounded-[50%] object-cover mb-[1rem]">
        <h3 class="text-[1.5rem] text-text_dark">username</h3>
        <p class="text-[1.3rem] text-text_light px-[.3rem] mb-[.5rem]">role</p>
        <router-link to="/update" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[15rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Update Profile</router-link>
    </div>
    <div class="flex flex-wrap gap-[1rem] mt-[2rem]">
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-bookmark text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">5</span>
                    <p class="text-text_light text-[1.3rem]">Saved Playlist</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Playlists</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-heart text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">27</span>
                    <p class="text-text_light text-[1.3rem]">Videos Liked</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Liked</router-link>
        </div>
        <div class="bg-background rounded-lg p-[1rem] flex-[1_1_25rem]">
            <div class="flex items-start gap-[1.5rem] mb-[1rem]">
                <div class="flex items-center justify-center bg-text_dark rounded-lg h-[5rem] w-[5rem] leading-[4rem]">
                    <i class="fas fa-comment text-base text-[2.7rem]"></i>
                </div>
                <div>
                    <span class="text-[1.5rem] text-button">2</span>
                    <p class="text-text_light text-[1.3rem]">Videos Comments</p>
                </div>
            </div>
            <router-link to="#" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[10rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Comments</router-link>
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

export default {
    components: {
        Header,
        Sidebar
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    }
}
</script>