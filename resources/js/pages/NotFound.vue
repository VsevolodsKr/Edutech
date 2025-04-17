<template>
    <div>
        <Header />
        <section :class="sectionClasses">
            <div class="bg-base rounded-lg p-[2rem] w-[50rem] max-w-[95%] text-center">
                <div class="text-[8rem] text-button font-bold [@media(max-width:550px)]:text-[4rem]">
                    404
                </div>
                <h1 class="text-[2rem] text-text_dark mb-4 [@media(max-width:550px)]:text-[1.5rem]">
                   Lapa netika atrasta
                </h1>
                <p class="text-text_light text-[1.2rem] mb-8 [@media(max-width:550px)]:text-[1rem]">
                    Lapa, kuru meklējat, varēja tikt noņemta, tā nosaukums ir mainīts, vai arī ir tīmējusi.
                </p>
                <router-link 
                    to="/" 
                    class="bg-button text-base inline-block border-2 border-button rounded-lg py-[.8rem] px-[2rem] transition-all duration-200 hover:bg-transparent hover:text-button [@media(max-width:550px)]:text-[.9rem] [@media(max-width:550px)]:py-[.5rem]"
                >
                    Atpakaļ uz sākumlapu
                </router-link>
            </div>
        </section>
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

const showSidebar = computed(() => store.getters.getShowSidebar);

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[2rem] bg-background min-h-[calc(100vh-4rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);
</script> 