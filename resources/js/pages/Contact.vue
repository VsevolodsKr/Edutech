<template>
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <div :class="[(showSidebar == true && width > 1180) ? '' : (showSidebar == false || (showSidebar == true && width < 1180)) ? '' : '', 'flex items-center flex-wrap gap-[1.5rem] [@media(max-width:1715px)]:flex [@media(max-width:1715px)]:flex-col-reverse']">
        <div class="flex-[1_1_45rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <img src="../images/contact-img.svg" class="w-full h-[50rem] [@media(max-width:550px)]:h-[25rem]">
        </div>
        <form action="" method="post" class="flex-[1_1_35rem] bg-base p-[2rem] text-center rounded-lg [@media(max-width:1715px)]:flex-[1_1_1rem] [@media(max-width:1715px)]:p-[1rem]">
            <h3 class="mb-[1rem] capitalize text-text_dark text-[2rem] [@media(max-width:550px)]:text-[1.7rem]">Get In Touch</h3>
            <input type="text" name="name" placeholder="Enter you name..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
            <input type="email" name="email" placeholder="Enter you email..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">    
            <input type="tel" name="phone" placeholder="Enter you phone number..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
            <textarea name="message" placeholder="Enter your message..." required maxlength="1000" cols="30" rows="10" class="h-[20rem] resize-none w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0"></textarea>
            <button type="submit" value="Send Message" name="submit" class="w-full bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Send Message</button>
        </form>
    </div>
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] justify-center items-start mt-[3rem] pb-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
        <div class="flex flex-col justify-center items-center bg-base rounded-lg p-[.3rem] w-full">
            <i class="fas fa-phone text-[2.5rem] text-button my-[1rem] [@media(max-width:550px)]:text-[1.5rem]"></i>
            <h3 class="text-[1.5rem] text-text_dark mt-[.5rem] [@media(max-width:550px)]:text-[1.2rem] [@media(max-width:550px)]:mt-0">Phone number</h3>
            <a href="tel:+37122222222" class="block text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">+37122222222</a>
        </div>
        <div class="flex flex-col justify-center items-center bg-base rounded-lg p-[.3rem] w-full">
            <i class="fas fa-envelope text-[2.5rem] text-button my-[1rem] [@media(max-width:550px)]:text-[1.5rem]"></i>
            <h3 class="text-[1.5rem] text-text_dark mt-[.5rem] [@media(max-width:550px)]:text-[1.2rem] [@media(max-width:550px)]:mt-0">Email address</h3>
            <a href="mailto:edutech@gmail.com" class="block text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">edutech@gmail.com</a>
        </div>
        <div class="flex flex-col justify-center items-center bg-base rounded-lg p-[.3rem] w-full">
            <i class="fas fa-map-marker-alt text-[2.5rem] text-button my-[1rem] [@media(max-width:550px)]:text-[1.5rem]"></i>
            <h3 class="text-[1.5rem] text-text_dark mt-[.5rem] [@media(max-width:550px)]:text-[1.2rem] [@media(max-width:550px)]:mt-0">Office address</h3>
            <a href="https://maps.app.goo.gl/9QKNkCmYL57HJPnN7" target="_blank" class="block text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">Satiksmes iela 6, Liepāja</a>
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

const {width} = useWindowSize()
export default{
    components: {
        Header,
        Sidebar
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