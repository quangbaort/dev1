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
          .get('/memos', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/memos', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`memos/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/memos/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/memos/destroyMulti`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    }
  },
}
