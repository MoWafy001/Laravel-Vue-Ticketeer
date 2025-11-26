import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
    const items = ref([])

    const totalItems = computed(() => {
        return items.value.reduce((sum, item) => sum + item.quantity, 0)
    })

    const totalPrice = computed(() => {
        return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    })

    function addItem(ticket, quantity = 1) {
        const existingItem = items.value.find(item => item.ticket_id === ticket.id)

        if (existingItem) {
            existingItem.quantity += quantity
        } else {
            items.value.push({
                ticket_id: ticket.id,
                ticket: ticket,
                quantity: quantity,
                price: ticket.price,
            })
        }

        saveToLocalStorage()
    }

    function removeItem(ticketId) {
        items.value = items.value.filter(item => item.ticket_id !== ticketId)
        saveToLocalStorage()
    }

    function updateQuantity(ticketId, quantity) {
        const item = items.value.find(item => item.ticket_id === ticketId)
        if (item) {
            item.quantity = Math.max(1, quantity)
            saveToLocalStorage()
        }
    }

    function clearCart() {
        items.value = []
        saveToLocalStorage()
    }

    function saveToLocalStorage() {
        localStorage.setItem('cart', JSON.stringify(items.value))
    }

    function loadFromLocalStorage() {
        const saved = localStorage.getItem('cart')
        if (saved) {
            items.value = JSON.parse(saved)
        }
    }

    // Load cart on initialization
    loadFromLocalStorage()

    return {
        items,
        totalItems,
        totalPrice,
        addItem,
        removeItem,
        updateQuantity,
        clearCart,
    }
})
