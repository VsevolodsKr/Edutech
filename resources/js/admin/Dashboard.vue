<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Dashboard</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <!-- Welcome Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Welcome back!</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">{{ teacherData?.name }}</div>
                    <router-link to="/admin_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">View Profile</router-link>
                </div>

                <!-- Contents Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.contents }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Contents</div>
                    <router-link to="/admin_contents" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add New Content</router-link>
                </div>

                <!-- Playlists Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.playlists }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Playlists</div>
                    <router-link to="/admin_playlists" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Add New Playlist</router-link>
                </div>

                <!-- Comments Card -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.comments }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Total Comments</div>
                    <router-link 
                        to="/admin_comments" 
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        View Comments
                    </router-link>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-[repeat(auto-fit,_minmax(40rem,_1fr))] gap-[2rem] mt-[2rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <!-- Engagement Trends -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Engagement Trends (Last 7 Days)</h2>
                    <div class="relative h-[300px]">
                        <canvas ref="engagementChart"></canvas>
                    </div>
                </div>

                <!-- Popular Contents -->
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Most Popular Contents</h2>
                    <div class="space-y-4">
                        <div v-for="(content, index) in popularContents" :key="content.id" 
                            class="flex items-center gap-4 p-4 bg-background rounded-lg">
                            <div class="text-[1.5rem] font-bold text-button">{{ index + 1 }}</div>
                            <div class="flex-1">
                                <h3 class="text-text_dark font-medium">{{ content.title }}</h3>
                                <div class="flex gap-4 text-sm text-text_light">
                                    <span>{{ content.likes }} likes</span>
                                    <span>{{ content.comments }} comments</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Admin_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Chart from 'chart.js/auto';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const router = useRouter();
const { width } = useWindowSize();

// State
const engagementChart = ref(null);
const chartInstance = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const teacherData = computed(() => store.getters.getUser);
const statistics = computed(() => store.getters.getDashboardStats);
const playlists = computed(() => store.getters.getPlaylists);
const contents = computed(() => store.getters.getContents);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const updateEngagementChart = (data) => {
    if (!engagementChart.value) return;
    
    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    const ctx = engagementChart.value.getContext('2d');
    chartInstance.value = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Likes',
                    data: data.likes,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    tension: 0.4,
                },
                {
                    label: 'Comments',
                    data: data.comments,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text_light')
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text_light')
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
};

// Lifecycle
onMounted(async () => {
    // Load initial data
    const user = store.getters.getUser;
    if (user) {
        await store.dispatch('loadDashboardStats', user.id);
    }
});

// Watch for changes in engagement data
watch(() => statistics.value?.engagement, (newData) => {
    if (newData && engagementChart.value) {
        updateEngagementChart(newData);
    }
}, { immediate: true });

// Watch for changes in playlists and contents
watch([playlists, contents], async ([newPlaylists, newContents]) => {
    const user = store.getters.getUser;
    if (user) {
        await store.dispatch('loadDashboardStats', user.id);
    }
}, { deep: true });
</script>