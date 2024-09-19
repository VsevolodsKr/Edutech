<template>
<Header />
<section v-if="showSidebar == true" class="pl-[22rem] pr-[1.5rem] pt-[2rem] bg-background">
    <h1 class="text-[1.5rem] text-text_dark capitalize">Expert Teachers</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <form class="w-full rounded-lg bg-[#eee] py-[.5rem] px-[1.5rem] flex gap-[2rem] bg-base" method="post" action="">
        <input class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none"type="text" name="search_box" required placeholder="search teachers..." maxlength="100">
        <button class="bg-transparent text-[1rem] cursor-pointer text-text_light fa fa-search hover:text-button" type="submit"></button>
    </form>
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start mt-[1rem]">
        <div class="bg-base rounded-lg p-[2rem] text-center">
            <h3 class="text-[2rem] text-text_dark capitalize pb-[.5rem]">Become A Teacher</h3>
            <p class="leading-1.7 py-[.5rem] text-text_light text-[1.3rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex dolores, animi fuga suscipit ullam repellendus in facere natus. A sit soluta eligendi accusamus quasi odit!</p>
            <div class="flex justify-center">
                <router-link to="/register" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[.7rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Get Started</router-link>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-1.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-2.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-3.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-4.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-5.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-6.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-7.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">View Profile</router-link>
        </div>
    </div>
</section>
<section v-if="showSidebar == false" class="pl-[2rem] pr-[1.5rem] pt-[2rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Expert Teachers</h1>
    <hr class="border-[#ccc] mb-[2rem]">
    <form class="w-full rounded-lg bg-[#eee] py-[.5rem] px-[1.5rem] flex gap-[2rem] bg-base" method="post" action="">
        <input class="w-full text-[1.3rem] bg-transparent outline-none border-transparent text-text_light focus:outline-none [@media(max-width:550px)]:text-[1rem]"type="text" name="search_box" required placeholder="search teachers..." maxlength="100">
        <button class="bg-transparent text-[1rem] cursor-pointer text-text_light fa fa-search hover:text-button" type="submit"></button>
    </form>
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start mt-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
        <div class="bg-base rounded-lg p-[2rem] text-center">
            <h3 class="text-[2rem] text-text_dark capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Become A Teacher</h3>
            <p class="leading-1.7 py-[.5rem] text-text_light text-[1.3rem] [@media(max-width:550px)]:text-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex dolores, animi fuga suscipit ullam repellendus in facere natus. A sit soluta eligendi accusamus quasi odit!</p>
            <div class="flex justify-center">
                <router-link to="/register" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[.7rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Get Started</router-link>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-1.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-2.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-3.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-4.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-5.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-6.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem] [@media(max-width:550px)]:mb-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-7.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">teacher</span>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
            <router-link to="/teacher_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] mt-[1rem] block w-[9rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">View Profile</router-link>
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