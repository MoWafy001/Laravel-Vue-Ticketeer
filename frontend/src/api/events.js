import api from './client'

export const eventsApi = {
  // Public endpoints
  getEvents(params = {}) {
    return api.get('/events', { params })
  },

  getEvent(id) {
    return api.get(`/events/${id}`)
  },

  getEventTickets(eventId) {
    return api.get(`/events/${eventId}/tickets`)
  },

  // Organizer endpoints
  createEvent(data) {
    return api.post('/events', data)
  },

  updateEvent(id, data) {
    return api.put(`/events/${id}`, data)
  },

  deleteEvent(id) {
    return api.delete(`/events/${id}`)
  },

  getEventAnalytics(id, params = {}) {
    return api.get(`/events/${id}/analytics`, { params })
  },

  getEventAttendees(id, params = {}) {
    return api.get(`/events/${id}/attendees`, { params })
  },

  // Ticket management
  createTicketType(eventId, data) {
    return api.post(`/events/${eventId}/tickets`, data)
  },

  updateTicketType(id, data) {
    return api.put(`/tickets/${id}`, data)
  },

  deleteTicketType(id) {
    return api.delete(`/tickets/${id}`)
  },

  // Event members
  addEventMember(eventId, data) {
    return api.post(`/events/${eventId}/members`, data)
  },

  getEventMembers(eventId) {
    return api.get(`/events/${eventId}/members`)
  },

  updateEventMember(eventId, memberId, data) {
    return api.put(`/events/${eventId}/members/${memberId}`, data)
  },

  removeEventMember(eventId, memberId) {
    return api.delete(`/events/${eventId}/members/${memberId}`)
  },
}
