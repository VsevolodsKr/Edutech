<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <div :class="[(showSidebar == true && width > 1180) ? '' : (showSidebar == false || (showSidebar == true && width < 1180)) ? '' : '', 'flex items-center flex-wrap gap-[1.5rem] [@media(max-width:1715px)]:flex [@media(max-width:1715px)]:flex-col-reverse']">
        <div class="flex-[1_1_45rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <img src="../images/contact-img.svg" class="w-full h-[50rem] [@media(max-width:550px)]:h-[25rem]">
        </div>
        <form method="post" class="flex-[1_1_35rem] bg-base p-[2rem] text-center rounded-lg [@media(max-width:1715px)]:flex-[1_1_1rem] [@media(max-width:1715px)]:p-[1rem]">
            <h3 class="mb-[1rem] capitalize text-text_dark text-[2rem] [@media(max-width:550px)]:text-[1.7rem]">Get In Touch</h3>
            <ul class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]" v-if="errorList.length > 0">
                <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i class="fa fa-warning"></i> {{ error }}</li>
            </ul>
            <input v-model="name" type="text" name="name" placeholder="Enter you name..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
            <input v-model="email" type="email" name="email" placeholder="Enter you email..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">    
            <input v-model="number" type="tel" name="phone" placeholder="Enter you phone number..." required maxlength="50" class="w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0">
            <textarea v-model="message" name="message" placeholder="Enter your message..." required maxlength="1000" cols="30" rows="10" class="h-[20rem] resize-none w-full rounded-lg bg-background my-[1rem] p-[.5rem] text-[1rem] text-text_dark outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:mb-0"></textarea>
            <button @click="handleSubmit" type="submit" value="Send Message" name="submit" class="w-full bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Send Message</button>
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
import Swal from 'sweetalert2';
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
          name: null,
          email: null,
          number: null,
          message: null,
          errorList: '', 
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    methods: {
        async handleSubmit(e){
            if(e && e.preventDefault){
                e.preventDefault()
            }
            const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
            const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
            const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')
            Swal.fire({
                title: "Are you sure?",
                text: "Check, is everything correct in your message form",
                icon: "warning",
                color: text_dark,
                background: background,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: button4,
                confirmButtonText: "Yes, send it!"
            }).then((result) => {
            if (result.isConfirmed) {
                let data = new FormData()
                data.append('name', this.name)
                data.append('email', this.email)
                data.append('number', this.number)
                data.append('message', this.message)
                axios.post('/api/contact/send', data).then(() => {
                    Swal.fire({
                        title: "Your message successfully sent to Edutech!",
                        text: "We will do our best to make Edutech better place to study!",
                        icon: "success",
                    })
                }).catch((err) => {
                    this.errorList = err.response.data.message
                })
        }
        });  
        }
    }
}
</script>