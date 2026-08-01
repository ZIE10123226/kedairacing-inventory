import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// Request Interceptor: sisipkan Bearer Token otomatis
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response Interceptor: tangani error global
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            const status = error.response.status;

            // Token tidak valid / expired — paksa logout & redirect ke login
            if (status === 401) {
                localStorage.clear();
                window.location.href = '/login';
                return Promise.reject(error);
            }

            // Forbidden (akun belum disetujui saat login) — biarkan komponen yang handle
            if (status === 403) {
                return Promise.reject(error);
            }
        }
        return Promise.reject(error);
    }
);

export default api;
