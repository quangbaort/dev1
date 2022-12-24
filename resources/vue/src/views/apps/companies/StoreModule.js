import axios from '@axios'
export default {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    fetchAll(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/companies', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/companies', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/companies/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/companies/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/companies/destroyMulti/`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    exportCsv(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/companies/exportCsv`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getUserCompany(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/users`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteUser(ctx,  {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/users/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
