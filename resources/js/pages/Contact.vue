<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Contact Us</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                <div class="flex items-center justify-center">
                    <form @submit.prevent="handleSubmit" class="bg-base rounded-lg p-[2rem] w-[50rem]">
                        <div v-if="errorList.length" class="bg-[#fcb6b6] text-[#912020] rounded-xl mb-[1rem]">
                            <p v-for="(error, index) in errorList" 
                               :key="index" 
                               class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                                <i class="fa fa-warning"></i> {{ error }}
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Your name <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.name"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="Enter your name..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Your email <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.email"
                                type="email"
                                required
                                maxlength="50"
                                placeholder="Enter your email..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Your phone number <span class="text-button4">*</span>
                            </label>
                            <input 
                                v-model="formData.number"
                                type="tel"
                                required
                                maxlength="15"
                                placeholder="Enter your phone number..."
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                            >
                        </div>

                        <div class="mb-4">
                            <label class="text-[1.2rem] text-text_dark [@media(max-width:550px)]:text-[.9rem]">
                                Your message <span class="text-button4">*</span>
                            </label>
                            <textarea 
                                v-model="formData.message"
                                required
                                maxlength="1000"
                                placeholder="Enter your message..."
                                rows="5"
                                class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none resize-none [@media(max-width:550px)]:text-[.7rem]"
                            ></textarea>
                        </div>

                        <button 
                            type="submit"
                            class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                        >
                            Send Message
                        </button>
                    </form>
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

// State
const formData = ref({
    name: '',
    email: '',
    number: '',
    message: ''
});
const errorList = ref([]);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const handleSubmit = async () => {
    try {
        // Get computed styles for the confirmation dialog
        const background = getComputedStyle(document.documentElement).getPropertyValue('--background');
        const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark');
        const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4');

        // Show confirmation dialog
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "Check, is everything correct in your message form",
            icon: "warning",
            color: text_dark,
            background: background,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: button4,
            confirmButtonText: "Yes, send it!"
        });

        if (result.isConfirmed) {
            const data = new FormData();
            Object.entries(formData.value).forEach(([key, value]) => {
                data.append(key, value);
            });

            await axios.post('/api/contact/send', data);
            
            // Show success message
            await Swal.fire({
                title: "Your message successfully sent to Edutech!",
                text: "We will do our best to make Edutech better place to study!",
                icon: "success",
            });

            // Reset form
            formData.value = {
                name: '',
                email: '',
                number: '',
                message: ''
            };
            errorList.value = [];
        }
    } catch (error) {
        errorList.value = error.response?.data?.message || ['An error occurred while sending your message'];
    }
};
</script>