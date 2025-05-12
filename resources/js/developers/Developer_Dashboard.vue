<template>
    <div>
        <Developer_Header />
        <section :class="sectionClasses">
            <h1 class="text-[1.5rem] text-text_dark capitalize">Izstrādātāja panelis</h1>
            <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">

            <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">Sveicināti!</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">{{ userData?.name }}</div>
                    <router-link to="/developer_profile" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Skatīt profilu</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.teachers }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Skolotāju skaits sistēmā</div>
                    <router-link to="/developer_teachers" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Pārvaldīt skolotājus</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.users }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Lietotāju skaits sistēmā</div>
                    <router-link to="/developer_users" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Pārvaldīt lietotājus</router-link>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[2rem] mb-[1rem] [@media(max-width:550px)]:text-[1.5rem]">{{ statistics.messages }}</h2>
                    <div class="w-full p-[1rem] bg-background rounded-lg text-center text-[1.2rem] text-text_light mb-[1rem] [@media(max-width:550px)]:text-[1rem] [@media(max-width:550px)]:p-[.5rem]">Ziņojumu skaits sistēmā</div>
                    <router-link to="/developer_messages" class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]">Pārvaldīt ziņojumus</router-link>
                </div>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,_minmax(40rem,_1fr))] gap-[2rem] mt-[2rem] pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Ziņojumu statusi</h2>
                    <div class="relative h-[300px]">
                        <canvas ref="messageStatusChart"></canvas>
                    </div>
                </div>

                <div class="bg-base rounded-lg p-[2rem] w-full">
                    <h2 class="text-center text-text_dark text-[1.5rem] mb-[2rem]">Populārākie skolotāji</h2>
                    <div class="relative h-[300px]">
                        <canvas ref="topTeachersChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
        <Developer_Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useWindowSize } from '@vueuse/core';
import Chart from 'chart.js/auto';
import Developer_Header from '../components/Developer_Header.vue';
import Developer_Sidebar from '../components/Developer_Sidebar.vue';
import store from '../store/store';
import axios from 'axios';

const { width } = useWindowSize();

const activityChart = ref(null);
const messageStatusChart = ref(null);
const topTeachersChart = ref(null);
const chartInstances = ref({
    activity: null,
    messageStatus: null,
    topTeachers: null
});

const showSidebar = computed(() => store.getters.getShowSidebar);
const userData = computed(() => store.getters.getUser);
const statistics = ref({
    teachers: 0,
    users: 0,
    messages: 0,
    activity: { labels: [], users: [], messages: [] },
    messageStatus: { labels: [], data: [] },
    topTeachers: { labels: [], datasets: {} },
    unreadMessages: []
});

const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

const loadDashboardData = async () => {
    try {
        const response = await axios.get('/api/developer/dashboard/stats');
        statistics.value = response.data;

        if (activityChart.value) {
            updateActivityChart(statistics.value.activity);
        }
        if (messageStatusChart.value) {
            updateMessageStatusChart(statistics.value.messageStatus);
        }
        if (topTeachersChart.value) {
            updateTopTeachersChart(statistics.value.topTeachers);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
};

const updateActivityChart = (data) => {
    if (!activityChart.value) return;
    
    if (chartInstances.value.activity) {
        chartInstances.value.activity.destroy();
    }

    const ctx = activityChart.value.getContext('2d');
    chartInstances.value.activity = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Lietotāji',
                    data: data.users,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Ziņojumi',
                    data: data.messages,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: getChartOptions()
    });
};

const updateMessageStatusChart = (data) => {
    if (!messageStatusChart.value) return;
    
    if (chartInstances.value.messageStatus) {
        chartInstances.value.messageStatus.destroy();
    }

    const statusLabelMap = {
        'jauns': 'Jauns',
        'atvērts': 'Atvērts',
        'apstrāde': 'Apstrāde',
        'pabeigts': 'Pabeigts'
    };
    const displayLabels = data.labels.map(status => statusLabelMap[status] || status);

    const ctx = messageStatusChart.value.getContext('2d');
    chartInstances.value.messageStatus = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: displayLabels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    getComputedStyle(document.documentElement).getPropertyValue('--button4'),
                    getComputedStyle(document.documentElement).getPropertyValue('--button3'),
                    getComputedStyle(document.documentElement).getPropertyValue('--button2'),
                    getComputedStyle(document.documentElement).getPropertyValue('--button')
                ],
                borderWidth: 2,
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--base')
            }]
        },
        options: {
            ...getChartOptions(),
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
};

const updateTopTeachersChart = (data) => {
    if (!topTeachersChart.value) return;
    
    if (chartInstances.value.topTeachers) {
        chartInstances.value.topTeachers.destroy();
    }

    const ctx = topTeachersChart.value.getContext('2d');
    chartInstances.value.topTeachers = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Kursi',
                    data: data.datasets.playlists,
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button')
                },
                {
                    label: 'Video',
                    data: data.datasets.contents,
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button2')
                },
                {
                    label: 'Komentāri',
                    data: data.datasets.comments,
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button3')
                },
                {
                    label: 'Patīk',
                    data: data.datasets.likes,
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text_dark'),
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--button4')
                }
            ]
        },
        options: {
            ...getChartOptions(),
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            }
        }
    });
};

const getChartOptions = () => ({
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
});

onMounted(async () => {
    await loadDashboardData();
});

setInterval(loadDashboardData, 5 * 60 * 1000);
</script>