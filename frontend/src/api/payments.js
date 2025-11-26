import api from './client'

export const paymentsApi = {
  createCheckoutSession(data) {
    return api.post('/payments/checkout', data)
  },

  getPayment(id) {
    return api.get(`/payments/${id}`)
  },
}
