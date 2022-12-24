import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
import {mdiDeleteOutline } from '@mdi/js'
const trans = lang
const treeToList = require('tree-to-list');

export default function useStore() {
  const loading = ref(false)
  const listHandel = ref([
    { title: trans('app.delete'), value: 'delete' , icon : mdiDeleteOutline },
  ])
  const textDate = ref({
    startDate : trans('app.start_date'),
    endDate : trans('app.end_date')
  })
  const tableColumn = ref([
    { text : trans('app.date') , value : 'start' , sortable : true},
    { text : trans('app.title') , value : 'title' , sortable : false},
    { text : trans('app.branch_committee') , value : 'department.name' , sortable : false},
    { text : trans('app.confirm_status') , value : 'read' , sortable : false, align: 'center'},
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
    }
  ])
  const listTable = ref([])
  const dialogs = ref({
    addNew : false,
    resultAnswer : false,
    dialog : false,
    replyNotify : false,
    detailNotify : false,
  })
  const tableColumnsNotify = ref([
    { text : trans('app.confirm_date_time') , value : 'read_at' , sortable : true },
    { text : trans('app.confirm_status') , value: 'read' , sortable:  true},
    { text : trans('app.name') , value: 'name' , sortable:  true},
    { text : trans('app.company') , value: 'company.name' , sortable : true}
  ])
  const readStatus = (
    {
      unRead : trans('app.unread') ,
      readed : trans('app.already_read')
    }
  )

  const paginate = ref({})
  const userId = JSON.parse(localStorage.getItem('userData')).id
  const selectedNotify = ref([])
  const titleSearch = ref(null)
  const departmentSearch = ref(null)
  const perPage = ref(itemPerPage[0])
  const role = ref(0)
  const sortBy = ref('')
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('notify/fetchAll' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : paginate.value.currentPage ?? page,
        title : titleSearch.value,
        department_name : departmentSearch.value,
        user_id : userId,
        sort : sortBy.value
      })
      .then(response => {
        const { data , meta } = response.data
        listTable.value = data
        paginate.value = {
          total : meta.total,
          currentPage : parseInt(meta.current_page),
          itemsPerPage : parseInt(meta.per_page),
          pageStart : meta.from,
          pageStop : meta.last_page,
          pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength :meta.last_page
        }
        selectedNotify.value = []
        loading.value = false
        store.commit('TOGGLE_LOADING', false)
        //role is true if admin, false if normal user
        role.value = store.state.user.role != 5 || JSON.parse(localStorage.getItem('userData')).is_super_admin ? 1 : 0
      })

      .catch(error => {
        loading.value = false
        store.commit('TOGGLE_LOADING', false)
      })
  }
  const changeItemPerPage = (limit = 5) => {
    loading.value = true
    store.commit('TOGGLE_LOADING', true)
    store.dispatch('notify/checkUserRole', {
      organization_id : JSON.parse(localStorage.getItem('Organization')).id,
      user_id: JSON.parse(localStorage.getItem('userData')).id
    })
      .then(response => {
        role.value = response.data
        store
          .dispatch('notify/fetchAll', {
            organization_id : JSON.parse(localStorage.getItem('Organization')).id,
            limit: limit,
            page : 1,
            title : titleSearch.value,
            department_name : departmentSearch.value,
            user_id : userId,
          })
          .then((response) => {
            const {data, meta}  = response.data
            listTable.value = data
            paginate.value = {
              total : meta.total,
              currentPage : parseInt(meta.current_page),
              itemsPerPage : parseInt(meta.per_page),
              pageStart : meta.from,
              pageStop : meta.last_page,
              pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
              itemsLength :meta.last_page
            }
            loading.value = false
            store.commit('TOGGLE_LOADING', false)
          })
      })
      .catch(error => {
        loading.value = false
        store.commit('TOGGLE_LOADING', false)
      })
  }

  const allBoard = ref([])
  const getDepartments = () =>{
    store
      .dispatch('notify/getTree', {
        organization_id: JSON.parse(localStorage.getItem('Organization')).id,
        response : 'tree',
        all : true,
      })
      .then(response => {
        const { data  } = response
        allBoard.value = treeToList.default(data, 'children')
      })
  }
  const loadingComponent = ref(false)
  const notify = ref({})
  const onReply = (item) => {
    store.commit('TOGGLE_LOADING' ,true)
    dialogs.value.replyNotify = true
    store
      .dispatch('notify/fetchOne', {id: item.id})
      .then(response => {
        notify.value = response.data.data
        store.commit('TOGGLE_LOADING', false)
      })
      .catch(error => {
        store.commit('TOGGLE_LOADING', false)
      })

  }
  let key = 0
  const listUserRep = ref([])
  const newsId = ref('')
  const onResult = (item) => {
    key += 1
    dialogs.value.resultAnswer = true
    newsId.value = item.id
    getStatistic(newsId.value)
    getUserReply(newsId.value)
  }
  const startDate = ref('')
  const endDate = ref('')
  const rateRead = ref(0)
  const getStatistic = (id) => {
    store.commit('TOGGLE_LOADING', true)
    store
      .dispatch('notify/getStatistic', {
        id : id,
        start_read: startDate.value,
        end_read: endDate.value
      })
      .then(response => {
        let statistic = response.data.data
        rateRead.value = Math.round(statistic.read / statistic.total * 100)
        store.commit('TOGGLE_LOADING', false)
      })
      .catch(error => {
        store.commit('TOGGLE_LOADING', false)
      })
  }
  const userName = ref('')
  const companyName = ref('')
  const startDate1 = ref('')
  const endDate1 = ref('')
  const getUserReply = (id) => {
    loading.value = true
    store.commit('TOGGLE_LOADING', true)
    store
      .dispatch('notify/getUserRep', {
        id: id,
        user_name: userName.value,
        company_name: companyName.value,
        start_read: startDate1.value,
        end_read: endDate1.value
      })
      .then(response =>{
        listUserRep.value = response.data.data
        loading.value = false
        store.commit('TOGGLE_LOADING', false)
      })
      .catch(error => {
        loading.value = false
        store.commit('TOGGLE_LOADING', false)
      })
  }
  return {
    paginate,selectedNotify,startDate1, endDate1,loading,role, key,getDepartments, allBoard, sortBy,
    listHandel,tableColumn,listTable,dialogs,tableColumnsNotify, readStatus,textDate, fetchAll,rateRead, newsId, userName, companyName,
    titleSearch, departmentSearch, changeItemPerPage, listUserRep,getStatistic,getUserReply, startDate, endDate,onReply,notify,onResult,loadingComponent
  }
}
