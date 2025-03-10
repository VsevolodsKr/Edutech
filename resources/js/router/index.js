import { createRouter, createWebHistory } from "vue-router";
import Swal from "sweetalert2";

// Route modules
const publicRoutes = [
    {
        path: '/',
        component: () => import('../pages/Home.vue'),
        name: 'Home',
        meta: { 
            title: 'Home',
            requiresAuth: false
        }
    },
    {
        path: '/about',
        component: () => import('../pages/About.vue'),
        name: 'About',
        meta: { 
            title: 'About Us',
            requiresAuth: false
        }
    },
    {
        path: '/courses',
        component: () => import('../pages/Courses.vue'),
        name: 'Courses',
        meta: { 
            title: 'Courses',
            requiresAuth: false
        }
    },
    {
        path: '/teachers',
        component: () => import('../pages/Teachers.vue'),
        name: 'Teachers',
        meta: { 
            title: 'Teachers',
            requiresAuth: false
        }
    },
    {
        path: '/contact',
        component: () => import('../pages/Contact.vue'),
        name: 'Contact',
        meta: { 
            title: 'Contact Us',
            requiresAuth: false
        }
    },
    {
        path: '/search',
        component: () => import('../pages/Search.vue'),
        name: 'Search',
        meta: { 
            title: 'Playlists',
            requiresAuth: false
        }
    }
];

const authRoutes = [
    {
        path: '/login',
        component: () => import('../pages/Login.vue'),
        name: 'Login',
        meta: { 
            title: 'Authorization',
            requiresAuth: false,
            guestOnly: true
        }
    },
    {
        path: '/register',
        component: () => import('../pages/Register.vue'),
        name: 'Register',
        meta: { 
            title: 'Authorization',
            requiresAuth: false,
            guestOnly: true
        }
    }
];

const userRoutes = [
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../pages/ViewProfile.vue'),
        meta: { 
            title: 'Profile',
            requiresAuth: true 
        }
    },
    {
        path: '/update-profile',
        name: 'update-profile',
        component: () => import('../pages/UpdateProfile.vue'),
        meta: { 
            title: 'Update Profile',
            requiresAuth: true 
        }
    },
    {
        path: '/playlist/:id',
        component: () => import('../pages/Playlist.vue'),
        name: 'Playlist',
        meta: { 
            title: 'Playlist',
            requiresAuth: false
        }
    },
    {
        path: '/watch_video/:id',
        component: () => import('../pages/Watch_Video.vue'),
        name: 'Watch_Video',
        meta: { 
            title: 'Video',
            requiresAuth: false
        }
    },
    {
        path: '/teacher_profile/:id',
        component: () => import('../pages/Teacher_Profile.vue'),
        name: 'Teacher_Profile',
        meta: { 
            title: 'Profile',
            requiresAuth: false
        }
    },
    {
        path: '/likes',
        component: () => import('../pages/Likes.vue'),
        name: 'Likes',
        meta: { 
            title: 'Your likes',
            requiresAuth: true
        }
    },
    {
        path: '/bookmarks',
        component: () => import('../pages/Bookmarks.vue'),
        name: 'Bookmarks',
        meta: { 
            title: 'Your bookmarks',
            requiresAuth: true
        }
    },
    {
        path: '/edit_comment/:id',
        component: () => import('../pages/Edit_Comment.vue'),
        name: 'Comment',
        meta: { 
            title: 'Edit Comment',
            requiresAuth: true
        }
    },
    {
        path: '/comments',
        component: () => import('../pages/Comments.vue'),
        name: 'Comments',
        meta: { 
            title: 'Your comments',
            requiresAuth: true
        }
    }
];

// Auth guard for admin routes
const requireAdminAuth = (to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!token || !user) {
        // Save the intended destination
        localStorage.setItem('adminRedirectTo', to.fullPath);
        next('/login');
        return;
    }
    
    // Check if user is a teacher
    if (!user.profession) {
        Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to access the admin dashboard.',
            icon: 'error'
        });
        next('/');
        return;
    }
    
    next();
};

const adminRoutes = [
    {
        path: '/dashboard',
        component: () => import('../admin/Dashboard.vue'),
        name: 'Dashboard',
        meta: { 
            title: 'Dashboard',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_profile',
        component: () => import('../admin/Admin_Profile.vue'),
        name: 'Admin_Profile',
        meta: { 
            title: 'Profile',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_update',
        component: () => import('../admin/Admin_Update.vue'),
        name: 'Admin_Update',
        meta: { 
            title: 'Update Profile',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_playlists',
        component: () => import('../admin/Admin_Playlists.vue'),
        name: 'Admin_Playlists',
        meta: { 
            title: 'Playlists',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_add_playlist',
        component: () => import('../admin/Admin_Add_Playlist.vue'),
        name: 'Admin_Add_Playlist',
        meta: { 
            title: 'Add Playlist',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_playlists/update/:id',
        component: () => import('../admin/Admin_Update_Playlist.vue'),
        name: 'Admin_Update_Playlist',
        meta: { 
            title: 'Update Playlist',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_contents',
        component: () => import('../admin/Admin_Contents.vue'),
        name: 'Admin_Contents',
        meta: { 
            title: 'Contents',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_add_content',
        component: () => import('../admin/Admin_Add_Content.vue'),
        name: 'Admin_Add_Content',
        meta: { 
            title: 'Upload Content',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_playlists/:id',
        component: () => import('../admin/Admin_Watch_Playlist.vue'),
        name: 'Admin_Watch_Playlist',
        meta: { 
            title: 'Playlist',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_contents/update/:id',
        component: () => import('../admin/Admin_Update_Content.vue'),
        name: 'Admin_Update_Content',
        meta: { 
            title: 'Update Content',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_watch_content/:id',
        component: () => import('../admin/Admin_Watch_Content.vue'),
        name: 'Admin_Watch_Content',
        meta: { 
            title: 'Content',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    }
];

// Error routes
const errorRoutes = [
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('../pages/NotFound.vue'),
        meta: { 
            title: 'Page Not Found',
            requiresAuth: false
        }
    }
];

// Combine all routes
const routes = [
    ...publicRoutes,
    ...authRoutes,
    ...userRoutes,
    ...adminRoutes,
    ...errorRoutes
];

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    }
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    
    // If the route requires authentication and user is not logged in
    if (to.meta.requiresAuth && !token) {
        next('/login');
        return;
    }
    
    // If user is logged in and tries to access guest-only pages
    if (token && to.meta.guestOnly) {
        next('/');
        return;
    }
    
    // Check admin routes access
    if (to.meta.isAdmin) {
        if (!token || !user) {
            localStorage.setItem('adminRedirectTo', to.fullPath);
            next('/login');
            return;
        }
        
        if (!user.profession) {
            Swal.fire({
                title: 'Access Denied',
                text: 'You do not have permission to access the admin dashboard.',
                icon: 'error'
            });
            next('/');
            return;
        }
    }
    
    // Allow access to public routes
    if (!to.meta.requiresAuth) {
        next();
        return;
    }
    
    next();
});

export default router;