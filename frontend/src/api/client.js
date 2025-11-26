import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

// Request interceptor to add auth token
api.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore()
        if (authStore.token) {
            config.headers.Authorization = `Bearer ${authStore.token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor to handle errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        // Only auto-logout if we have a token and got 401
        // This prevents logout on public endpoints that return 401
        if (
            error.response?.status === 401 &&
            error.config?.headers?.Authorization &&
            !error.config.url.includes('/events/') // Don't logout/redirect for public event endpoints
        ) {
            const authStore = useAuthStore()
            authStore.logout()
            // Only redirect if we're not already on login page
            if (window.location.pathname !== '/login') {
                window.location.href = '/login'
            }
        }
        return Promise.reject(error)
    }
)

export default api
