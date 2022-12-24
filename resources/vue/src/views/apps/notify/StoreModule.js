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
    getDepartments(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/departments', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    deleteSelected(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/notify/destroyMulti`, {params: queryParams})
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    getUserRep(ctx, {id, user_name, company_name, start_read, end_read}){
      return new Promise((resolve, reject) =>{
        axios
          .get(`/notifies/${id}/users?user_name=`+user_name+'&company_name='+ company_name+'&start_read='+ start_read+'&end_read='+ end_read)
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    getStatistic(ctx, {id, start_read, end_read}){
      return new Promise((resolve, reject) =>{
        axios
          .get(`/notifies/${id}/chart?start_read=`+start_read+'&end_read='+ end_read)
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    readNotify(ctx, url){
      return new Promise((resolve, reject) =>{
        axios
          .get(url)
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    sendMailRemind(ctx, id){
      return new Promise((resolve, reject) =>{
        axios
          .get(`/notifies/${id}/resend-remind`)
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
    checkUserRole(ctx, queryParams){
      return new Promise((resolve, reject) =>{
        axios
          .get('/notify/checkUserRole', {params: queryParams})
          .then(response => resolve(response))
          . catch(errors => reject(errors))
      })
    },
  },
}
