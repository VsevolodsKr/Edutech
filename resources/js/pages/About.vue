<template>
<Header />
<section v-if="showSidebar == true" class="pl-[22rem] pt-[2rem] pr-[1rem] bg-background">
    <div class="flex items-center flex-wrap gap-[1.5rem] [@media(max-width:1715px)]:flex [@media(max-width:1715px)]:flex-col-reverse">
        <div class="flex-[1_1_40rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <img src="../images/about-img.svg" class="w-full h-[50rem]">
        </div>
        <div class="flex-[1_1_50rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <h3 class="text-[2rem] text-text_dark capitalize">Why Choose Us?</h3>
            <p class="text-[1.3rem] leading-2 text-text_light py-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem accusantium quaerat, assumenda sapiente debitis sed explicabo autem consequatur laudantium adipisci velit tenetur vitae ipsam repudiandae nobis ex. Nostrum, iusto amet!</p>
            <router-link to="/courses" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-[8rem] transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base">Our Courses</router-link>
        </div>
    </div>
    <div class="flex justify-evenly flex-wrap gap-y-[2rem] justify-center items-start mt-[2rem]">
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-graduation-cap text-[3rem] text-button"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem]">+10k</h3>
                <p class="text-[1rem] text-text_light">Online Courses</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-user-graduate text-[3rem] text-button"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem]">+32k</h3>
                <p class="text-[1rem] text-text_light">Brilliant students</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-chalkboard-user text-[3rem] text-button"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem]">+1k</h3>
                <p class="text-[1rem] text-text_light">Expert Teachers</p>
            </div>
        </div>
        <div class="rounded-lg bg-base p-[2rem] flex items-center gap-[2rem] w-[20rem]">
            <i class="fas fa-briefcase text-[3rem] text-button"></i>
            <div>
                <h3 class="text-[1.5rem] text-text_dark mb-[.3rem]">100%</h3>
                <p class="text-[1rem] text-text_light">Job Placement</p>
            </div>
        </div>
    </div>
</section>
<section v-if="showSidebar == true" class="pl-[22rem] pt-[2rem] bg-background">
    <h1 class="text-[1.5rem] text-text_dark capitalize">Student's Reviews</h1>
    <hr class="border-[#ccc] mb-[2rem] mr-[1rem]">
    <div class="grid grid-cols-[repeat(auto-fit,_minmax(26rem,_1fr))] gap-[1.5rem] justify-center items-start pr-[1rem]">
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-1.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-2.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-3.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-4.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-5.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
        <div class="rounded-lg bg-base p-[2rem] pt-0 mb-[2rem]">
            <div class="mt-[2rem] flex items-start gap-[.5rem] mb-[1rem]">
                <img src="../images/pic-6.png" class="h-[5rem] w-[5rem] object-cover rounded-[50%]">
                <div class="flex flex-col">
                    <h3 class="text-[1.3rem] text-text_dark pt-[.3rem]">publisher</h3>
                    <div class="text-[1rem] text-button2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <p class="leading-[1.7rem] text-[1rem] text-text_light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
    </div>
</section>
<section v-if="showSidebar == false" class="pl-[2rem] pt-[2rem] pr-[1rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]">
    <div class="flex items-center flex-wrap gap-[1.5rem] @media(max-width:1715px)]:flex [@media(max-width:1715px)]:flex-col-reverse">
        <div class="flex-[1_1_40rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <img src="../images/about-img.svg" class="w-full h-[50rem] [@media(max-width:550px)]:h-[25rem]">
        </div>
        <div class="flex-[1_1_50rem] [@media(max-width:1715px)]:flex-[1_1_1rem]">
            <h3 class="text-[2rem] text-text_dark capitalize [@media(max-width:550px)]:text-[1.7rem]">Why Choose Us?</h3>
            <p class="text-[1.3rem] leading-2 text-text_light py-[1rem] [@media(max-width:550px)]:text-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem accusantium quaerat, assumenda sapiente debitis sed explicabo autem consequatur laudantium adipisci velit tenetur vitae ipsam repudiandae nobis ex. Nostrum, iusto amet!</p>
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
<section v-if="showSidebar == false" class="pl-[2rem] pt-[2rem] bg-background [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-0">
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
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
            <p class="leading-[1.7rem] text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] [@media(max-width:550px)]:leading-[1rem]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta architecto reiciendis quidem veritatis ea ipsa alias, molestias libero nostrum tenetur dolorem delectus, commodi magni repudiandae in quis esse amet obcaecati.</p>
        </div>
    </div>
</section>
<Sidebar />
</template>
<script>
import Header from "../components/Header.vue";
import Sidebar from "../components/Sidebar.vue";
import store from '../store/store';

export default{
    components: {
        Header,
        Sidebar
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
}
</script>