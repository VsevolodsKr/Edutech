<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <div class="flex justify-between items-center mb-[2rem]">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Pasniedzēju pārvaldība</h1>
                <button 
                    @click="openAddModal"
                    class="bg-button text-base px-[1.5rem] py-[.5rem] rounded-lg hover:bg-transparent hover:text-button border-2 border-button transition-all duration-200"
                >
                    Pievienot pasniedzēju
                </button>
            </div>

            <div class="mb-[2rem] flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Meklēt pasniedzējus..."
                        class="w-full p-[.8rem] rounded-lg bg-base text-text_dark border-2 border-base focus:border-button focus:outline-none"
                    >
                </div>
                <select 
                    v-model="statusFilter"
                    class="p-[.8rem] rounded-lg bg-base text-text_dark border-2 border-base focus:border-button focus:outline-none"
                >
                    <option value="">Visi statusi</option>
                    <option value="aktīvs">Aktīvs</option>
                    <option value="neaktīvs">Neaktīvs</option>
                </select>
            </div>

            <div class="bg-base rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-background">
                            <th class="p-[1rem] text-left text-text_dark">Vārds</th>
                            <th class="p-[1rem] text-left text-text_dark">E-pasts</th>
                            <th class="p-[1rem] text-left text-text_dark">Profesija</th>
                            <th class="p-[1rem] text-left text-text_dark">Statuss</th>
                            <th class="p-[1rem] text-left text-text_dark">Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="border-t-2 border-line">
                            <td class="p-[1rem] text-text_dark">{{ teacher.name }}</td>
                            <td class="p-[1rem] text-text_dark">{{ teacher.email }}</td>
                            <td class="p-[1rem] text-text_dark">{{ teacher.profession }}</td>
                            <td class="p-[1rem]">
                                <span 
                                    :class="[
                                        'px-[.8rem] py-[.3rem] rounded-full text-sm',
                                        teacher.status === 'aktīvs' ? 'bg-button2 text-base' : 'bg-button4 text-base'
                                    ]"
                                >
                                    {{ teacher.status === 'aktīvs' ? 'Aktīvs' : 'Neaktīvs' }}
                                </span>
                            </td>
                            <td class="p-[1rem]">
                                <div class="flex gap-2">
                                    <button 
                                        @click="editTeacher(teacher)"
                                        class="p-[.5rem] text-button3 hover:text-button transition-colors duration-200"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        @click="deleteTeacher(teacher)"
                                        class="p-[.5rem] text-button4 hover:text-button transition-colors duration-200"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredTeachers.length === 0">
                            <td colspan="4" class="p-[2rem] text-center text-text_light">
                                Nav atrasts neviens skolotājs
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-between items-center p-4 border-t-2 border-line">
                    <div class="text-text_dark">
                        Rādīt {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, teachers.length) }} no {{ teachers.length }}
                    </div>
                    <div class="flex gap-2">
                        <button 
                            @click="changePage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 rounded-lg border-2 border-line text-text_dark disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Iepriekšējā
                        </button>
                        <button 
                            v-for="page in totalPages" 
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'px-3 py-1 rounded-lg border-2',
                                currentPage === page 
                                    ? 'bg-button text-base border-button' 
                                    : 'border-line text-text_dark'
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button 
                            @click="changePage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="px-3 py-1 rounded-lg border-2 border-line text-text_dark disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Nākamā
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-base rounded-lg p-[2rem] w-[40rem] max-w-[90%]">
                <h2 class="text-[1.5rem] text-text_dark mb-[2rem]">
                    {{ editingTeacher ? 'Rediģēt skolotāju' : 'Pievienot jaunu skolotāju' }}
                </h2>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-text_dark mb-2">Vārds</label>
                        <input 
                            type="text" 
                            v-model="form.name"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">E-pasts</label>
                        <input 
                            type="email" 
                            v-model="form.email"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">Profesija</label>
                        <input 
                            type="text" 
                            v-model="form.profession"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">Statuss</label>
                        <select 
                            v-model="form.status"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                            required
                        >
                            <option value="aktīvs">Aktīvs</option>
                            <option value="neaktīvs">Neaktīvs</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">
                            {{ editingTeacher ? 'Mainīt paroli (neobligāts)' : 'Parole' }}
                        </label>
                        <input
                            v-if="!editingTeacher || showPasswordField"
                            type="password"
                            v-model="form.password"
                            :required="!editingTeacher"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                        >
                        <div v-if="editingTeacher" class="mt-2">
                            <label class="flex items-center text-text_dark">
                                <input
                                    type="checkbox"
                                    v-model="showPasswordField"
                                    class="mr-2"
                                >
                                Mainīt paroli
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">
                            {{ editingTeacher ? 'Mainīt attēlu (neobligāts)' : 'Attēls' }}
                        </label>
                        <input
                            type="file"
                            ref="imageInput"
                            @change="handleImageUpload"
                            accept="image/*"
                            :required="!editingTeacher"
                            class="w-full p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-base focus:border-button focus:outline-none"
                        >
                        <p v-if="editingTeacher" class="text-sm text-text_light mt-1">
                            Ja nevēlaties mainīt attēlu, atstājiet šo lauku tukšu
                        </p>
                    </div>
                    <div class="flex justify-end gap-4 mt-[2rem]">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="px-[1.5rem] py-[.5rem] rounded-lg border-2 border-line text-text_dark hover:bg-background transition-all duration-200"
                        >
                            Atcelt
                        </button>
                        <button 
                            type="submit"
                            class="px-[1.5rem] py-[.5rem] rounded-lg bg-button text-base hover:bg-transparent hover:text-button border-2 border-button transition-all duration-200"
                        >
                            {{ editingTeacher ? 'Saglabāt' : 'Pievienot' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <Developer_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Developer_Header from '../components/Developer_Header.vue';
import Developer_Sidebar from '../components/Developer_Sidebar.vue';
import store from '../store/store';
import Swal from 'sweetalert2';

const { width } = useWindowSize();

const teachers = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const editingTeacher = ref(null);
const showPasswordField = ref(false);
const currentPage = ref(1);
const itemsPerPage = 10;
const form = ref({
    name: '',
    email: '',
    password: '',
    profession: '',
    status: 'aktīvs',
    image: null
});

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-[var(--background)] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const filteredTeachers = computed(() => {
    const filtered = teachers.value.filter(teacher => {
        const matchesSearch = teacher.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            teacher.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || teacher.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });

    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return filtered.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    const filtered = teachers.value.filter(teacher => {
        const matchesSearch = teacher.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            teacher.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || teacher.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });
    return Math.ceil(filtered.length / itemsPerPage);
});

const changePage = (page) => {
    currentPage.value = page;
};

const loadTeachers = async () => {
    try {
        const response = await axios.get('/api/developer/teachers');
        teachers.value = response.data.data;
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās ielādēt skolotājus',
            icon: 'error'
        });
    }
};

const openAddModal = () => {
    editingTeacher.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        profession: '',
        status: 'aktīvs',
        image: null
    };
    showModal.value = true;
};

const editTeacher = (teacher) => {
    editingTeacher.value = teacher;
    form.value = {
        name: teacher.name,
        email: teacher.email,
        password: '',
        profession: teacher.profession,
        status: teacher.status,
        image: null
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingTeacher.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        profession: '',
        status: 'aktīvs',
        image: null
    };
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.image = file;
    }
};

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        formData.append('profession', form.value.profession);
        formData.append('status', form.value.status);
        
        if (editingTeacher.value) {
            if (showPasswordField.value && form.value.password) {
                formData.append('password', form.value.password);
            }
        } else {
            formData.append('password', form.value.password);
        }
        
        if (form.value.image) {
            formData.append('image', form.value.image);
        }

        if (editingTeacher.value) {
            formData.append('_method', 'PUT');
            const response = await axios.post(`/api/developer/teachers/${editingTeacher.value.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            if (response.data.status === 200) {
                await Swal.fire({
                    title: 'Veiksmīgi',
                    text: 'Skolotājs rediģēts!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        } else {
            const response = await axios.post('/api/developer/teachers', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            if (response.data.status === 201) {
                await Swal.fire({
                    title: 'Veiksmīgi',
                    text: 'Skolotājs pievienots',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
        closeModal();
        await loadTeachers();
    } catch (error) {
        console.error('Form submission error:', error);
        await Swal.fire({
            title: 'Kļūda',
            text: error.response?.data?.message || 'Kaut kas nogāja greizi',
            icon: 'error'
        });
    }
};

const deleteTeacher = async (teacher) => {
    try {
        const result = await Swal.fire({
            title: 'Vai tiešām vēlaties dzēst šo skolotāju?',
            text: 'Šī darbība dzēsīs arī visus pasniedzēja kursus un video. Šo darbību nevar atsaukt!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Jā, dzēst',
            cancelButtonText: 'Atcelt',
            confirmButtonColor: 'var(--button4)'
        });

        if (result.isConfirmed) {
            Swal.fire({
                title: 'Notiek dzēšana...',
                text: 'Lūdzu uzgaidiet, kamēr tiek dzēsti visi dati',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            await axios.delete(`/api/developer/teachers/${teacher.id}/playlists`);
            await axios.delete(`/api/developer/teachers/${teacher.id}`);
            
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Pasniedzējs un visi viņa dati ir dzēsti',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            
            await loadTeachers();
        }
    } catch (error) {
        console.error('Error deleting teacher:', error);
        await Swal.fire({
            title: 'Kļūda',
            text: error.response?.data?.message || 'Neizdevās dzēst pasniedzēju un viņa datus',
            icon: 'error'
        });
    }
};

onMounted(() => {
    loadTeachers();
});
</script> 