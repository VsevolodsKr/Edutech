<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Sazināties ar mums</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

                <div class="flex flex-wrap items-center justify-between mb-[2rem] [@media(max-width:1024px)]:justify-center [@media(max-width:1024px)]:gap-[2rem]">
                    <div class="w-[50%] pr-[1rem] [@media(max-width:1024px)]:w-full [@media(max-width:1024px)]:pr-0">
                        <img 
                            src="../images/contact-img.svg" 
                            alt="Contact Us"
                            class="w-full h-auto"
                        >
                    </div>

                    <form @submit.prevent="handleSubmit" class="w-[50%] bg-base rounded-lg p-[2rem] [@media(max-width:1024px)]:w-full">
                        <div v-if="errorList.length" class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]">
                            <p v-for="(error, index) in errorList" 
                               :key="index" 
                               class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Jūsu vārds <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.name"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="Ievadiet savu vārdu..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Jūsu e-pasts <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.email"
                                type="email"
                                required
                                maxlength="50"
                                placeholder="Ievadiet savu e-pastu..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Jūsu tālruņa numurs <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.number"
                                type="tel"
                                required
                                maxlength="15"
                                placeholder="Ievadiet savu tālruņa numuru..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Jūsu ziņa <span class="text-button4">*</span>
                            </label>
                            <textarea 
                                v-model="formData.message"
                                required
                                maxlength="1000"
                                placeholder="Ievadiet savu ziņu..."
                                rows="5"
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none resize-none [@media(max-width:550px)]:text-[.7rem]"
                            ></textarea>
                        </div>

                        <button 
                            type="submit"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Nosūtīt ziņu
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,_minmax(20rem,_1fr))] gap-[1.5rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col">
                    <div class="bg-base rounded-lg p-[2rem] flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-300">
                        <i class="fas fa-envelope text-[2.5rem] text-button mb-4"></i>
                        <h3 class="text-[1.3rem] text-text_dark mb-2">E-pasts</h3>
                        <a href="mailto:info@edutech.com" class="text-text_light">info@edutech.com</a>
                        <a href="mailto:support@edutech.com" class="text-text_light">support@edutech.com</a>
                    </div>

                    <div class="bg-base rounded-lg p-[2rem] flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-300">
                        <i class="fas fa-phone text-[2.5rem] text-button mb-4"></i>
                        <h3 class="text-[1.3rem] text-text_dark mb-2">Tālruņa numurs</h3>
                        <a href="tel:+37112345678" class="text-text_light">+371 12345678</a>
                        <a href="tel:+37187654321" class="text-text_light">+371 87654321</a>
                    </div>

                    <div class="bg-base rounded-lg p-[2rem] flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-300">
                        <i class="fas fa-map-marker-alt text-[2.5rem] text-button mb-4"></i>
                        <h3 class="text-[1.3rem] text-text_dark mb-2">Ofisa adrese</h3>
                        <a href="https://www.google.com/maps/place/%D0%A0%D0%B8%D0%B3%D0%B0/@56.9716492,23.9642685,11z/data=!3m1!4b1!4m6!3m5!1s0x46eecfb0e5073ded:0x400cfcd68f2fe30!8m2!3d56.9676941!4d24.1056221!16zL20vMDZjbjU?entry=ttu&g_ep=EgoyMDI1MDQxNC4xIKXMDSoASAFQAw%3D%3D" class="text-text_light" target="_blank">Rīga, Latvija</a>
                        <p class="text-text_light">LV-1050</p>
                    </div>
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Swal from 'sweetalert2';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const { width } = useWindowSize();

const formData = ref({
    name: '',
    email: '',
    number: '',
    message: ''
});
const errorList = ref([]);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const handleSubmit = async () => {
    try {
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        const result = await Swal.fire({
            title: "Vai esat pārliecināts?",
            text: "Pārbaudiet, vai jūsu ziņa ir pareiza",
            icon: "warning",
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: button4,
            confirmButtonText: "Jā, nosūtīt!",
            cancelButtonText: "Atcelt!"
        });

        if (result.isConfirmed) {
            const data = new FormData();
            Object.entries(formData.value).forEach(([key, value]) => {
                data.append(key, value);
            });

            await axios.post('/api/contact/send', data);
            
            await Swal.fire({
                title: "Jūsu ziņa ir veiksmīgi nosūtīta!",
                text: "Mēs darīsim visu iespējamo, lai Edutech būtu labāka vieta, kur mācīties!",
                icon: "success",
            });

            formData.value = {
                name: '',
                email: '',
                number: '',
                message: ''
            };
            errorList.value = [];
        }
    } catch (error) {
        errorList.value = error.response?.data?.message || ['Radās kļūda, nosūtot ziņu'];
    }
};
</script>