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
          .get('/safeties', { params: queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addNew(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/safeties', queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteID(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/safeties/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchOne(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/safeties/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getAllDepartments(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/departments/tree', { params: queryParams })
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/safeties/delete-multiple`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    deleteOne(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/safeties/`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    updateAnswer(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/safety-replies/answer`, queryParams)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getListReply(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .get('/safety-replies', { params: queryParams })
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
    getCommentReply(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/safeties/${id}/reply `)
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
    responseReply(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/safety-replies/response-reply', queryParams)
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
    chartReply(ctx, { id, includes = '', queryParams }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`safeties/${id}/chart?includes=${includes}`, { params: queryParams })
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
    resendMail(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`safeties/${id}/remind-answer`)
          .then(response => resolve(response))
          .catch(errors => reject(errors))
      })
    },
  }
}
