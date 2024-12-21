<template>
<Preloader />
<Header />
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
    <div class="flex items-center flex-wrap gap-[1.5rem] @media(max-width:1715px)]:flex [@media(max-width:1715px)]:flex-col-reverse">
        <div class="flex-[1_1_40rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <img src="../images/about-img.svg" class="w-full h-[50rem] [@media(max-width:550px)]:h-[25rem]">
        </div>
        <div class="flex-[1_1_50rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <h3 class="text-[2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.7rem]">Why Choose Us?</h3>
            <p class="text-[1.3rem] leading-2 text-text_light py-[1rem] [@media(max-width:550px)]:text-[1rem]">At Edutech, we believe that quality education should be accessible to everyone, no matter where they are. Our platform connects learners and educators from all around the world, providing interactive courses and resources designed to spark curiosity and foster growth. We focus on a personalized learning experience that empowers students to achieve their goals on their own terms. By bringing together experts and cutting-edge technology, we’re reimagining how education is delivered and received—making learning flexible, engaging, and impactful for a global community</p>
            <router-link to="/courses" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem] [@media(max-width:550px)]:w-[7rem]">Our Courses</router-link>
        </div>
    </div>
    <div class="flex justify-evenly flex-wrap gap-y-[2rem] justify-center items-start mt-[2rem]">
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-graduation-cap text-[3rem] text-button [@media(max-width:550px)]:text-[2.3rem]"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem] [@media(max-width:550px)]:text-[1.2rem]">+10k</h3>
                <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Online Courses</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-user-graduate text-[3rem] text-button [@media(max-width:550px)]:text-[2.3rem]"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem] [@media(max-width:550px)]:text-[1.2rem]">+32k</h3>
                <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Brilliant students</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-chalkboard-user text-[3rem] text-button [@media(max-width:550px)]:text-[2.3rem]"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem] [@media(max-width:550px)]:text-[1.2rem]">+1k</h3>
                <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Expert Teachers</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-briefcase text-[3rem] text-button [@media(max-width:550px)]:text-[2.3rem]"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem] [@media(max-width:550px)]:text-[1.2rem]">100%</h3>
                <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem]">Job Placement</p>
            </div>
        </div>
    </div>
</section>
<section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-0']">
    <h1 class="text-[1.5rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.2rem]">Student's Reviews</h1>
    <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(26rem,_1fr))] gap-[1.5rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-[.5rem]">
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-1.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">I’ve taken several courses on Edutech, and I’m amazed at how engaging and well-structured the lessons are. The instructors are passionate experts, and the interactive elements make learning so much easier. The flexibility to learn at my own pace has been a game-changer for balancing my studies with work. Highly recommend to anyone looking to upgrade their skills!</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-2.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Edutech has become my go-to for learning new skills. The courses are in-depth, updated regularly, and cover a wide variety of topics. It’s amazing to access world-class education from the comfort of home. Every course I’ve taken has been a solid investment in my personal and professional growth</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-3.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">I wasn’t sure what to expect, but Edutech has exceeded every one of my expectations. The content quality, the ease of navigating through modules, and the active community are fantastic. I also love how I can interact with other students and ask questions directly to the instructors. Worth every penny!</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-4.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Being able to learn on my schedule has been invaluable. The mobile-friendly design means I can access lessons on the go, and I feel connected to a community of learners. Edutech has really made it possible for me to pursue my interests and boost my career skills without compromising my daily responsibilities</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-5.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Whenever I’ve had a question or needed help, Edutech’s support team has been there within minutes! The platform is user-friendly, the content is superb, and I feel like I’m actually gaining skills I can apply right away. Can’t recommend it enough!</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-6.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%] [@media(max-width:550px)]:h-[3rem] [@media(max-width:550px)]:w-[3rem]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem] [@media(max-width:550px)]:text-[1rem]">publisher</h3>
                    <div class="text-[1rem] text-button2 [@media(max-width:550px)]:text-[.7rem]">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">I’m so impressed by the depth and breadth of the courses offered. From the interactive exercises to the clear video lessons, everything is top-notch. Edutech makes it easy to stay motivated and achieve my goals. It’s as close to a traditional classroom experience as you can get online!</p>
        </div>
    </div>
</section>
<Sidebar />
</template>
<script>
import Header from "../components/Header.vue";
import Sidebar from "../components/Sidebar.vue";
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
          width  
        }
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
}
</script>