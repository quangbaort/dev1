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
          .get('/logs', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/logs/destroyMulti/`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
