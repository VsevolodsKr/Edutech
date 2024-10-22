<template>
    <Preloader />
    <Header />
        <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
            <div class="bg-base rounded-lg p-[1rem] w-[50rem]">
            <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Update Profile</h3>
            <ul :class="[status == 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]' , 'rounded-xl']" v-if="errorList.length > 0">
                <li class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]" v-for="(error, index) in errorList" :key="index"><i :class="[status == 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}</li>
            </ul>
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Update name</p>
            <input v-model="name" type="name" name="name" :placeholder="user.name" required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Update email</p>
            <input v-model="email" type="email" name="email" :placeholder="user.email" required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Previous password</p>
            <input v-model="p_password" type="password" name="password" placeholder="Enter your old password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">New password</p>
            <input v-model="n_password" type="password" name="password" placeholder="Enter your new password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Confirm password</p>
            <input v-model="c_password" type="password" name="password2" placeholder="Confirm your new password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Select image</p>
            <input id="img" type="file" accept="image/*" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <button @click="handleSubmit()" type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">Update</button>
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
            width,
            user: null,
            name: '',
            email: '',
            p_password: '',
            n_password: '',
            c_password: '',
            errorList: '',
            status: ''  
        }
    },
    computed: {
    showSidebar: function (){
        return store.getters.getShowSidebar
    }
    },
    mounted(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.user = response.data
            this.user.image = new URL(this.user.image, import.meta.url)
        })
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },
    created(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.user = response.data
            this.user.image = new URL(this.user.image, import.meta.url)
        })
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
            data.append('old_email', this.user.email)
            data.append('email', this.email)
            data.append('p_password', this.p_password)
            data.append('n_password', this.n_password)
            data.append('c_password', this.c_password)
            if(document.querySelector('#img').files[0]){
                data.append('image', document.querySelector('#img').files[0])
            }
            try{
                const response = await axios.post('api/update/send', data, config)
                console.log(response.data.message)
                this.errorList = response.data.message
                this.status = response.data.status
                setTimeout(() => {
                    this.$router.push('/', this.user)
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