import api from './client'

export const profileApi = {
  // Organizer
  getOrganizerProfile() {
    return api.get('/organizer/profile')
  },

  updateOrganizerProfile(data) {
    return api.put('/organizer/profile', data)
  },

  // Buyer
  getBuyerProfile() {
    return api.get('/buyer/profile')
  },

  updateBuyerProfile(data) {
    return api.put('/buyer/profile', data)
  },
}
