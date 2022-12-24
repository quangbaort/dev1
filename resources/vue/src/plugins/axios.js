// axios
import axios from 'axios'
import Vue from 'vue'
import store from "@/store";

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL : "/api",
  // timeout: 1000,
  headers : {
    'Accept'          : 'application/json',
  }
})

axiosIns.interceptors.request.use(
  config => {

    // Do something before request is sent
    const accessToken = localStorage.getItem('accessToken')

    if (accessToken) {
      const version = 'v1'
      const organization = JSON.parse(localStorage.getItem('Organization'))
      config.baseURL = `${config.baseURL}/${version}`
      // config.baseURL = `${config.baseURL}/${version}/Organization`
      config.headers.Authorization = `Bearer ${accessToken}`
    }

    const accessedOrgan = localStorage.getItem('Organization');
    if (accessedOrgan) {
      config.headers.OrganId = JSON.parse(accessedOrgan).id;
    }


    return config
  },
  error => {
    Promise.reject(error)
  },
)
axiosIns.interceptors.response.use(
  response => {
    if(response.status === 200) {
      store.commit('TOGGLE_LOADING', false)
    }
    return response
  },

  err => {

    if (err.response.status === 401) {
      store.commit('TOGGLE_LOADING', true)
      store.dispatch('logout').then(() => {
        window.location.href = '/login'
      })
      return
    }

    store.commit('TOGGLE_LOADING' , false);
    let options = {
      isColorSnackbarVisible : true,
      message : trans('message.internal_server_error'),
      color : 'error'
    };
    let responseDataErr = err.response.data;

    if (err.response.status === 422) {
      if (!responseDataErr.errors) {
        options.message = responseDataErr.message;
      } else  {
        options.message = responseDataErr.errors;
      }
    }
    if (err.response.status === 429) {
      options.message = '作業が速すぎます。数分後にもう一度お試しください';
    }


    let inErrorStatus = [500, 406, 404, 403];
    if (inErrorStatus.includes(err.response.status) && responseDataErr.message) {
      options.message = err.response.data.message;
    }

    store.commit('setSnackbar', options);

    throw err;
  },

)

Vue.prototype.$http = axiosIns

export default axiosIns
