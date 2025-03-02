<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">About Us</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] items-center">
                    <div class="text-center">
                        <img src="../images/about-img.svg" alt="About Us" class="w-full h-[40rem] [@media(max-width:550px)]:h-[20rem]">
                    </div>
                    <div class="text-center">
                        <h3 class="text-[1.75rem] text-text_dark mb-[2rem] [@media(max-width:550px)]:text-[1.25rem]">Why Choose Us?</h3>
                        <p class="text-[1.1rem] text-text_light leading-[1.8] [@media(max-width:550px)]:text-[0.9rem]">
                            We are dedicated to providing high-quality education through our innovative online learning platform. 
                            Our courses are designed by industry experts and educators to ensure you receive the best possible learning experience.
                        </p>
                        <div class="mt-[2rem] grid grid-cols-[repeat(auto-fit,_minmax(16rem,_1fr))] gap-[1.5rem]">
                            <div v-for="(stat, index) in statistics" 
                                 :key="index"
                                 class="flex items-center gap-[1rem] h-[7rem] bg-base rounded-lg px-[1.2rem]">
                                <i :class="stat.icon" class="text-[2rem] text-button"></i>
                                <div class="text-left">
                                    <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.25rem]">{{ stat.value }}</h3>
                                    <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[0.9rem]">{{ stat.label }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-[3rem]">
                    <h3 class="text-[1.75rem] text-text_dark text-center mb-[2rem] [@media(max-width:550px)]:text-[1.25rem]">Our Features</h3>
                    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem]">
                        <div v-for="(feature, index) in features" 
                             :key="index"
                             class="bg-base rounded-lg p-[2rem] text-center">
                            <i :class="feature.icon" class="text-[2.5rem] text-button mb-[1.5rem] [@media(max-width:550px)]:text-[2rem]"></i>
                            <h3 class="text-[1.5rem] text-text_dark mb-[1rem] [@media(max-width:550px)]:text-[1.25rem]">{{ feature.title }}</h3>
                            <p class="text-[1rem] text-text_light leading-[1.8] [@media(max-width:550px)]:text-[0.9rem]">{{ feature.description }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const { width } = useWindowSize();

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Static Data
const statistics = [
    {
        icon: 'fas fa-graduation-cap',
        value: '1.5K+',
        label: 'Online Courses'
    },
    {
        icon: 'fas fa-user-graduate',
        value: '100K+',
        label: 'Students'
    },
    {
        icon: 'fas fa-chalkboard-user',
        value: '1K+',
        label: 'Expert Tutors'
    }
];

const features = [
    {
        icon: 'fas fa-graduation-cap',
        title: 'Expert Tutors',
        description: 'Learn from industry professionals and experienced educators who are passionate about teaching.'
    },
    {
        icon: 'fas fa-video',
        title: 'Video Lectures',
        description: 'Access high-quality video content that makes complex concepts easy to understand.'
    },
    {
        icon: 'fas fa-certificate',
        title: 'Certificates',
        description: 'Earn recognized certificates upon completion of our courses to boost your career.'
    },
    {
        icon: 'fas fa-clock',
        title: 'Lifetime Access',
        description: 'Get unlimited access to course materials and learn at your own pace.'
    },
    {
        icon: 'fas fa-book',
        title: 'Rich Content',
        description: 'Access comprehensive study materials, quizzes, and practical assignments.'
    },
    {
        icon: 'fas fa-headset',
        title: '24/7 Support',
        description: 'Get help whenever you need it through our dedicated support system.'
    }
];
</script>