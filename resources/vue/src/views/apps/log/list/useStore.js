import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
import { mdiDeleteOutline } from '@mdi/js'
const trans = lang
export default function useStore() {
  let userListTable = ref([])
  const tableColumns = [
    { text: trans('app.operation_date'), value: 'date' , sortable: true, width: '10%' },
    { text: trans('app.operation_screen'), value: 'function' , sortable: false, width: '15%' },
    { text: trans('app.category'), value: 'type' , sortable: false, width: '10%' },
    { text: trans('app.detail'), value: 'detail' , sortable: false, width: '25%' },
    { text: trans('app.operator'), value: 'user_name' , sortable: false, width: '20%' },
    { text: trans('app.organization_name'), value: 'organization_name' , sortable: false  , width: '20%' },
  ]
  const listAction = {
    delete: trans('app.delete'),
    detail : trans('app.detail')
  }
  const listTypeLog = [
    { type:'1', text: trans('app.add') },
    { type:'2',text: trans('app.update') },
    { type:'3',text: trans('app.delete') },
  ]
  const listHandel = [
    { title: trans('app.delete'), value: 'delete' , icon : mdiDeleteOutline },
  ]
  const loading = ref(false)
  const detailSearch = ref('')
  const typeSearch = ref(null)
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const sortBy = ref('')
  // fetch data
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('log/fetchAll' , {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : paginate.value.currentPage ?? page,
        detail: detailSearch.value,
        type: typeSearch.value,
        sort : sortBy.value
      })
      .then(response => {
        const { data , meta } = response.data
        userListTable.value = data
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
        store.commit('TOGGLE_LOADING' ,false)
      })
      .catch(error => {
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)
      })
  }
  const changeItemPerPage = (limit = 5) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('log/fetchAll' , {
        limit: limit,
        page : 1,
        detail: detailSearch.value,
        type: typeSearch.value
      })
      .then(response => {
        const { data , meta } = response.data
        userListTable.value = data
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
        store.commit('TOGGLE_LOADING' ,false)
      })
      .catch(error => {
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)
      })
  }

  watch : {
    fetchAll()
  }
  return {
    userListTable,
    tableColumns,
    loading,
    fetchAll,
    detailSearch,
    typeSearch,
    paginate,
    listAction,
    listHandel,
    perPage,
    listTypeLog,
    changeItemPerPage,
    sortBy
  }
}
