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
          .get('/notifies', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/notifies', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/notifies/${id}` )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/notifies/${id}`)
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
    getUser(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/users', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    getNotifyGroup(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/notify-groups', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/notify-group/destroyMulti`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    }
  },
}
