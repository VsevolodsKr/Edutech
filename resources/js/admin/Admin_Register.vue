<template>
    <Preloader />
    <Admin_Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <div class="bg-base rounded-lg p-[1rem] w-[50rem]">
            <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Register</h3>
            <ul :class="[status == 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]' , 'rounded-xl']" v-if="errorList.length > 0">
                <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i :class="[status == 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}</li>
            </ul>
            <div class="flex gap-[2rem]">
                <div class="flex-[1_1_25rem]">
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your name</p>
                    <input v-model="name" type="name" name="name" placeholder="Enter your name..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full">
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your profession</p>
                    <select v-model="profession" name="profession" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:w-full">
                        <option value="" selected>Select your profession</option>
                        <option value="english">English teacher</option>
                        <option value="math">Math teacher</option>
                        <option value="art">Art teacher</option>
                        <option value="science">Science teacher</option>
                        <option value="history">History teacher</option>
                        <option value="music">Music teacher</option>
                        <option value="geography">Geography teacher</option>
                        <option value="PE">Physical education (PE) teacher</option>
                        <option value="biology">Biology teacher</option>
                        <option value="chemistry">Chemistry teacher</option>
                        <option value="physics">Physics teacher</option>
                        <option value="IT">Information technology (IT) teacher</option>
                        <option value="social">Social studies teacher</option>
                        <option value="technology">Technology teacher</option>
                        <option value="philosophy">Philosophy teacher</option>
                        <option value="design">Design teacher</option>
                        <option value="literature">Literature teacher</option>
                        <option value="algebra">Algebra teacher</option>
                        <option value="geometry">Geometry teacher</option>
                    </select>
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your email</p>
                    <input v-model="email" type="email" name="email" placeholder="Enter your email..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
                </div>
                <div class="flex-[1_1_25rem] [@media(max-width:550px)]:flex-col">
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your password</p>
                    <input v-model="password" type="password" name="password" placeholder="Enter your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Confirm your password</p>
                    <input v-model="conf_password" type="password" name="password" placeholder="Confirm your new password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
                    <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Select image</p>
                    <input id="img" type="file" accept="image/*" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
                </div>
            </div>
            <button @click="handleSubmit()" type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">Register</button>
        </div>
    </section>
    <Admin_Sidebar />
    </template>
    <script>
    import Admin_Header from '../components/Admin_Header.vue';
    import Admin_Sidebar from '../components/Admin_Sidebar.vue';
    import store from '../store/store';
    import { useWindowSize } from '@vueuse/core'
    import axios from 'axios';
    import Preloader from '../components/Preloader.vue';
    
    const {width} = useWindowSize()
    export default{
        components: {
            Admin_Header,
            Admin_Sidebar,
            Preloader
        },
        data: () => {
            return{
                width,
                name: '',
                profession: '',
                email: '',
                password: '',
                conf_password: '',
                errorList: '',
                status: '',  
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
                const config = {
                    headers: {
                        'description-Type': 'multipart/form-data'
                    }
                }
                let data = new FormData()
                data.append('name', this.name)
                data.append('profession', this.profession)
                data.append('email', this.email)
                data.append('password', this.password)
                data.append('conf_password', this.conf_password)
                if(document.querySelector('#img').files[0]){
                    data.append('image', document.querySelector('#img').files[0])
                }
                try{
                    const response = await axios.post('api/admin_register/send', data, config)
                    console.log(response.data.message)
                    this.errorList = response.data.message
                    this.status = response.data.status
                    setTimeout(() => {
                        this.$router.push('/admin_login')
                    }, 500)            
                }catch(err){
                    console.log(err.response.data)
                    this.errorList = err.response.data.message
                    this.status = err.response.data.status
                }
            }
        }
    }
    </script>