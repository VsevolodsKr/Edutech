<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <div class="flex justify-between items-center mb-[2rem]">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Ziņojumu pārvaldība</h1>
            </div>

            <div class="mb-[2rem] flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Meklēt ziņojumus..."
                        class="w-full p-[.8rem] rounded-lg bg-base text-text_dark border-2 border-line focus:border-button focus:outline-none"
                    >
                </div>
                <select 
                    v-model="statusFilter"
                    class="p-[.8rem] rounded-lg bg-base text-text_dark border-2 border-line focus:border-button focus:outline-none"
                >
                    <option value="">Visi statusi</option>
                    <option value="jauns">Jauns</option>
                    <option value="atvērts">Atvērts</option>
                    <option value="apstrāde">Apstrāde</option>
                    <option value="pabeigts">Pabeigts</option>
                </select>
            </div>

            <div class="bg-base rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-background">
                            <th class="p-[1rem] text-left text-text_dark">Vārds</th>
                            <th class="p-[1rem] text-left text-text_dark">E-pasts</th>
                            <th class="p-[1rem] text-left text-text_dark">Tālrunis</th>
                            <th class="p-[1rem] text-left text-text_dark">Statuss</th>
                            <th class="p-[1rem] text-left text-text_dark">Datums</th>
                            <th class="p-[1rem] text-left text-text_dark">Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="message in filteredMessages" :key="message.id" class="border-t-2 border-line">
                            <td class="p-[1rem] text-text_dark">{{ message.name }}</td>
                            <td class="p-[1rem] text-text_dark">{{ message.email }}</td>
                            <td class="p-[1rem] text-text_dark">{{ message.number }}</td>
                            <td class="p-[1rem]">
                                <span 
                                    :class="[
                                        'px-[.8rem] py-[.3rem] rounded-full text-sm',
                                        message.status === 'jauns' ? 'bg-button4' :
                                        message.status === 'atvērts' ? 'bg-button3' :
                                        message.status === 'apstrāde' ? 'bg-button2' :
                                        'bg-button',
                                        'text-base'
                                    ]"
                                >
                                    {{ message.status === 'jauns' ? 'Jauns' :
                                       message.status === 'atvērts' ? 'Atvērts' :
                                       message.status === 'apstrāde' ? 'Apstrāde' : 'Pabeigts' }}
                                </span>
                            </td>
                            <td class="p-[1rem] text-text_dark">{{ formatDate(message.created_at) }}</td>
                            <td class="p-[1rem]">
                                <div class="flex gap-2">
                                    <button 
                                        @click="viewMessage(message)"
                                        class="p-[.5rem] text-button2 hover:text-button transition-colors duration-200"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                        @click="deleteMessage(message)"
                                        class="p-[.5rem] text-button4 hover:text-button transition-colors duration-200"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredMessages.length === 0">
                            <td colspan="6" class="p-[2rem] text-center text-text_light">
                                Nav atrasts neviens ziņojums
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-between items-center p-4 border-t-2 border-line">
                    <div class="text-text_dark">
                        Rādīt {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, messages.length) }} no {{ messages.length }}
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
                    Ziņojuma saturs
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-text_dark mb-2">Vārds</label>
                        <p class="p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line">
                            {{ selectedMessage?.name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">E-pasts</label>
                        <p class="p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line">
                            {{ selectedMessage?.email }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">Tālrunis</label>
                        <p class="p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line">
                            {{ selectedMessage?.number }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-text_dark mb-2">Ziņojums</label>
                        <p class="p-[.8rem] rounded-lg bg-background text-text_dark border-2 border-line min-h-[10rem] whitespace-pre-wrap">
                            {{ selectedMessage?.message }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-4 mt-[2rem]">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="px-[1.5rem] py-[.5rem] rounded-lg border-2 border-line text-text_dark hover:bg-background transition-all duration-200"
                        >
                            Aizvērt
                        </button>
                        <button 
                            v-if="selectedMessage?.status !== 'apstrāde'"
                            @click="updateStatus('apstrāde')"
                            class="px-[1.5rem] py-[.5rem] rounded-lg bg-button2 text-base hover:bg-transparent hover:text-button2 border-2 border-button2 transition-all duration-200"
                        >
                            Apstrādē
                        </button>
                        <button 
                            v-if="selectedMessage?.status !== 'pabeigts'"
                            @click="updateStatus('pabeigts')"
                            class="px-[1.5rem] py-[.5rem] rounded-lg bg-button text-base hover:bg-transparent hover:text-button border-2 border-button transition-all duration-200"
                        >
                            Pabeigts
                        </button>
                    </div>
                </div>
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

const messages = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const selectedMessage = ref(null);
const currentPage = ref(1);
const itemsPerPage = 10;

const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-[var(--background)] min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const filteredMessages = computed(() => {
    const filtered = messages.value.filter(message => {
        const matchesSearch = message.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.message.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || message.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });

    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return filtered.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    const filtered = messages.value.filter(message => {
        const matchesSearch = message.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            message.message.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !statusFilter.value || message.status === statusFilter.value;
        return matchesSearch && matchesStatus;
    });
    return Math.ceil(filtered.length / itemsPerPage);
});

const changePage = (page) => {
    currentPage.value = page;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('lv-LV');
};

const loadMessages = async () => {
    try {
        const response = await axios.get('/api/developer/messages');
        messages.value = response.data.data;
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās ielādēt ziņojumus',
            icon: 'error'
        });
    }
};

const viewMessage = async (message) => {
    try {
        const response = await axios.put(`/api/developer/messages/${message.id}/read`);
        if (response.data.status === 200) {
            message.status = 'atvērts';
            selectedMessage.value = message;
            showModal.value = true;
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Kļūda',
            text: 'Neizdevās atvērt ziņojumu'
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedMessage.value = null;
};

const updateStatus = async (newStatus) => {
    try {
        await axios.put(`/api/developer/messages/${selectedMessage.value.id}/status`, {
            status: newStatus
        });
        
        selectedMessage.value.status = newStatus;
        
        await Swal.fire({
            title: 'Veiksmīgi',
            text: 'Statuss atjaunināts',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        
        closeModal();
        await loadMessages();
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās atjaunināt statusu',
            icon: 'error'
        });
    }
};

const deleteMessage = async (message) => {
    try {
        const result = await Swal.fire({
            title: 'Vai tiešām vēlaties dzēst šo ziņojumu?',
            text: 'Šo darbību nevar atsaukt',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Jā, dzēst',
            cancelButtonText: 'Atcelt',
            confirmButtonColor: 'var(--button4)'
        });

        if (result.isConfirmed) {
            await axios.delete(`/api/developer/messages/${message.id}`);
            await Swal.fire({
                title: 'Veiksmīgi',
                text: 'Ziņojums dzēsts',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            await loadMessages();
        }
    } catch (error) {
        await Swal.fire({
            title: 'Kļūda',
            text: 'Neizdevās dzēst ziņojumu',
            icon: 'error'
        });
    }
};

onMounted(() => {
    loadMessages();
});
</script> 