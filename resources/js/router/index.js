import { createRouter, createWebHistory } from "vue-router";

const publicRoutes = [
    {
        path: '/',
        component: () => import('../pages/Home.vue'),
        name: 'Home',
        meta: { 
            title: 'Sākumlapa',
            requiresAuth: false
        }
    },
    {
        path: '/about',
        component: () => import('../pages/About.vue'),
        name: 'About',
        meta: { 
            title: 'Par mums',
            requiresAuth: false
        }
    },
    {
        path: '/courses',
        component: () => import('../pages/Courses.vue'),
        name: 'Courses',
        meta: { 
            title: 'Kursi',
            requiresAuth: false
        }
    },
    {
        path: '/teachers',
        component: () => import('../pages/Teachers.vue'),
        name: 'Teachers',
        meta: { 
            title: 'Pasniedzēji',
            requiresAuth: false
        }
    },
    {
        path: '/contact',
        component: () => import('../pages/Contact.vue'),
        name: 'Contact',
        meta: { 
            title: 'Sazinies ar mums',
            requiresAuth: false
        }
    },
];

const authRoutes = [
    {
        path: '/login',
        component: () => import('../pages/Login.vue'),
        name: 'Login',
        meta: { 
            title: 'Autentifikācija',
            requiresAuth: false,
            guestOnly: true
        }
    },
    {
        path: '/register',
        component: () => import('../pages/Register.vue'),
        name: 'Register',
        meta: { 
            title: 'Reģistrācija',
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
            title: 'Profils',
            requiresAuth: true 
        }
    },
    {
        path: '/playlist/:id',
        component: () => import('../pages/Playlist.vue'),
        name: 'Playlist',
        meta: { 
            title: 'Kurss',
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
            title: 'Profils',
            requiresAuth: false
        }
    },
    {
        path: '/likes',
        component: () => import('../pages/Likes.vue'),
        name: 'Likes',
        meta: { 
            title: 'Jūsu favorītvideo',
            requiresAuth: true
        }
    },
    {
        path: '/bookmarks',
        component: () => import('../pages/Bookmarks.vue'),
        name: 'Bookmarks',
        meta: { 
            title: 'Jūsu gramātiezīmētie kursi',
            requiresAuth: true
        }
    },
    {
        path: '/edit_comment/:id',
        component: () => import('../pages/Edit_Comment.vue'),
        name: 'Comment',
        meta: { 
            title: 'Rediģēt komentāru',
            requiresAuth: true
        }
    },
    {
        path: '/comments',
        component: () => import('../pages/Comments.vue'),
        name: 'Comments',
        meta: { 
            title: 'Jūsu komentāri',
            requiresAuth: true
        }
    }
];

const requireAdminAuth = (to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!token || !user) {
        localStorage.setItem('adminRedirectTo', to.fullPath);
        next('/login');
        return;
    }
    
    next();
};

const requireDeveloperAuth = (to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!token || !user) {
        localStorage.setItem('developerRedirectTo', to.fullPath);
        next('/login');
        return;
    }
    
    if (!user.is_developer) {
        if (user.is_teacher) {
            next('/dashboard');
        } else {
            next('/');
        }
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
            title: 'Vadības panelis',
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
            title: 'Profils',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_teachers',
        component: () => import('../developers/Developer_Teachers.vue'),
        name: 'Admin_Teachers',
        meta: { 
            title: 'Skolotāju pārvaldība',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_users',
        component: () => import('../developers/Developer_Users.vue'),
        name: 'Admin_Users',
        meta: { 
            title: 'Lietotāju pārvaldība',
            requiresAuth: true,
            isAdmin: true
        },
        beforeEnter: requireAdminAuth
    },
    {
        path: '/admin_messages',
        component: () => import('../developers/Developer_Messages.vue'),
        name: 'Admin_Messages',
        meta: { 
            title: 'Ziņojumu pārvaldība',
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
            title: 'Kursi',
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
            title: 'Pievienot kursu',
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
            title: 'Rediģēt kursu',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_contents',
        component: () => import('../admin/Admin_Contents.vue'),
        name: 'Admin_Contents',
        meta: { 
            title: 'Video',
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
            title: 'Augšupielādēt video',
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
            title: 'Kurss',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_contents/update/:id',
        component: () => import('../admin/Admin_Update_Content.vue'),
        name: 'Admin_Update_Content',
        meta: { 
            title: 'Rediģēt video',
            requiresAuth: true,
            isAdmin: true
        }
    },
    {
        path: '/admin_watch_content/:id',
        component: () => import('../admin/Admin_Watch_Content.vue'),
        name: 'Admin_Watch_Content',
        meta: { 
            title: 'Apskatīt video',
            requiresAuth: true,
            isTeacher: true
        }
    },
    {
        path: '/admin_comments',
        component: () => import('../admin/Admin_Comments.vue'),
        name: 'Admin_Comments',
        meta: { 
            title: 'Komentāri',
            requiresAuth: true,
            isTeacher: true
        }
    }
];

const developerRoutes = [
    {
        path: '/developer_dashboard',
        component: () => import('../developers/Developer_Dashboard.vue'),
        name: 'Developer_Dashboard',
        meta: { 
            title: 'Vadības panelis',
            requiresAuth: true,
            isDeveloper: true
        },
        beforeEnter: requireDeveloperAuth
    },
    {
        path: '/developer_profile',
        component: () => import('../developers/Developer_Profile.vue'),
        name: 'Developer_Profile',
        meta: { 
            title: 'Mans profils',
            requiresAuth: true,
            isDeveloper: true
        },
        beforeEnter: requireDeveloperAuth
    },
    {
        path: '/developer_teachers',
        component: () => import('../developers/Developer_Teachers.vue'),
        name: 'Developer_Teachers',
        meta: { 
            title: 'Pasniedzēju pārvaldība',
            requiresAuth: true,
            isDeveloper: true
        },
        beforeEnter: requireDeveloperAuth
    },
    {
        path: '/developer_users',
        component: () => import('../developers/Developer_Users.vue'),
        name: 'Developer_Users',
        meta: { 
            title: 'Lietotāju pārvaldība',
            requiresAuth: true,
            isDeveloper: true
        },
        beforeEnter: requireDeveloperAuth
    },
    {
        path: '/developer_messages',
        component: () => import('../developers/Developer_Messages.vue'),
        name: 'Developer_Messages',
        meta: { 
            title: 'Ziņojumu pārvaldība',
            requiresAuth: true,
            isDeveloper: true
        },
        beforeEnter: requireDeveloperAuth
    }
];

const errorRoutes = [
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('../pages/NotFound.vue'),
        meta: { 
            title: 'Kļūda',
            requiresAuth: false
        }
    }
];

const routes = [
    ...publicRoutes,
    ...authRoutes,
    ...userRoutes,
    ...adminRoutes,
    ...developerRoutes,
    ...errorRoutes
];

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

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    
    document.title = to.meta.title ? `${to.meta.title}` : 'Edutech';
    
    if (to.meta.requiresAuth && !token) {
        next('/login');
        return;
    }
    
    if (token && to.meta.guestOnly) {
        next('/');
        return;
    }
    
    if (to.meta.isAdmin) {
        if (!token || !user) {
            localStorage.setItem('adminRedirectTo', to.fullPath);
            next('/login');
            return;
        }
    }
    
    if (!to.meta.requiresAuth) {
        next();
        return;
    }
    
    next();
});

export default router;