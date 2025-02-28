import { createRouter, createWebHistory } from "vue-router";

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
        meta: { requiresAuth: true }
    },
    {
        path: '/update-profile',
        name: 'update-profile',
        component: () => import('../pages/UpdateProfile.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/playlist/:id',
        component: () => import('../pages/Playlist.vue'),
        name: 'Playlist',
        meta: { 
            title: 'Playlist',
            requiresAuth: true
        }
    },
    {
        path: '/watch_video/:id',
        component: () => import('../pages/Watch_Video.vue'),
        name: 'Watch_Video',
        meta: { 
            title: 'Video',
            requiresAuth: true
        }
    },
    {
        path: '/teacher_profile/:id',
        component: () => import('../pages/Teacher_Profile.vue'),
        name: 'Teacher_Profile',
        meta: { 
            title: 'Profile',
            requiresAuth: true
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

const adminRoutes = [
    {
        path: '/dashboard',
        component: () => import('../admin/Dashboard.vue'),
        name: 'Dashboard',
        meta: { 
            title: 'Dashboard',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_profile',
        component: () => import('../admin/Admin_Profile.vue'),
        name: 'Admin_Profile',
        meta: { 
            title: 'Profile',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_update',
        component: () => import('../admin/Admin_Update.vue'),
        name: 'Admin_Update',
        meta: { 
            title: 'Update Profile',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_login',
        component: () => import('../admin/Admin_Login.vue'),
        name: 'Admin_Login',
        meta: { 
            title: 'Authorization',
            requiresAuth: false,
            guestOnly: true
        }
    },
    {
        path: '/admin_register',
        component: () => import('../admin/Admin_Register.vue'),
        name: 'Admin_Register',
        meta: { 
            title: 'Authorization',
            requiresAuth: false,
            guestOnly: true
        }
    },
    {
        path: '/admin_playlists',
        component: () => import('../admin/Admin_Playlists.vue'),
        name: 'Admin_Playlists',
        meta: { 
            title: 'Playlists',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_add_playlist',
        component: () => import('../admin/Admin_Add_Playlist.vue'),
        name: 'Admin_Add_Playlist',
        meta: { 
            title: 'Add Playlist',
            requiresAuth: true,
            isAdmin: true
        }
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
        }
    },
    {
        path: '/admin_add_content',
        component: () => import('../admin/Admin_Add_Content.vue'),
        name: 'Admin_Add_Content',
        meta: { 
            title: 'Upload Content',
            requiresAuth: true,
            isAdmin: true
        }
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
        path: '/admin_contents/:id',
        component: () => import('../admin/Admin_Watch_Content.vue'),
        name: 'Admin_Watch_Content',
        meta: { 
            title: 'Content',
            requiresAuth: true,
            isAdmin: true
        }
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
router.beforeEach(async (to, from, next) => {
    // Update document title
    document.title = to.meta.title;

    // Get authentication token
    const token = localStorage.getItem('token');
    const isAuthenticated = !!token && token !== '';

    // Handle guest-only routes
    if (to.meta.guestOnly && isAuthenticated) {
        return next({ name: 'Home' });
    }

    // Handle protected routes
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next({ name: 'Login' });
    }

    // Handle admin routes
    if (to.meta.isAdmin) {
        try {
            const response = await fetch('/api/user/role', {
                headers: { Authorization: `Bearer ${token}` }
            });
            const { role } = await response.json();
            
            if (role !== 'admin') {
                return next({ name: 'Home' });
            }
        } catch (error) {
            console.error('Error checking user role:', error);
            return next({ name: 'Home' });
        }
    }

    next();
});

export default router;