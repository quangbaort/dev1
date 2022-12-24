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
          .get('/users', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
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
    deleteID(ctx,  {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/users/${id}`)
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
    fetchCompany(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/companies', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    searchOrganization(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/organizations', { params: queryParams })
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
    checkEmail(ctx, queryParams){
      return new Promise((resolve, reject) => {
        axios.get('/users/check-email', { params: queryParams })
        .then(response => resolve(response))
        .catch(error => reject(error))
      })
    },
    export(ctx, queryParams){
      return new Promise((resolve, reject) => {
        axios
          .post(`/users/export-csv`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/users/delete-multiple`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    checkCountUser(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/users/check-count-users`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },

}
