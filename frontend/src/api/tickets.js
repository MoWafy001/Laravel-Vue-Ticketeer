import api from './client'

export const ticketsApi = {
  // Buyer endpoints
  getMyTickets(params = {}) {
    return api.get('/buyer/tickets', { params })
  },

  getMyTicket(id) {
    return api.get(`/buyer/tickets/${id}`)
  },

  downloadTicketPDF(id) {
    return api.get(`/buyer/tickets/${id}/pdf`, {
      responseType: 'blob',
    })
  },

  cancelTicket(id) {
    return api.post(`/buyer/tickets/${id}/cancel`)
  },

  // Organizer endpoints
  cancelBuyerTicket(ticketId, data) {
    return api.post(`/organizer/tickets/${ticketId}/cancel`, data)
  },

  // Venue staff
  scanTicket(qrCode) {
    return api.post('/tickets/scan', { qr_code: qrCode })
  },
}
