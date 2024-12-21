<template>
    <header v-if="showSidebar == true && width > 1180" class="sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base pl-[20rem]">
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <router-link to="/"><h1 class="text-[2rem] text-text_dark mr-[.7rem]">EduTech</h1></router-link>
            <form class="w-[50rem] rounded-lg bg-[#eee] py-[.5rem] px-[1.5rem] flex gap-[2rem] bg-background" method="post" action="">
                <input class="w-full text-[1.3rem] bg-transparent outline-none text-text_light focus:outline-none"type="text" name="search_box" required placeholder="search courses..." maxlength="100">
                <button class="bg-transparent text-[1rem] cursor-pointer text-text_dark fa fa-search hover:text-button" type="submit"></button>
            </form>
            <div class="text-[1.5rem] flex">
                <button @click="changeShowSide(); updateShowSidebar()" class=" hidden items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:1180px)]:flex"><div class="fa fa-bars"></div></button>
                <button class="hidden flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"><div class="fa fa-search"></div></button>
                <button @click="show = !show" class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"><div class="fa fa-user"></div></button>
                <button @click="switchTheme();" class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem]"><div :class="[currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon']"></div></button>
            </div>
            <div v-if="show == true && user != null" class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100">
                <div class="flex justify-center">
                    <img :src="user.image" class="h-[8rem] w-[8rem] rounded-[50%] object-contain mb-[1rem]">
                </div>
                <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap">{{ user.name }}</h3>
                <p class="text-[1.3rem] text-text_light">student</p>
                <router-link to="/profile" class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base mb-[1rem]">View Profile</router-link>
                <div class="flex gap-[1rem]">
                    <router-link to="/login" class="bg-button2 text-base border-2 border-button2 rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base">Login</router-link>
                    <router-link to="/register" class="bg-button2 text-base border-2 border-button2 rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base">Register</router-link>
                </div>
                <button @click="logout" class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base">Logout</button>
            </div>
            <div v-if="show == true && user == null" class="absolute top-[120%] right-[5rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100">
                <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">Please login or register</h3>
                <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem]">
                    <router-link to="/login" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Login</router-link>
                    <router-link to="/register" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Register</router-link>
                </div>
            </div>
        </section>
    </header>
    <header v-if="showSidebar == false || (showSidebar == true && width < 1180)" class="sticky z-[120] top-0 right-0 left-0 border-b-2 bg-base">
        <section class="flex items-center justify-between relative py-[.5rem] px-[1.5rem]">
            <router-link to="/"><h1 class="text-[2rem] text-text_dark mr-[.7rem] [@media(max-width:970px)]:text-[1.5rem]">EduTech</h1></router-link>
            <form class="w-[50rem] rounded-lg bg-[#eee] py-[.5rem] px-[1.5rem] flex gap-[2rem] bg-background [@media(max-width:700px)]:absolute [@media(max-width:700px)]:top-[99%] [@media(max-width:700px)]:left-[0] [@media(max-width:700px)]:right-[0] [@media(max-width:700px)]:border-t [@media(max-width:700px)]:border-b [@media(max-width:700px)]:rounded-none [@media(max-width:700px)]:w-auto [@media(max-width:700px)]:p-8 [@media(max-width:700px)]:[clip-path:polygon(0_0,_100%_0,_100%_0,_0_0)] [@media(max-width:700px)]:[transition:.2s_linear] [@media(max-width:700px)]:active:[clip-path:polygon(0_0,_100%_0,_100%_100%,_0_100%)]" method="post" action="">
                <input class="w-full text-[1.3rem] bg-transparent outline-none text-text_light focus:outline-none [@media(max-width:970px)]:text-[1rem]"type="text" name="search_box" required placeholder="search courses..." maxlength="100">
                <button class="bg-transparent text-[1rem] cursor-pointer text-text_dark fa fa-search hover:text-button" type="submit"></button>
            </form>
            <div class="text-[1.5rem] flex [@media(max-width:970px)]:text-[1rem]">
                <button @click="changeShowSide(); updateShowSidebar()" class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"><div class="fa fa-bars"></div></button>
                <button class="hidden flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem] [@media(max-width:700px)]:flex"><div class="fa fa-search"></div></button>
                <button @click="show = !show" class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"><div class="fa fa-user"></div></button>
                <button @click="switchTheme();" class="flex items-center justify-center text-text_dark bg-background rounded-lg h-[3rem] w-[3rem] leading-[4rem] cursor-pointer text-center ml-[.7rem] hover:bg-text_dark hover:text-base hover:flex hover:items-center hover:justify-center hover:text-[1.5rem] [@media(max-width:970px)]:h-[2.5rem] [@media(max-width:970px)]:w-[2.5rem] [@media(max-width:970px)]:mt-[.2rem]"><div :class="[currentTheme === 'theme-light' ? 'fas fa-sun' : 'fas fa-moon']"></div></button>
            </div>
            <div v-if="show == true && user != null" class="absolute top-[120%] right-[1rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[20rem] transition ease-linear duration-200 transform scale-100">
                <div class="flex justify-center">
                    <img :src="user.image" class="h-[8rem] w-[8rem] rounded-[50%] object-contain mb-[1rem]">
                </div>
                <h3 class="text-[1.5rem] text-text_dark text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1.2rem]">{{ user.name }}</h3>
                <p class="text-[1.3rem] text-text_light [@media(max-width:550px)]:text-[1rem]">student</p>
                <router-link to="/profile" class="bg-button text-base border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base mb-[1rem] [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Profile</router-link>
                <div class="flex gap-[1rem]">
                    <router-link to="/login" class="bg-button2 text-base border-2 border-button2 rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Login</router-link>
                    <router-link to="/register" class="bg-button2 text-base border-2 border-button2 rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Register</router-link>
                </div>
                <button @click="logout" class="bg-button4 text-base border-2 border-button4 rounded-lg mt-[1rem] py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Logout</button>
            </div>
            <div v-if="show == true && user == null" class="absolute top-[120%] right-[1rem] bg-base rounded-lg p-[1.5rem] text-center overflow-hidden origin-top-right w-[15rem] transition ease-linear duration-200 transform scale-100">
                <h3 class="text-[1.3rem] text-text_dark text-center overflow-hidden text-ellipsis whitespace-nowrap [@media(max-width:550px)]:text-[1rem]">Please login or register</h3>
                <div class="w-full flex gap-[.5rem] px-[1rem] pt-[.5rem]">
                    <router-link to="/login" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Login</router-link>
                    <router-link to="/register" class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:text-button2 hover:bg-base hover:transition hover:ease-linear hover:duration-200 [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Register</router-link>
                </div>
            </div>
        </section>
    </header>
</template>
<script>
import store from '../store/store'
import { useWindowSize } from '@vueuse/core'

const {width} = useWindowSize()
let showSide = true;
export default {
    data: () => {
        return {
            show: false,
            showSide: true,
            width,
            currentTheme: localStorage.getItem('theme-color'),
            user: null,
        }
    },
    methods: {
        updateShowSidebar: function() {
            store.commit('setShowSidebar', showSide)
        },
        changeShowSide(){
            showSide = !showSide
        },
        switchTheme(){
            const storedTheme = localStorage.getItem('theme-color')
            if(storedTheme === 'theme-dark'){
                localStorage.setItem('theme-color', 'theme-light')
                this.currentTheme = localStorage.getItem('theme-color')
                document.documentElement.style.setProperty('--background', '#ddd')
                document.documentElement.style.setProperty('--base', '#fff')
                document.documentElement.style.setProperty('--text_dark', '#000')
                document.documentElement.style.setProperty('--text_light', '#777')
                document.documentElement.style.setProperty('--button', '#7499f8')
                document.documentElement.style.setProperty('--button3', '#abd032')
                document.documentElement.style.setProperty('--selection', '#749af8b9')
            }else{
                localStorage.setItem('theme-color', 'theme-dark')
                this.currentTheme = localStorage.getItem('theme-color')    
                document.documentElement.style.setProperty('--background', '#777')
                document.documentElement.style.setProperty('--base', '#434343')
                document.documentElement.style.setProperty('--text_dark', '#fff')
                document.documentElement.style.setProperty('--text_light', '#ddd')
                document.documentElement.style.setProperty('--button', '#eae883')
                document.documentElement.style.setProperty('--button3', '#ea7d10')
                document.documentElement.style.setProperty('--selection', '#eae883b9')       
            }
        },
        logout: function (){
            localStorage.setItem('token', '')
            window.location.reload()
        }
    },
    computed: {
        showSidebar: function (){
            showSide = store.getters.getShowSidebar
            return store.getters.getShowSidebar
        },
        
    },
    mounted(){
        axios.get('/api/user', {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}}).then((response)=>{
            this.user = response.data
            this.user.image = new URL(this.user.image, import.meta.url)
        })
    },
    created(){
        const storedTheme = localStorage.getItem('theme-color')
            if(storedTheme === 'theme-light'){
                document.documentElement.style.setProperty('--background', '#ddd')
                document.documentElement.style.setProperty('--base', '#fff')
                document.documentElement.style.setProperty('--text_dark', '#000')
                document.documentElement.style.setProperty('--text_light', '#777')
                document.documentElement.style.setProperty('--button', '#7499f8')
                document.documentElement.style.setProperty('--button3', '#abd032')
                document.documentElement.style.setProperty('--selection', '#749af8b9')
            }else{   
                document.documentElement.style.setProperty('--background', '#777')
                document.documentElement.style.setProperty('--base', '#434343')
                document.documentElement.style.setProperty('--text_dark', '#fff')
                document.documentElement.style.setProperty('--text_light', '#ddd')
                document.documentElement.style.setProperty('--button', '#eae883')
                document.documentElement.style.setProperty('--button3', '#ea7d10')
                document.documentElement.style.setProperty('--selection', '#eae883b9')       
            }
    }
}
</script>