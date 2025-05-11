<template>
    <div class="min-h-screen bg-[var(--background)]">
        <Developer_Header />
        <Developer_Sidebar />

        <main class="pt-[5rem] [@media(max-width:550px)]:pt-[4rem]">
            <div class="p-[2rem] [@media(max-width:550px)]:p-[1rem]">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[2rem]">
                    <!-- Teachers Card -->
                    <div class="bg-[var(--base)] p-[2rem] rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-[1rem]">
                            <h3 class="text-[1.5rem] text-[var(--text_dark)]">Skolotāji</h3>
                            <i class="fa-solid fa-chalkboard-user text-[2rem] text-[var(--button)]"></i>
                        </div>
                        <p class="text-[2rem] text-[var(--text_dark)] font-bold mb-[1rem]">{{ stats.teachers }}</p>
                        <router-link 
                            to="/developer_teachers"
                            class="text-[var(--button)] hover:text-[var(--button2)] transition-colors duration-200"
                        >
                            Skatīt visus →
                        </router-link>
                    </div>

                    <!-- Users Card -->
                    <div class="bg-[var(--base)] p-[2rem] rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-[1rem]">
                            <h3 class="text-[1.5rem] text-[var(--text_dark)]">Lietotāji</h3>
                            <i class="fa-solid fa-users text-[2rem] text-[var(--button2)]"></i>
                        </div>
                        <p class="text-[2rem] text-[var(--text_dark)] font-bold mb-[1rem]">{{ stats.users }}</p>
                        <router-link 
                            to="/developer_users"
                            class="text-[var(--button2)] hover:text-[var(--button)] transition-colors duration-200"
                        >
                            Skatīt visus →
                        </router-link>
                    </div>

                    <!-- Messages Card -->
                    <div class="bg-[var(--base)] p-[2rem] rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-[1rem]">
                            <h3 class="text-[1.5rem] text-[var(--text_dark)]">Ziņojumi</h3>
                            <i class="fa-solid fa-envelope text-[2rem] text-[var(--button3)]"></i>
                        </div>
                        <p class="text-[2rem] text-[var(--text_dark)] font-bold mb-[1rem]">{{ stats.messages }}</p>
                        <router-link 
                            to="/developer_messages"
                            class="text-[var(--button3)] hover:text-[var(--button)] transition-colors duration-200"
                        >
                            Skatīt visus →
                        </router-link>
                    </div>

                    <!-- Unread Messages Card -->
                    <div class="bg-[var(--base)] p-[2rem] rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-[1rem]">
                            <h3 class="text-[1.5rem] text-[var(--text_dark)]">Nelasīti</h3>
                            <i class="fa-solid fa-envelope-open text-[2rem] text-[var(--button4)]"></i>
                        </div>
                        <p class="text-[2rem] text-[var(--text_dark)] font-bold mb-[1rem]">{{ stats.unreadMessages }}</p>
                        <router-link 
                            to="/developer_messages"
                            class="text-[var(--button4)] hover:text-[var(--button)] transition-colors duration-200"
                        >
                            Skatīt visus →
                        </router-link>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-[2rem] bg-[var(--base)] p-[2rem] rounded-lg shadow-lg">
                    <h3 class="text-[1.5rem] text-[var(--text_dark)] mb-[1rem]">Pēdējā aktivitāte</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-[var(--border-color)]">
                                    <th class="text-left p-[1rem] text-[var(--text_dark)]">Darbība</th>
                                    <th class="text-left p-[1rem] text-[var(--text_dark)]">Lietotājs</th>
                                    <th class="text-left p-[1rem] text-[var(--text_dark)]">Datums</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(activity, index) in recentActivity" :key="index" class="border-b border-[var(--border-color)]">
                                    <td class="p-[1rem] text-[var(--text_light)]">{{ activity.action }}</td>
                                    <td class="p-[1rem] text-[var(--text_light)]">{{ activity.user }}</td>
                                    <td class="p-[1rem] text-[var(--text_light)]">{{ activity.date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Developer_Header from './Developer_Header.vue';
import Developer_Sidebar from './Developer_Sidebar.vue';
import axios from 'axios';

const stats = ref({
    teachers: 0,
    users: 0,
    messages: 0,
    unreadMessages: 0
});

const recentActivity = ref([]);

const loadStats = async () => {
    try {
        const response = await axios.get('/api/developer/stats');
        stats.value = response.data;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
};

const loadRecentActivity = async () => {
    try {
        const response = await axios.get('/api/developer/activity');
        recentActivity.value = response.data;
    } catch (error) {
        console.error('Error loading recent activity:', error);
    }
};

onMounted(() => {
    loadStats();
    loadRecentActivity();
});
</script> 