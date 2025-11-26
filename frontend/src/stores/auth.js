import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/client'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('auth_token') || null)
    const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
    const userType = ref(localStorage.getItem('auth_user_type') || null) // 'organizer' or 'buyer'

    const isAuthenticated = computed(() => !!token.value)
    const isOrganizer = computed(() => userType.value === 'organizer')
    const isBuyer = computed(() => userType.value === 'buyer')

    async function loginOrganizer(credentials) {
        const response = await api.post('/auth/organizer/login', credentials)
        setAuth(response.data.data.token, response.data.data.user, 'organizer')
        return response.data
    }

    async function registerOrganizer(data) {
        const response = await api.post('/auth/organizer/register', data)
        setAuth(response.data.data.token, response.data.data.organizer, 'organizer')
        return response.data
    }

    async function loginBuyer(credentials) {
        const response = await api.post('/auth/buyer/login', credentials)
        setAuth(response.data.data.token, response.data.data.buyer, 'buyer')
        return response.data
    }

    async function registerBuyer(data) {
        const response = await api.post('/auth/buyer/register', data)
        setAuth(response.data.data.token, response.data.data.buyer, 'buyer')
        return response.data
    }

    function setAuth(newToken, newUser, type) {
        token.value = newToken
        user.value = newUser
        userType.value = type
        localStorage.setItem('auth_token', newToken)
        localStorage.setItem('auth_user', JSON.stringify(newUser))
        localStorage.setItem('auth_user_type', type)
    }

    async function logout() {
        try {
            await api.post('/auth/logout')
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            token.value = null
            user.value = null
            userType.value = null
            localStorage.removeItem('auth_token')
            localStorage.removeItem('auth_user')
            localStorage.removeItem('auth_user_type')
        }
    }

    return {
        token,
        user,
        userType,
        isAuthenticated,
        isOrganizer,
        isBuyer,
        loginOrganizer,
        registerOrganizer,
        loginBuyer,
        registerBuyer,
        logout,
    }
})
