import axios from 'axios';

const api = axios.create({
    baseURL: (import.meta.env.VITE_APP_URL ? new URL(import.meta.env.VITE_APP_URL).pathname : '') + '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            const base = import.meta.env.VITE_APP_URL ? new URL(import.meta.env.VITE_APP_URL).pathname : '';
            window.location.href = base + '/login';
        }
        return Promise.reject(error);
    }
);

export default api;
