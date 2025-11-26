import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('@/views/Home.vue'),
        },
        {
            path: '/events',
            name: 'events',
            component: () => import('@/views/Events.vue'),
        },
        {
            path: '/events/:id',
            name: 'event-details',
            component: () => import('@/views/EventDetails.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('@/views/auth/Login.vue'),
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('@/views/auth/Register.vue'),
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('@/views/Cart.vue'),
            meta: { requiresAuth: true, requiresBuyer: true },
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: () => import('@/views/Checkout.vue'),
            meta: { requiresAuth: true, requiresBuyer: true },
        },
        {
            path: '/my-tickets',
            name: 'my-tickets',
            component: () => import('@/views/buyer/MyTickets.vue'),
            meta: { requiresAuth: true, requiresBuyer: true },
        },
        {
            path: '/organizer',
            name: 'organizer',
            redirect: '/organizer/dashboard',
            meta: { requiresAuth: true, requiresOrganizer: true },
            children: [
                {
                    path: 'dashboard',
                    name: 'organizer-dashboard',
                    component: () => import('@/views/organizer/Dashboard.vue'),
                },
                {
                    path: 'companies',
                    name: 'organizer-companies',
                    component: () => import('@/views/organizer/Companies.vue'),
                },
                {
                    path: 'companies/:id',
                    name: 'organizer-company-details',
                    component: () => import('@/views/organizer/CompanyDetails.vue'),
                },
                {
                    path: 'events',
                    name: 'organizer-events',
                    component: () => import('@/views/organizer/Events.vue'),
                },
                {
                    path: 'events/:id',
                    name: 'organizer-event-details',
                    component: () => import('@/views/organizer/EventDetails.vue'),
                },
            ],
        },
    ],
})

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } })
    } else if (to.meta.requiresOrganizer && !authStore.isOrganizer) {
        next({ name: 'home' })
    } else if (to.meta.requiresBuyer && !authStore.isBuyer) {
        next({ name: 'home' })
    } else {
        next()
    }
})

export default router
