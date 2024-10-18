<template>
    <Admin_Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <form method="post" class="bg-base rounded-lg p-[1rem] w-[50rem]">
            <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Login Now</h3>
            <ul :class="[status == 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]' , 'rounded-xl']" v-if="errorList.length > 0">
                <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i :class="[status == 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}</li>
            </ul>
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your email <span class="text-[#ff0000]">*</span></p>
            <input v-model="email" type="email" name="email" placeholder="Enter your email..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Your password <span class="text-[#ff0000]">*</span></p>
            <input v-model="password" type="password" name="password" placeholder="Enter your password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <button @click="handleSubmit" type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">Login</button>
        </form>
    </section>
    <Admin_Sidebar />
    </template>
    <script>
    import Admin_Header from '../components/Admin_Header.vue';
    import Admin_Sidebar from '../components/Admin_Sidebar.vue';
    import store from '../store/store';
    import { useWindowSize } from '@vueuse/core';
    import { useRouter } from 'vue-router';
    
    const {width} = useWindowSize()
    const router = new useRouter()
    export default{
        components: {
            Admin_Header,
            Admin_Sidebar
        },
        data: () => {
            return{
                width,
                email: '',
                password: '',
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
                e.preventDefault()
                let data = new FormData()
                data.append('email', this.email)
                data.append('password', this.password)
                try{
                    const response = await axios.post('api/login/send', data)
                    console.log(response)
                    localStorage.setItem('token', response.data.token)
                    store.commit('setUser', response.data.data)
                    this.errorList = response.data.message
                    this.status = response.data.status
                    console.log(response.data.is_teacher)
                    setTimeout(() => {
                        if(response.data.is_teacher){
                            this.$router.push('/dashboard', response.data)
                        }else{
                            this.$router.push('/', response.data)
                        }
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