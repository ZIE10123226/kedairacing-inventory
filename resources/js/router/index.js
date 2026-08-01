import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../pages/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../pages/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/',
        component: () => import('../layouts/MainLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('../pages/Dashboard.vue')
            },
            {
                path: 'sparepart',
                name: 'Sparepart',
                component: () => import('../pages/Sparepart.vue')
            },
            {
                path: 'kategori',
                name: 'Kategori',
                component: () => import('../pages/Category.vue')
            },
            {
                path: 'approvals',
                name: 'UserApproval',
                component: () => import('../pages/UserApproval.vue')
            },
            {
                path: 'supplier',
                name: 'Supplier',
                component: () => import('../pages/Supplier.vue')
            },
            {
                path: 'barang-masuk',
                name: 'IncomingTransaction',
                component: () => import('../pages/IncomingTransaction.vue')
            },
            {
                path: 'barang-keluar',
                name: 'OutgoingTransaction',
                component: () => import('../pages/OutgoingTransaction.vue')
            },
            {
                path: 'laporan',
                name: 'Report',
                component: () => import('../pages/Report.vue')
            }
            // Master Data dan Transaksi akan ditambahkan di Tahap 6-13
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // Fetch user details if we have token but no user yet
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'Login' });
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;
