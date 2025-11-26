import api from './client'

export const companiesApi = {
  getCompanies(params = {}) {
    return api.get('/companies', { params })
  },

  getCompany(id) {
    return api.get(`/companies/${id}`)
  },

  createCompany(data) {
    return api.post('/companies', data)
  },

  updateCompany(id, data) {
    return api.put(`/companies/${id}`, data)
  },

  deleteCompany(id) {
    return api.delete(`/companies/${id}`)
  },

  getCompanyAnalytics(id, params = {}) {
    return api.get(`/companies/${id}/analytics`, { params })
  },

  // Company members
  addMember(companyId, data) {
    return api.post(`/companies/${companyId}/members`, data)
  },

  getMembers(companyId, params = {}) {
    return api.get(`/companies/${companyId}/members`, { params })
  },

  getMember(companyId, memberId) {
    return api.get(`/companies/${companyId}/members/${memberId}`)
  },

  updateMember(companyId, memberId, data) {
    return api.put(`/companies/${companyId}/members/${memberId}`, data)
  },

  removeMember(companyId, memberId) {
    return api.delete(`/companies/${companyId}/members/${memberId}`)
  },
}
