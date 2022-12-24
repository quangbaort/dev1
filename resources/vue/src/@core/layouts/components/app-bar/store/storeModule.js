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
          .get('/organizations', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/users/${id}`)
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
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/users', queryParams )
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchRoles(ctx,queryParams){
      return new Promise((resolve, reject) => {
        axios
          .get('/roles', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getMenu(ctx,queryParams){
      return new Promise((resolve, reject) => {
        axios
          .get('/menu', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getUser(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/users/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getProfile(ctx, queryParams){
      return new Promise((resolve, reject) => {
        axios
          .get(`/profile` , { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchCompany(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/companies', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
