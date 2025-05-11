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
                    v-model="roleFilter"
                    class="p-[.8rem] rounded-lg bg-[var(--base)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                >
                    <option value="">Visas lomas</option>
                    <option value="admin">Administrators</option>
                    <option value="teacher">Skolotājs</option>
                    <option value="student">Skolnieks</option>
                </select>
            </div>

            <!-- Users Table -->
            <div class="bg-[var(--base)] rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--background)]">
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Vārds</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">E-pasts</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Loma</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Reģistrējies</th>
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
                                        user.role === 'admin' ? 'bg-[var(--button4)]' :
                                        user.role === 'teacher' ? 'bg-[var(--button3)]' :
                                        'bg-[var(--button2)]',
                                        'text-[var(--text_dark)]'
                                    ]"
                                >
                                    {{ user.role === 'admin' ? 'Administrators' :
                                       user.role === 'teacher' ? 'Skolotājs' : 'Skolnieks' }}
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
                        <label class="block text-[var(--text_dark)] mb-2">Loma</label>
                        <select 
                            v-model="form.role"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
                            <option value="admin">Administrators</option>
                            <option value="teacher">Skolotājs</option>
                            <option value="student">Skolnieks</option>
                        </select>
                    </div>
                    <div v-if="!editingUser">
                        <label class="block text-[var(--text_dark)] mb-2">Parole</label>
                        <input 
                            type="password" 
                            v-model="form.password"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
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
const roleFilter = ref('');
const showModal = ref(false);
const editingUser = ref(null);
const form = ref({
    name: '',
    email: '',
    role: 'student',
    password: ''
});

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
        const matchesRole = !roleFilter.value || user.role === roleFilter.value;
        return matchesSearch && matchesRole;
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

const openAddModal = () => {
    editingUser.value = null;
    form.value = {
        name: '',
        email: '',
        role: 'student',
        password: ''
    };
    showModal.value = true;
};

const editUser = (user) => {
    editingUser.value = user;
    form.value = { ...user };
    delete form.value.password; // Don't send password when editing
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingUser.value = null;
    form.value = {
        name: '',
        email: '',
        role: 'student',
        password: ''
    };
};

const handleSubmit = async () => {
    try {
        if (editingUser.value) {
            await axios.put(`/api/developer/users/${editingUser.value.id}`, form.value);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Lietotājs atjaunināts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            await axios.post('/api/developer/users', form.value);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Lietotājs pievienots',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
        closeModal();
        await loadUsers();
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: error.response?.data?.message || 'Kaut kas nogāja greizi',
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