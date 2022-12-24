import axios from '@axios'

export default {
  namespaced: true,
  state: {
    calendarOptions: [
      {
        color: 'error',
        label: 'Personal',
      },
      {
        color: 'primary',
        label: 'Business',
      },
      {
        color: 'warning',
        label: 'Family',
      },
      {
        color: 'success',
        label: 'Holiday',
      },
      {
        color: 'info',
        label: 'ETC',
      },
    ],
    selectedCalendars: ['Personal', 'Business', 'Family', 'Holiday', 'ETC'],
  },
  getters: {},
  mutations: {
    SET_SELECTED_CALENDARS(state, val) {
      state.selectedCalendars = val
    },
  },
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
    deleteID(ctx, {id}) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/users/${id}` )
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
  },
}
