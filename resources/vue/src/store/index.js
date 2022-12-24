import appConfigStoreModule from '@core/@app-config/appConfigStoreModule'
import Vue from 'vue'
import Vuex from 'vuex'
import app from './app'
import { useRouter } from '@core/utils'
import { initialAbility } from '@/plugins/acl/config'
import { getCurrentInstance } from '@vue/composition-api'

Vue.use(Vuex)
export default new Vuex.Store({
  state: {
    loading : false,
    opacity : '80',
    snackbar : {
      isColorSnackbarVisible : false,
      message : '',
      color : 'error'
    },
    moduleName : null,
    countNotify : {
      news : 0,
      event : 0,
      safetyCheck : 0
    },
    role : 5,
    user : {
      id : null,
      name : null,
      email : null,
      avatar : null,
      role : null,
      ability : initialAbility
    },
    userMenu : [],
    alert: false,
    resultsImport: {
      success:{
        users: 0,
        company: 0,
      },
      error:{}
    },
    refetchUser: false,
    organizations : [],
    userMenu : [],
    eventList : [],
    organizations : [],
    organization : {},
  },
  mutations: {
    setOrganizations (state, organizations) {
      state.organizations = organizations
    },
    setOrganization (state, organization) {
      state.organization = organization
    },
    setUserMenu (state, userMenu) {
      state.userMenu = userMenu
    },
    TOGGLE_LOADING(state , value){
      state.loading = value
    },
    setSnackbar(state, value){
      state.snackbar = value
    },
    setModuleName(state, value){
      state.moduleName = value
    },
    setCountNotify(state, value){
      state.countNotify = value
    },
    setRole(state, value){
      state.role = value
    },
    setOpacity(state, value){
      state.opacity = value
    },
    setEventList(state, value){
      state.eventList = value
    },
    removeEvent(state, value){
      state.eventList = state.eventList.filter(item => item.id !== value)
    },
    addEvent(state, value){
      state.eventList.push(value)
    },
    updateEvent(state, value){
      state.eventList = state.eventList.map(item => {
        if(item.id === value.id){
          return value
        }
        return item
      })
    },
    TOGGLE_ALERT(state, value){
      state.alert = value
    },
    SetResultsImport(state, value){
      state.resultsImport = value
    },
    setRefetchUser(state, value){
      state.refetchUser = value
    },
    setRole(state, value){
      state.role = value
    },
    setUser(state, value){
      state.user = value
    },
    setUserMenu(state, value){
      state.userMenu = value
    },
  },
  actions: {
    logout(){
      window.localStorage.removeItem('userData')
      window.localStorage.removeItem('accessToken')
      window.localStorage.removeItem('userAbility')
      window.localStorage.removeItem('Organization')
      window.localStorage.removeItem('userMenu')
      window.localStorage.removeItem('totalUnansweredNotify')
      return
    }
  },
  modules: {
    appConfig: appConfigStoreModule,
    app,
  },
})
