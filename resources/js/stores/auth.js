import { defineStore } from 'pinia';
import api from '@/api.js';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        loading: false,
        errors: {},
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user?.name || '',
        userGrade: (state) => state.user?.grade_level || null,
        userInitials: (state) => {
            if (!state.user?.name) return '';
            return state.user.name
                .split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        },
    },

    actions: {
        async register(data) {
            this.loading = true;
            this.errors = {};
            try {
                const response = await api.post('/register', data);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('auth_token', this.token);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors || { general: ['Kayıt yapılamadı.'] };
                return false;
            } finally {
                this.loading = false;
            }
        },

        async login(credentials) {
            this.loading = true;
            this.errors = {};
            try {
                const response = await api.post('/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('auth_token', this.token);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors || { general: ['Giriş yapılamadı.'] };
                return false;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            try {
                const response = await api.get('/profile');
                this.user = response.data.user;
            } catch {
                this.logout();
            }
        },

        async logout() {
            try {
                await api.post('/logout');
            } catch {
                // ignore
            }
            this.token = null;
            this.user = null;
            localStorage.removeItem('auth_token');
        },
    },
});
