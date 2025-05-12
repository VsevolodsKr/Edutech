<template>
    <div>
        <Admin_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Vadības panelis</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

            <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Prieks Jūs redzēt!</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">{{ teacherData?.name }}</div>
                    <router-link to="/admin_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Skatīt profilu</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.contents }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Kopējais video skaits</div>
                    <router-link to="/admin_contents" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Pievienot jaunu video</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.playlists }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Kopējais kursu skaits</div>
                    <router-link to="/admin_playlists" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Pievienot jaunu kursu</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.comments }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Kopējais komentāru skaits</div>
                    <router-link 
                        to="/admin_comments" 
                        class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                    >
                        Skatīt komentārus
                    </router-link>
                </div>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,_minmax(40rem,_1fr))] gap-[2rem] mt-[2rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Sakarības tendence (Pēdējās 7 dienās)</h2>
                    <div class="relative h-[300px]">
                        <canvas ref="engagementChart"></canvas>
                    </div>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Vispopulārākie video</h2>
                    <div class="space-y-4">
                        <div v-for="(content, index) in popularContents" :key="content.id" 
                            class="flex items-center gap-4 p-4 bg-background rounded-lg">
                            <div class="text-[1.5rem] font-bold text-button">{{ index + 1 }}</div>
                            <div class="flex-1">
                                <h3 class="text-text_dark font-medium">{{ content.title }}</h3>
                                <div class="flex gap-4 text-sm text-text_light">
                                    <span>{{ content.likes || 0 }} patīk</span>
                                    <span>{{ content.comments || 0 }} komentāri</span>
                                    <span class="text-button">{{ content.teacher_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="popularContents.length === 0" class="text-center text-text_light py-4">
                            Nav pieejamu video
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
import { useWindowSize } from '@vueuse/core';
import Chart from 'chart.js/auto';
import Admin_Header from '../components/Admin_Header.vue';
import Admin_Sidebar from '../components/Admin_Sidebar.vue';
import store from '../store/store';

const { width } = useWindowSize();

const engagementChart = ref(null);
const chartInstance = ref(null);

const showSidebar = computed(() => store.getters.getShowSidebar);
const teacherData = computed(() => store.getters.getUser);
const isUserLoaded = computed(() => store.getters.isUserLoaded);
const statistics = computed(() => store.getters.getDashboardStats);
const popularContents = computed(() => {
    return statistics.value?.popularContents || [];
});
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const engagementData = computed(() => {
    const stats = statistics.value;
    if (!stats || !stats.engagement) {
        return {
            labels: [],
            likes: [],
            comments: []
        };
    }
    return stats.engagement;
});

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
                    label: 'Favorītvideo',
                    data: data.likes,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Komentāri',
                    data: data.comments,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    tension: 0.4,
                    fill: false
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
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text_light'),
                        stepSize: 1
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

// Add a watch effect for user data loading
watch(isUserLoaded, async (loaded) => {
    if (loaded && teacherData.value?.encrypted_id) {
        await store.dispatch('loadDashboardStats', teacherData.value.encrypted_id);
    }
});

onMounted(async () => {
    try {
        // Load user data if not already loaded
        if (!isUserLoaded.value) {
            await store.dispatch('loadUserData');
        }
        
        // User data should now be loaded, check if we have valid data
        if (!teacherData.value?.encrypted_id) {
            console.error('No valid user data found');
            return;
        }

        // Load dashboard stats if user is loaded
        if (isUserLoaded.value) {
            await store.dispatch('loadDashboardStats', teacherData.value.encrypted_id);
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
});

// Update watchers to use encrypted_id from teacherData
watch(() => store.getters.getPlaylists, async () => {
    if (teacherData.value?.encrypted_id) {
        await store.dispatch('loadDashboardStats', teacherData.value.encrypted_id);
    }
}, { deep: true });

watch(() => store.getters.getContents, async () => {
    if (teacherData.value?.encrypted_id) {
        await store.dispatch('loadDashboardStats', teacherData.value.encrypted_id);
    }
}, { deep: true });

watch(() => engagementData.value, (newData) => {
    if (newData && engagementChart.value) {
        updateEngagementChart(newData);
    }
}, { deep: true });
</script>