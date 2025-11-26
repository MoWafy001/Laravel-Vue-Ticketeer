import api from './client'

export const ordersApi = {
  createOrder(data) {
    return api.post('/orders', data)
  },

  getOrders(params = {}) {
    return api.get('/orders', { params })
  },

  getOrder(id) {
    return api.get(`/orders/${id}`)
  },
}
