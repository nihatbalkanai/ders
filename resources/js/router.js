import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';

const routes = [
    {
        path: '/',
        name: 'landing',
        component: () => import('@/views/Landing.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        component: () => import('@/layouts/AppLayout.vue'),
        meta: { auth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/views/Dashboard.vue'),
            },
            {
                path: '/upload',
                name: 'upload',
                component: () => import('@/views/Upload.vue'),
            },
            {
                path: '/questions',
                name: 'questions',
                component: () => import('@/views/QuestionHistory.vue'),
            },
            {
                path: '/tests',
                name: 'tests',
                component: () => import('@/views/TestList.vue'),
            },
            {
                path: '/tests/:id',
                name: 'test-view',
                component: () => import('@/views/TestView.vue'),
                props: true,
            },
            {
                path: '/profile',
                name: 'profile',
                component: () => import('@/views/Profile.vue'),
            },
            {
                path: '/ai-settings',
                name: 'ai-settings',
                component: () => import('@/views/AiSettings.vue'),
            },
        ]
    },
];

const base = import.meta.env.VITE_APP_URL ? new URL(import.meta.env.VITE_APP_URL).pathname : '/';
const router = createRouter({
    history: createWebHistory(base),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');

    if (to.meta.auth && !token) {
        return next({ name: 'login' });
    }

    if (to.meta.guest && token) {
        return next({ name: 'dashboard' });
    }

    next();
});

export default router;
