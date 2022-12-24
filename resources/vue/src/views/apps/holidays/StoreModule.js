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
          .get('/holidays', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/holidays', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/holidays/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/holidays/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/holidays/destroyMulti/`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    exportCsv(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/holidays/exportCsv`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
