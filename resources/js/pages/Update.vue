<template>
    <Header />
        <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
            <form action="" method="post" enctype="multipart/form-data" class="bg-base rounded-lg p-[1rem] w-[50rem]">
            <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Update Profile</h3>
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Update name</p>
            <input type="name" name="name" value="username" required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Update email</p>
            <input type="email" name="email" value="edutech@gmail.com" required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Previous password</p>
            <input type="password" name="password" placeholder="Enter your old password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">New password</p>
            <input type="password" name="password" placeholder="Enter your new password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">        
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Confirm password</p>
            <input type="password" name="password2" placeholder="Confirm your new password..." required maxlength="50" class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <p class="text-[1.2rem] text-text_light pt-[1rem] [@media(max-width:550px)]:text-[.9rem]">Select image</p>
            <input type="file" accept="image/*" required class="text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]">
            <button type="submit" name="submit" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full mt-[1.5rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:text-[.7rem]">Update</button>
        </form>
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