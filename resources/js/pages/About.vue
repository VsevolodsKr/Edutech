<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Par mums</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] items-center [@media(max-width:550px)]:grid-cols-1 [@media(max-width:550px)]:gap-[1rem]">
                    <div class="text-center">
                        <img src="../images/about-img.svg" alt="About Us" class="w-full h-[40rem] [@media(max-width:550px)]:h-[15rem]">
                    </div>
                    <div class="text-center [@media(max-width:550px)]:px-[.5rem]">
                        <h3 class="text-[1.75rem] text-text_dark mb-[2rem] [@media(max-width:550px)]:text-[1.25rem] [@media(max-width:550px)]:mb-[1rem]">Kāpēc mēs?</h3>
                        <p class="text-[1.1rem] text-text_light leading-[1.8] [@media(max-width:550px)]:text-[0.9rem] [@media(max-width:550px)]:leading-[1.6]">
                            Mēs esam paredzēti nodrošināt augstas kvalitātes izglītību caur mūsu inovatīvo tiešsaistes mācīšanās platformu. 
                            Mūsu kursi ir izstrādāti nozarēs ekspertiem un mācītājiem, lai nodrošinātu jums labāko iespējamo mācīšanās pieredzi.
                        </p>
                        <div class="mt-[2rem] grid grid-cols-[repeat(auto-fit,_minmax(16rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:grid-cols-1 [@media(max-width:550px)]:gap-[1rem] [@media(max-width:550px)]:mt-[1rem]">
                            <div v-for="(stat, index) in statistics" 
                                 :key="index"
                                 class="flex items-center gap-[1rem] h-[7rem] bg-base rounded-lg px-[1.2rem] [@media(max-width:550px)]:h-[5rem] [@media(max-width:550px)]:px-[1rem]">
                                <i :class="stat.icon" class="text-[2rem] text-button [@media(max-width:550px)]:text-[1.5rem]"></i>
                                <div class="text-left">
                                    <h3 class="text-[1.5rem] text-text_dark [@media(max-width:550px)]:text-[1.1rem]">{{ stat.value }}</h3>
                                    <p class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[0.8rem]">{{ stat.label }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-[3rem] [@media(max-width:550px)]:mt-[2rem]">
                    <h3 class="text-[1.75rem] text-text_dark text-center mb-[2rem] [@media(max-width:550px)]:text-[1.25rem] [@media(max-width:550px)]:mb-[1rem]">Mūsu priekšrocības</h3>
                    <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:grid-cols-1 [@media(max-width:550px)]:gap-[1rem]">
                        <div v-for="(feature, index) in features" 
                             :key="index"
                             class="bg-base rounded-lg p-[2rem] text-center [@media(max-width:550px)]:p-[1rem]">
                            <i :class="feature.icon" class="text-[2.5rem] text-button mb-[1.5rem] [@media(max-width:550px)]:text-[2rem] [@media(max-width:550px)]:mb-[1rem]"></i>
                            <h3 class="text-[1.5rem] text-text_dark mb-[1rem] [@media(max-width:550px)]:text-[1.1rem] [@media(max-width:550px)]:mb-[.5rem]">{{ feature.title }}</h3>
                            <p class="text-[1rem] text-text_light leading-[1.8] [@media(max-width:550px)]:text-[0.8rem] [@media(max-width:550px)]:leading-[1.6]">{{ feature.description }}</p>
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

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const statistics = [
    {
        icon: 'fas fa-graduation-cap',
        value: '1.5K+',
        label: 'Tiešsaistes kursi'
    },
    {
        icon: 'fas fa-user-graduate',
        value: '100K+',
        label: 'Studenti'
    },
    {
        icon: 'fas fa-chalkboard-user',
        value: '1K+',
        label: 'Eksperti'
    }
];

const features = [
    {
        icon: 'fas fa-graduation-cap',
        title: 'Eksperti',
        description: 'Mācīties no nozarēs ekspertiem un mācītājiem, kuri ir pārliecināti par mūsu kursu.'
    },
    {
        icon: 'fas fa-video',
        title: 'Video lekcijas',
        description: 'Piekļūstiet augstas kvalitātes video saturam, kas padara sarežģītus jēdzienus vieglāk saprast.'
    },
    {
        icon: 'fas fa-certificate',
        title: 'Sertifikāti',
        description: 'Iegūstiet sertifikātus pēc mūsu kursu pabeigšanas, lai palielinātu jūsu karjeru.'
    },
    {
        icon: 'fas fa-clock',
        title: 'Nelimitēts piekļūšanas laiks',
        description: 'Iegūstiet nelimitētu piekļūšanas laiku mūsu kursu materiāliem un mācīties jūsu paša laikā.'
    },
    {
        icon: 'fas fa-book',
        title: 'Bagāts materiālu klāsts',
        description: 'Piekļūstiet bagātam materiālu klāstam, kas palīdz jums labāk saprast un atgūt prasmes.'
    },
    {
        icon: 'fas fa-headset',
        title: '24/7 Palīdzība',
        description: 'Iegūstiet palību jebkurā laikā caur mūsu paredzēto palīdzības sistēmu.'
    }
];
</script>