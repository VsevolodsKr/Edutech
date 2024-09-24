import { createRouter, createWebHistory } from "vue-router";
import Home from '../pages/Home.vue';
import About from '../pages/About.vue';
import Courses from '../pages/Courses.vue';
import Teachers from '../pages/Teachers.vue';
import Contact from '../pages/Contact.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Profile from '../pages/Profile.vue';
import Update from '../pages/Update.vue';
import Playlist from '../pages/Playlist.vue';
import Watch_Video from '../pages/Watch_Video.vue';
import Teacher_Profile from "../pages/Teacher_Profile.vue";
import Dashboard from "../admin/Dashboard.vue";

const routes = [
    {
        path: '/',
        component: Home,
        name: 'Home',
        meta: {title: 'Home'}
    },
    {
        path: '/about',
        component: About,
        meta: {title: 'About Us'}
    },
    {
        path: '/courses',
        component: Courses,
        meta: {title: 'Courses'}
    },
    {
        path: '/teachers',
        component: Teachers,
        meta: {title: 'Teachers'}
    },
    {
        path: '/contact',
        component: Contact,
        meta: {title: 'Contact Us'}
    },
    {
        path: '/login',
        component: Login,
        meta: {title: 'Authorization'}
    },
    {
        path: '/register',
        component: Register,
        meta: {title: 'Authorization'}
    },
    {
        path: '/profile',
        component: Profile,
        meta: {title: 'Profile'}
    },
    {
        path: '/update',
        component: Update,
        meta: {title: 'Update Profile'}    
    },
    {
        path: '/playlist',
        component: Playlist,
        meta: {title: 'Playlist'}    
    },
    {
        path: '/watch_video',
        component: Watch_Video,
        meta: {title: 'Video'}
    },
    {
        path: '/teacher_profile',
        component: Teacher_Profile,
        meta: {title: 'Profile'}
    },
    {
        path: '/dashboard',
        component: Dashboard,
        meta: {title: 'Dashboard'}
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from) => {
    document.title = to.meta.title;
})

export default router