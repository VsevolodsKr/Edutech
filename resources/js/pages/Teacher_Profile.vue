<template>
    <div>
        <Header />
        <div class="main-content">
            <section :class="sectionClasses">
                <h1 class="text-[1.5rem] text-text_dark capitalize">Teacher Profile</h1>
                <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
                
                <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-button"></div>
                </div>
                
                <div v-else-if="teacher" class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[2rem]">
                    <!-- Teacher Info -->
                    <div class="bg-base rounded-lg p-[2rem] text-center">
                        <img 
                            :src="teacher.image" 
                            :alt="teacher.name"
                            class="w-48 h-48 rounded-full mx-auto mb-4 object-cover"
                        >
                        <h2 class="text-[2rem] text-text_dark mb-2">{{ teacher.name }}</h2>
                        <p class="text-[1.3rem] text-text_light mb-4">{{ teacher.title || 'Teacher' }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center">
                                <h3 class="text-[1.8rem] text-button">{{ totalStudents }}</h3>
                                <p class="text-[1.2rem] text-text_light">Students</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-[1.8rem] text-button">{{ totalCourses }}</h3>
                                <p class="text-[1.2rem] text-text_light">Courses</p>
                            </div>
                        </div>
                        
                        <div class="text-left mb-6">
                            <h3 class="text-[1.5rem] text-text_dark mb-2">About</h3>
                            <p class="text-[1.2rem] text-text_light leading-relaxed">
                                {{ teacher.bio || 'No bio available' }}
                            </p>
                        </div>
                        
                        <div class="flex justify-center space-x-4">
                            <a v-if="teacher.website" 
                               :href="teacher.website" 
                               target="_blank"
                               class="text-[1.5rem] text-button hover:text-text_dark transition-colors">
                                <i class="fas fa-globe"></i>
                            </a>
                            <a v-if="teacher.linkedin" 
                               :href="teacher.linkedin" 
                               target="_blank"
                               class="text-[1.5rem] text-button hover:text-text_dark transition-colors">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a v-if="teacher.github" 
                               :href="teacher.github" 
                               target="_blank"
                               class="text-[1.5rem] text-button hover:text-text_dark transition-colors">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Teacher Courses -->
                    <div class="space-y-4">
                        <h3 class="text-[1.5rem] text-text_dark mb-4">Courses by {{ teacher.name }}</h3>
                        <div v-if="courses.length === 0" class="text-center text-text_light text-[1.2rem]">
                            No courses available
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="course in courses" 
                                 :key="course.id"
                                 class="bg-base rounded-lg p-4 flex items-center space-x-4">
                                <img 
                                    :src="course.thumbnail" 
                                    :alt="course.title"
                                    class="w-24 h-24 rounded-lg object-cover"
                                >
                                <div>
                                    <h4 class="text-[1.3rem] text-text_dark mb-1">{{ course.title }}</h4>
                                    <p class="text-[1.1rem] text-text_light mb-2">{{ course.description }}</p>
                                    <router-link 
                                        :to="'/course/' + course.id"
                                        class="text-button hover:text-text_dark transition-colors text-[1.1rem]"
                                    >
                                        View Course →
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center text-text_light text-[1.3rem]">
                    Teacher not found
                </div>
            </section>
        </div>
        <Sidebar />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import store from '../store/store';

const route = useRoute();
const { width } = useWindowSize();

// State
const teacher = ref(null);
const courses = ref([]);
const isLoading = ref(true);
const totalStudents = ref(0);
const totalCourses = ref(0);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadTeacherData = async () => {
    try {
        const teacherId = route.params.id;
        const [teacherResponse, coursesResponse, statsResponse] = await Promise.all([
            axios.get(`/api/teachers/${teacherId}`),
            axios.get(`/api/teachers/${teacherId}/courses`),
            axios.get(`/api/teachers/${teacherId}/stats`)
        ]);

        teacher.value = teacherResponse.data;
        courses.value = coursesResponse.data;
        
        const stats = statsResponse.data;
        totalStudents.value = stats.totalStudents;
        totalCourses.value = stats.totalCourses;
    } catch (error) {
        console.error('Error loading teacher data:', error);
    } finally {
        isLoading.value = false;
    }
};

// Lifecycle
onMounted(() => {
    loadTeacherData();
});
</script>