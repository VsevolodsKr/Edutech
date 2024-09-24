<template>
<Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <form @submit="handleSubmit" method="post" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
        <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Register</h3>
        <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your name <span class="text-[#ff0000]">*</span></p>
        <input v-model="name" type="name" name="name" placeholder="Enter your name..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
        <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your email <span class="text-[#ff0000]">*</span></p>
        <input v-model="email" type="email" name="email" placeholder="Enter your email..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
        <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your password <span class="text-[#ff0000]">*</span></p>
        <input v-model="password" type="password" name="password" placeholder="Enter your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
        <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Confirm password <span class="text-[#ff0000]">*</span></p>
        <input v-model="conf_password" type="password" name="password2" placeholder="Confirm your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
        <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Select image <span class="text-[#ff0000]">*</span></p>
        <input type="file" accept="image/*" required class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
        <button type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:py-[.2rem]">Register</button>
    </form>
    </section>
<Sidebar />
</template>
<script>
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';
import { useWindowSize } from '@vueuse/core'
import axios from 'axios';

const {width} = useWindowSize()
export default{
    components: {
        Header,
        Sidebar
    },
    data: () => {
        return{
          width,
          name:'',
          email: '',
          password: '',
          conf_password: '',  
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    methods: {
        async handleSubmit(e){
            e.preventDefault()
            let data = new FormData()
            data.append('name', this.name)
            data.append('email', this.email)
            data.append('password', this.password)
            data.append('conf_password', this.conf_password)
            const response = await axios.post('/api/register/enter', data);
            console.log(response)
        }
    }
}
</script>