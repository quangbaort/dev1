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
                    .post('/users', queryParams)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        deleteID(ctx, { id }) {
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
        fetchOrganizations(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/organizations', { params: queryParams })
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        getHoliday(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios.get('/holidays', { params: queryParams })
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        getTree(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios
                    .get('departments/tree', { params: queryParams })
                    .then(response => resolve(response))
                    .catch(errors => reject(errors))
            })
        },
        getMemo(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/memos', { params: queryParams })
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        detailMemo(ctx, { id }) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`memos/${id}`)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        addNewEvent(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios
                    .post('/events', queryParams)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        getEvent(ctx, queryParams) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/events', { params: queryParams })
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        replyEvent(ctx, {id, answer}) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/events/${id}/response/${answer}`)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
      replyEventAdmin(ctx, {id, answer, user_id}) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/events/${id}/response/${answer}?user_id=${user_id}`)
            .then(response => resolve(response))
            .catch(error => reject(error))
        })
      },
        deleteEvent(ctx, { id }) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`/events/${id}`)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        chartEvent(ctx, {id,start='',end='', name='', company='',}) {
            return new Promise((resolve, reject) => {
              let url = `/events/${id}/chart?`
              if(start && end) {
                url += `start=${start}&end=${end}`
              }
              if (name) {
                url += `&name=${name}`
              }
              if (company) {
                url += `&company=${company}`
              }
                axios
                    .get(url)
                    .then(response => resolve(response))
                    .catch(error => reject(error))
            })
        },
        getEventReplies(ctx , queryParams) {
          return new Promise((resolve, reject) => {
            axios
                .get(`event-replies` , { params: queryParams} )
                .then(response => resolve(response))
                .catch(error => reject(error))
        })
        },
        departments(ctx, queryParams) {
          return new Promise((resolve, reject) => {
            axios
              .get('/departments', { params: queryParams })
              .then(response => resolve(response))
              .catch(error => reject(error))
          })
        },
        detailEvent(ctx, { id }) {
          return new Promise((resolve, reject) => {
            axios
              .get(`/events/${id}`)
              .then(response => resolve(response))
              .catch(error => reject(error))
          })
        },
        duplicateEvent(ctx, queryParams) {
          return new Promise((resolve, reject) => {
            axios
                .post('/events/duplicateEvent', queryParams)
                .then(response => resolve(response))
                .catch(error => reject(error))
        })
        },
        sendMailRemind(ctx, { id }) {
          return new Promise((resolve, reject) => {
            axios
                .get(`events/${id}/remind-answer`)
                .then(response => resolve(response))
                .catch(error => reject(error))
            })
        },
    }
}
