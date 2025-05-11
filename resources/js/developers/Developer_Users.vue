<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <div class="flex justify-between items-center mb-[2rem]">
                <h1 class="text-[1.5rem] text-[var(--text_dark)] capitalize">Lietotāju pārvaldība</h1>
                <button 
                    @click="openAddModal"
                    class="bg-[var(--button)] text-[var(--text_dark)] px-[1.5rem] py-[.5rem] rounded-lg hover:bg-transparent hover:text-[var(--button)] border-2 border-[var(--button)] transition-all duration-200"
                >
                    Pievienot lietotāju
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="mb-[2rem] flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Meklēt lietotājus..."
                        class="w-full p-[.8rem] rounded-lg bg-[var(--base)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                    >
                </div>
                <select 
                    v-model="statusFilter"
                    class="p-[.8rem] rounded-lg bg-[var(--base)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                >
                    <option value="">Visi statusi</option>
                    <option value="aktīvs">Aktīvs</option>
                    <option value="neaktīvs">Neaktīvs</option>
                </select>
            </div>

            <!-- Users Table -->
            <div class="bg-[var(--base)] rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--background)]">
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Vārds</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">E-pasts</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Statuss</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Reģistrācijas datums</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in filteredUsers" :key="user.id" class="border-t-2 border-[var(--border-color)]">
                            <td class="p-[1rem] text-[var(--text_dark)]">{{ user.name }}</td>
                            <td class="p-[1rem] text-[var(--text_dark)]">{{ user.email }}</td>
                            <td class="p-[1rem]">
                                <span 
                                    :class="[
                                        'px-[.8rem] py-[.3rem] rounded-full text-sm',
                                        user.status === 'aktīvs' ? 'bg-[var(--button2)] text-[var(--text_dark)]' : 'bg-[var(--button4)] text-[var(--text_dark)]'
                                    ]"
                                >
                                    {{ user.status === 'aktīvs' ? 'Aktīvs' : 'Neaktīvs' }}
                                </span>
                            </td>
                            <td class="p-[1rem] text-[var(--text_dark)]">{{ formatDate(user.created_at) }}</td>
                            <td class="p-[1rem]">
                                <div class="flex gap-2">
                                    <button 
                                        @click="editUser(user)"
                                        class="p-[.5rem] text-[var(--button3)] hover:text-[var(--button)] transition-colors duration-200"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        @click="deleteUser(user)"
                                        class="p-[.5rem] text-[var(--button4)] hover:text-[var(--button)] transition-colors duration-200"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredUsers.length === 0">
                            <td colspan="5" class="p-[2rem] text-center text-[var(--text_light)]">
                                Nav atrasts neviens lietotājs
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-[var(--base)] rounded-lg p-[2rem] w-[40rem] max-w-[90%]">
                <h2 class="text-[1.5rem] text-[var(--text_dark)] mb-[2rem]">
                    {{ editingUser ? 'Rediģēt lietotāju' : 'Pievienot jaunu lietotāju' }}
                </h2>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-[var(--text_dark)] mb-2">Vārds</label>
                        <input 
                            type="text" 
                            v-model="form.name"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-[var(--text_dark)] mb-2">E-pasts</label>
                        <input 
                            type="email" 
                            v-model="form.email"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-[var(--text_dark)] mb-2">
                            {{ editingUser ? 'Mainīt paroli (neobligāts)' : 'Parole' }}
                        </label>
                        <input
                            v-if="!editingUser || showPasswordField"
                            type="password"
                            v-model="form.password"
                            :required="!editingUser"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                        >
                        <div v-if="editingUser" class="mt-2">
                            <label class="flex items-center text-[var(--text_dark)]">
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
                        <label class="block text-[var(--text_dark)] mb-2">
                            {{ editingUser ? 'Mainīt attēlu (neobligāts)' : 'Attēls' }}
                        </label>
                        <input
                            type="file"
                            ref="imageInput"
                            @change="handleImageUpload"
                            accept="image/*"
                            :required="!editingUser"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                        >
                        <p v-if="editingUser" class="text-sm text-[var(--text_light)] mt-1">
                            Ja nevēlaties mainīt attēlu, atstājiet šo lauku tukšu
                        </p>
                    </div>
                    <div>
                        <label class="block text-[var(--text_dark)] mb-2">Statuss</label>
                        <select 
                            v-model="form.status"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
                            <option value="aktīvs">Aktīvs</option>
                            <option value="neaktīvs">Neaktīvs</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-4 mt-[2rem]">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="px-[1.5rem] py-[.5rem] rounded-lg border-2 border-[var(--border-color)] text-[var(--text_dark)] hover:bg-[var(--background)] transition-all duration-200"
                        >
                            Atcelt
                        </button>
                        <button 
                            type="submit"
                            class="px-[1.5rem] py-[.5rem] rounded-lg bg-[var(--button)] text-[var(--text_dark)] hover:bg-transparent hover:text-[var(--button)] border-2 border-[var(--button)] transition-all duration-200"
                        >
                            {{ editingUser ? 'Saglabāt' : 'Pievienot' }}
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

const users = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const editingUser = ref(null);
const showPasswordField = ref(false);
const form = ref({
    name: '',
    email: '',
    password: '',
    status: 'aktīvs',
    image: null
});

const imageInput = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-[var(--background)] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const filteredUsers = computed(() => {
    return users.value.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || user.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('lv-LV');
};

const loadUsers = async () => {
    try {
        const response = await axios.get('/api/developer/users');
        users.value = response.data.data;
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās ielādēt lietotājus',
            icon: 'error'
        });
    }
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.image = file;
    }
};

const openAddModal = () => {
    editingUser.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        status: 'aktīvs',
        image: null
    };
    showModal.value = true;
};

const editUser = (user) => {
    editingUser.value = user;
    form.value = {
        name: user.name,
        email: user.email,
        password: '',
        status: user.status,
        image: null
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingUser.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        status: 'aktīvs',
        image: null
    };
    if (imageInput.value) {
        imageInput.value.value = '';
    }
};

const handleSubmit = async () => {
    try {
        console.log('Form data before submission:', form.value);
        
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        formData.append('status', form.value.status);
        
        if (showPasswordField.value && form.value.password) {
            formData.append('password', form.value.password);
        } else if (!editingUser.value) {
            formData.append('password', form.value.password);
        }
        
        if (form.value.image) {
            formData.append('image', form.value.image);
        }

        let response;
        if (editingUser.value) {
            // For PUT requests, we need to use _method=PUT with POST
            formData.append('_method', 'PUT');
            console.log('Sending update request for user:', editingUser.value.id);
            console.log('Form data being sent:', Object.fromEntries(formData));
            
            response = await axios.post(`/api/developer/users/${editingUser.value.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json'
                }
            });
        } else {
            console.log('Sending create request');
            console.log('Form data being sent:', Object.fromEntries(formData));
            
            response = await axios.post('/api/developer/users', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json'
                }
            });
        }

        console.log('Response received:', response.data);

        if (response.data.status === 200 || response.data.status === 201) {
            await Swal.fire({
                title: 'Veiksmīgi',
                text: editingUser.value ? 'Lietotājs rediģēts!' : 'Lietotājs pievienots',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            closeModal();
            await loadUsers();
        }
    } catch (error) {
        console.error('Form submission error:', error);
        console.error('Error response:', error.response?.data);
        
        const errorMessage = error.response?.data?.message || 'Kaut kas nogāja greizi';
        const errorDetails = error.response?.data?.errors;
        
        await Swal.fire({
            title: 'Kļūda',
            text: errorMessage,
            html: errorDetails ? 
                `<div class="text-left">
                    ${Object.entries(errorDetails).map(([field, messages]) => 
                        `<p class="mb-2"><strong>${field}:</strong> ${messages.join(', ')}</p>`
                    ).join('')}
                </div>` : 
                errorMessage,
            icon: 'error'
        });
    }
};

const deleteUser = async (user) => {
    try {
        const result = await Swal.fire({
            title: 'Vai tiešām vēlaties dzēst šo lietotāju?',
            text: 'Šo darbību nevar atsaukt',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Jā, dzēst',
            cancelButtonText: 'Atcelt',
            confirmButtonColor: 'var(--button4)'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/developer/users/${user.id}`);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Lietotājs dzēsts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            await loadUsers();
        }
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās dzēst lietotāju',
            icon: 'error'
        });
    }
};

onMounted(() => {
    loadUsers();
});
</script> 