import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role?.name === 'admin',
        isKaryawan: (state) => state.user?.role?.name === 'karyawan',
    },
    actions: {
        async login(email, password) {
            try {
                const response = await api.post('/login', { email, password });
                this.token = response.data.access_token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                return response.data;
            } catch (error) {
                throw error.response?.data?.message || 'Login failed';
            }
        },
        async register(formData) {
            try {
                const response = await api.post('/register', formData);
                return response.data;
            } catch (error) {
                // Return generic error or specific validation errors
                throw error.response?.data?.message || 'Registrasi gagal';
            }
        },
        async fetchUser() {
            if (!this.token) return;
            try {
                const response = await api.get('/user');
                this.user = response.data;
            } catch (error) {
                this.logout();
            }
        },
        async logout() {
            try {
                await api.post('/logout');
            } catch (e) {} // Abaikan error logout
            this.user = null;
            this.token = null;
            localStorage.removeItem('token');
        }
    }
});
