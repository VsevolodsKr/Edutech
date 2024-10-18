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
import Admin_Profile from "../admin/Admin_Profile.vue";
import Admin_Update from "../admin/Admin_Update.vue";
import Admin_Login from "../admin/Admin_Login.vue";
import Admin_Register from "../admin/Admin_Register.vue";
import Admin_Playlists from "../admin/Admin_Playlists.vue";
import Admin_Add_Playlist from "../admin/Admin_Add_Playlist.vue";

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
        name: 'About',
        meta: {title: 'About Us'}
    },
    {
        path: '/courses',
        component: Courses,
        name: 'Courses',
        meta: {title: 'Courses'}
    },
    {
        path: '/teachers',
        component: Teachers,
        name: 'Teachers',
        meta: {title: 'Teachers'}
    },
    {
        path: '/contact',
        component: Contact,
        name: 'Contact',
        meta: {title: 'Contact Us'}
    },
    {
        path: '/login',
        component: Login,
        name: 'Login',
        meta: {title: 'Authorization'}
    },
    {
        path: '/register',
        component: Register,
        name: 'Register',
        meta: {title: 'Authorization'}
    },
    {
        path: '/profile',
        component: Profile,
        name: 'Profile',
        meta: {title: 'Profile'}
    },
    {
        path: '/update',
        component: Update,
        name: 'Update',
        meta: {title: 'Update Profile'}    
    },
    {
        path: '/playlist',
        component: Playlist,
        name: 'Playlist',
        meta: {title: 'Playlist'}    
    },
    {
        path: '/watch_video',
        component: Watch_Video,
        name: 'Watch_Video',
        meta: {title: 'Video'}
    },
    {
        path: '/teacher_profile',
        component: Teacher_Profile,
        name: 'Teacher_Profile',
        meta: {title: 'Profile'}
    },
    {
        path: '/dashboard',
        component: Dashboard,
        name: 'Dashboard',
        meta: {title: 'Dashboard'}
    },
    {
        path: '/admin_profile',
        component: Admin_Profile,
        name: 'Admin_Profile',
        meta: {title: 'Profile'}
    },
    {
        path: '/admin_update',
        component: Admin_Update,
        name: 'Admin_Update',
        meta: {title: 'Update Profile'}
    },
    {
        path: '/admin_login',
        component: Admin_Login,
        name: 'Admin_Login',
        meta: {title: 'Authorization'}
    },
    {
        path: '/admin_register',
        component: Admin_Register,
        name: 'Admin_Register',
        meta: {title: 'Authorization'}
    },
    {
        path: '/admin_playlists',
        component: Admin_Playlists,
        name: 'Admin_Playlists',
        meta: {title: 'Playlists'}
    },
    {
        path: '/admin_add_playlist',
        component: Admin_Add_Playlist,
        name: 'Admin_Add_Playlist',
        meta: {title: 'Add Playlist'}
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    const token = localStorage.getItem('token')
    if((to.name === 'Login' || to.name === 'Register') && token != ''){
        return next({
            name: 'Home',
        })
    }
    if((to.name === 'Dashboard' || to.name.includes('Admin')) && token === ''){
        return next({
            name: 'Home'
        })
    }
    next()
})

export default router