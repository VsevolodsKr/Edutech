<template>
<Header />
    <section v-if="showSidebar == true" class="pl-[22rem] pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center">
        <form action="" method="post" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
        <h3 class="text-[1.5rem] capitalize text-text_dark text-center">Register</h3>
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your name <span class="text-[#ff0000]">*</span></p>
        <input type="name" name="name" placeholder="Enter your name..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your email <span class="text-[#ff0000]">*</span></p>
        <input type="email" name="email" placeholder="Enter your email..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your password <span class="text-[#ff0000]">*</span></p>
        <input type="password" name="password" placeholder="Enter your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">        
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Confirm password <span class="text-[#ff0000]">*</span></p>
        <input type="password" name="password2" placeholder="Confirm your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Select image <span class="text-[#ff0000]">*</span></p>
        <input type="file" accept="image/*" required class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <button type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Register</button>
    </form>
    </section>
    <section v-if="showSidebar == false" class="pl-[2rem] pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center">
        <form action="" method="post" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
        <h3 class="text-[1.5rem] capitalize text-text_dark text-center">Register</h3>
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your name <span class="text-[#ff0000]">*</span></p>
        <input type="name" name="name" placeholder="Enter your name..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your email <span class="text-[#ff0000]">*</span></p>
        <input type="email" name="email" placeholder="Enter your email..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Your password <span class="text-[#ff0000]">*</span></p>
        <input type="password" name="password" placeholder="Enter your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">        
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Confirm password <span class="text-[#ff0000]">*</span></p>
        <input type="password" name="password2" placeholder="Confirm your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <p class="text-[1.2rem] text-text_light pt-[1rem]">Select image <span class="text-[#ff0000]">*</span></p>
        <input type="file" accept="image/*" required class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none">
        <button type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Register</button>
    </form>
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