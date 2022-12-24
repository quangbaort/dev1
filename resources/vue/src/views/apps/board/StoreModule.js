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
          .get('/departments', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    nextPage(ctx , queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/departments' , { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/departments', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/departments/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/departments/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getTree(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/departments/tree', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/departments/destroyMulti`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    updateDispOrder(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/departments/updateDispOrder', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
  }
}
