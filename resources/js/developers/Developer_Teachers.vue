<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <div class="flex justify-between items-center mb-[2rem]">
                <h1 class="text-[1.5rem] text-[var(--text_dark)] capitalize">Skolotāju pārvaldība</h1>
                <button 
                    @click="openAddModal"
                    class="bg-[var(--button)] text-[var(--text_dark)] px-[1.5rem] py-[.5rem] rounded-lg hover:bg-transparent hover:text-[var(--button)] border-2 border-[var(--button)] transition-all duration-200"
                >
                    Pievienot skolotāju
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="mb-[2rem] flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Meklēt skolotājus..."
                        class="w-full p-[.8rem] rounded-lg bg-[var(--base)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                    >
                </div>
                <select 
                    v-model="statusFilter"
                    class="p-[.8rem] rounded-lg bg-[var(--base)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                >
                    <option value="">Visi statusi</option>
                    <option value="active">Aktīvs</option>
                    <option value="inactive">Neaktīvs</option>
                </select>
            </div>

            <!-- Teachers Table -->
            <div class="bg-[var(--base)] rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--background)]">
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Vārds</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">E-pasts</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Statuss</th>
                            <th class="p-[1rem] text-left text-[var(--text_dark)]">Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="border-t-2 border-[var(--border-color)]">
                            <td class="p-[1rem] text-[var(--text_dark)]">{{ teacher.name }}</td>
                            <td class="p-[1rem] text-[var(--text_dark)]">{{ teacher.email }}</td>
                            <td class="p-[1rem]">
                                <span 
                                    :class="[
                                        'px-[.8rem] py-[.3rem] rounded-full text-sm',
                                        teacher.status === 'active' ? 'bg-[var(--button2)] text-[var(--text_dark)]' : 'bg-[var(--button4)] text-[var(--text_dark)]'
                                    ]"
                                >
                                    {{ teacher.status === 'active' ? 'Aktīvs' : 'Neaktīvs' }}
                                </span>
                            </td>
                            <td class="p-[1rem]">
                                <div class="flex gap-2">
                                    <button 
                                        @click="editTeacher(teacher)"
                                        class="p-[.5rem] text-[var(--button3)] hover:text-[var(--button)] transition-colors duration-200"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        @click="deleteTeacher(teacher)"
                                        class="p-[.5rem] text-[var(--button4)] hover:text-[var(--button)] transition-colors duration-200"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredTeachers.length === 0">
                            <td colspan="4" class="p-[2rem] text-center text-[var(--text_light)]">
                                Nav atrasts neviens skolotājs
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
                    {{ editingTeacher ? 'Rediģēt skolotāju' : 'Pievienot jaunu skolotāju' }}
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
                        <label class="block text-[var(--text_dark)] mb-2">Statuss</label>
                        <select 
                            v-model="form.status"
                            class="w-full p-[.8rem] rounded-lg bg-[var(--background)] text-[var(--text_dark)] border-2 border-[var(--border-color)] focus:border-[var(--button)] focus:outline-none"
                            required
                        >
                            <option value="active">Aktīvs</option>
                            <option value="inactive">Neaktīvs</option>
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
const form = ref({
    name: '',
    email: '',
    status: 'active'
});

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-[var(--background)] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const filteredTeachers = computed(() => {
    return teachers.value.filter(teacher => {
        const matchesSearch = teacher.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            teacher.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || teacher.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });
});

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
        status: 'active'
    };
    showModal.value = true;
};

const editTeacher = (teacher) => {
    editingTeacher.value = teacher;
    form.value = { ...teacher };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingTeacher.value = null;
    form.value = {
        name: '',
        email: '',
        status: 'active'
    };
};

const handleSubmit = async () => {
    try {
        if (editingTeacher.value) {
            await axios.put(`/api/developer/teachers/${editingTeacher.value.id}`, form.value);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Skolotājs atjaunināts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            await axios.post('/api/developer/teachers', form.value);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Skolotājs pievienots',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
        closeModal();
        await loadTeachers();
    } catch (error) {
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
            text: 'Šo darbību nevar atsaukt',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Jā, dzēst',
            cancelButtonText: 'Atcelt',
            confirmButtonColor: 'var(--button4)'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/developer/teachers/${teacher.id}`);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Skolotājs dzēsts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            await loadTeachers();
        }
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās dzēst skolotāju',
            icon: 'error'
        });
    }
};

onMounted(() => {
    loadTeachers();
});
</script> 