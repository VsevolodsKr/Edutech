<template>
    <Preloader />
    <Admin_Header />
    <section :class="[(showSidebar == true && width > 1180) ? 'pl-[22rem]' : (showSidebar == false || (showSidebar == true && width < 1180)) ? 'pl-[2rem]' : '', 'pt-[2rem] pr-[2rem] bg-background min-h-[calc(127.5vh-20rem)] flex items-center justify-center [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]']">
        <div class="bg-base rounded-lg p-[1rem] w-[50rem]">
            <h3 class="text-[1.5rem] capitalize text-text_dark text-center [@media(max-width:550px)]:text-[1.2rem]">Update Profile</h3>
            <div v-if="errorList.length" 
                 :class="[status === 500 ? 'bg-[#fcb6b6] text-[#912020]' : 'bg-[#b6f5b5] text-[#378c35]', 'rounded-xl mb-4']">
                <p v-for="(error, index) in errorList" 
                   :key="index" 
                   class="py-[.5rem] pl-[.5rem] w-full [@media(max-width:550px)]:text-[.7rem]">
                    <i :class="[status === 500 ? 'fa fa-warning' : 'fa fa-check']"></i> {{ error }}
                </p>
            </div>
            <div class="flex gap-[2rem] [@media(max-width:768px)]:flex-col">
                <div class="flex-[1_1_25rem]">
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            Update name
                        </label>
                        <input 
                            v-model="formData.name" 
                            type="text" 
                            :placeholder="userData?.name"
                            required 
                            maxlength="50" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            Update profession
                        </label>
                        <select 
                            v-model="formData.profession" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                            <option value="">Select your profession</option>
                            <option v-for="profession in professions" 
                                    :key="profession.value" 
                                    :value="profession.value">
                                {{ profession.label }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            Update email
                        </label>
                        <input 
                            v-model="formData.email" 
                            type="email" 
                            :placeholder="userData?.email"
                            required 
                            maxlength="50" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>
                </div>
                <div class="flex-[1_1_25rem]">
                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            Previous password
                        </label>
                        <input 
                            v-model="formData.old_password" 
                            type="password" 
                            placeholder="Enter your old password..." 
                            maxlength="50" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            New password
                        </label>
                        <input 
                            v-model="formData.new_password" 
                            type="password" 
                            placeholder="Enter your new password..." 
                            maxlength="50" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                            Confirm password
                        </label>
                        <input 
                            v-model="formData.confirm_password" 
                            type="password" 
                            placeholder="Confirm your new password..." 
                            maxlength="50" 
                            class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                        >
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="text-[1.2rem] text-text_light [@media(max-width:550px)]:text-[.9rem]">
                    Select image
                </label>
                <input 
                    ref="fileInput"
                    type="file" 
                    accept="image/*"
                    @change="handleFileChange"
                    class="mt-2 text-[1rem] text-text_light rounded-lg p-[.5rem] bg-background w-full outline-none focus:outline-none [@media(max-width:550px)]:text-[.7rem]"
                >
            </div>
            <button 
                @click="handleSubmit" 
                :disabled="isSubmitting"
                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
            >
                {{ isSubmitting ? 'Updating...' : 'Update Profile' }}
            </button>
        </div>
    </section>
    <Admin_Sidebar />
</template>

<script>
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';
import { useWindowSize } from '@vueuse/core';
import Preloader from '../components/Preloader.vue';

const {width} = useWindowSize()
export default {
    data(){
        return{
            width,
            teacher: null,
            name: '',
            profession: '',
            email: '',
            p_password: '',
            n_password: '',
            c_password: '',
            errorList: '',
            status: '',
            userData: null,
            formData: {
                name: '',
                profession: '',
                email: '',
                old_password: '',
                new_password: '',
                confirm_password: '',
                image: null
            },
            isSubmitting: false,
            fileInput: null,
            professions: [
                { value: 'english', label: 'English teacher' },
                { value: 'math', label: 'Math teacher' },
                { value: 'art', label: 'Art teacher' },
                { value: 'science', label: 'Science teacher' },
                { value: 'history', label: 'History teacher' },
                { value: 'music', label: 'Music teacher' },
                { value: 'geography', label: 'Geography teacher' },
                { value: 'PE', label: 'Physical education (PE) teacher' },
                { value: 'biology', label: 'Biology teacher' },
                { value: 'chemistry', label: 'Chemistry teacher' },
                { value: 'physics', label: 'Physics teacher' },
                { value: 'IT', label: 'Information technology (IT) teacher' },
                { value: 'social', label: 'Social studies teacher' },
                { value: 'technology', label: 'Technology teacher' },
                { value: 'philosophy', label: 'Philosophy teacher' },
                { value: 'design', label: 'Design teacher' },
                { value: 'literature', label: 'Literature teacher' },
                { value: 'algebra', label: 'Algebra teacher' },
                { value: 'geometry', label: 'Geometry teacher' }
            ]
        }
    },
    components: {
        Admin_Header,
        Admin_Sidebar,
        Preloader,
    },
    computed: {
        showSidebar: function (){
            return store.getters.getShowSidebar
        }
    },
    mounted(){
        this.loadUserData()
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },
    created(){
        this.loadUserData()
        if(localStorage.getItem('token') == ''){
            this.$router.push('/').then(() =>{this.$router.go(0)})
        }
    },
    methods: {
        async loadUserData() {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    this.$router.push('/');
                    return;
                }

                const response = await axios.get('/api/user', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                this.userData = response.data;
                this.formData.name = response.data.name;
                this.formData.email = response.data.email;
                this.formData.profession = response.data.profession;
            } catch (error) {
                console.error('Error loading user data:', error);
                if (error.response?.status === 401) {
                    this.$router.push('/');
                }
            }
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    this.errorList = ['Image size should not exceed 2MB'];
                    event.target.value = '';
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                    this.errorList = ['Please select a valid image file (JPEG, PNG, or GIF)'];
                    event.target.value = '';
                    return;
                }

                this.formData.image = file;
            }
        },
        async handleSubmit(e) {
            if(e && e.preventDefault){
                e.preventDefault()
            }
            this.isSubmitting = true;
            this.errorList = [];
            try {
                // Validate passwords match if new password is being set
                if (this.formData.new_password && this.formData.new_password !== this.formData.confirm_password) {
                    this.errorList = ['New password and confirmation do not match'];
                    this.status = 500;
                    return;
                }

                const data = new FormData();
                Object.entries(this.formData).forEach(([key, value]) => {
                    if (value !== null && value !== '') {
                        data.append(key, value);
                    }
                });

                const response = await axios.post('/api/admin/update-profile', data, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.errorList = [response.data.message];
                this.status = response.data.status;

                if (this.status !== 500) {
                    await Swal.fire({
                        title: 'Success!',
                        text: 'Your profile has been updated successfully',
                        icon: 'success'
                    });

                    // Reset form
                    this.formData.old_password = '';
                    this.formData.new_password = '';
                    this.formData.confirm_password = '';
                    this.formData.image = null;
                    if (this.fileInput) this.fileInput.value = '';

                    // Reload user data
                    await this.loadUserData();
                    
                    // Redirect to dashboard
                    this.$router.push('/dashboard');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                this.errorList = error.response?.data?.message || ['An error occurred while updating your profile'];
                this.status = 500;
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>