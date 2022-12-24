import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
import {
  mdiDeleteOutline,
  mdiExportVariant } from '@mdi/js'
const trans = lang
export default function useStore() {
  const listTable = ref([])
  const tableColumns = [
    { text: trans('app.start_date'), value: 'start' , sortable: true },
    { text: trans('app.end_date'), value: 'end' , sortable: true },
    { text: trans('app.title'), value: 'title' , sortable: true },
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
    },
  ]
  const listAction = {
    delete: trans('app.delete'),
    detail : trans('app.detail')
  }
  const listHandel = [
    { title: trans('app.delete'), value: 'delete' , icon : mdiDeleteOutline },
  ]
  const textNoData = (trans('app.no_data'))
  const totalListTable = ref(0)
  const loading = ref(true)
  const searchQuery = ref('')
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const roleOptions = []
  const titleSearch = ref(null)
  const dialogs = ref({
    dialog : false,
    addNew : false,
    detailMemo : false
  })
  const sortBy = ref('')
  // fetch data
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('memos/fetchAll' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : paginate.value.currentPage ?? page,
        title : titleSearch.value,
        sort: sortBy.value
      })
      .then(response => {
        const { data , meta }  = response.data
        listTable.value = data
        totalListTable.value = meta.total
        paginate.value = {
          total: meta.total,
          currentPage : parseInt(meta.current_page),
          itemsPerPage : parseInt(meta.per_page),
          pageStart : meta.from,
          pageStop : meta.last_page,
          pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength :meta.last_page
        }
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)
        // remove loading state
      })
      .catch(error => {
      })
  }
  const changeItemPerPage = (limit) => {
    store
      .dispatch('memos/fetchAll', {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: limit,
        page: 1
      })
      .then((response) => {
        const { data ,meta } = response.data
        listTable.value = data
        totalListTable.value = meta.total
        paginate.value = {
          total: meta.total,
          currentPage : parseInt(meta.current_page),
          itemsPerPage : parseInt(meta.per_page),
          pageStart : meta.from,
          pageStop : meta.last_page,
          pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength :meta.last_page
        }
        loading.value = false
      })
  }
  watch: { fetchAll() }
  return {
    listTable, tableColumns, totalListTable, loading, fetchAll, searchQuery, paginate, listAction, listHandel, roleOptions,
    perPage, titleSearch, dialogs,changeItemPerPage,textNoData, sortBy
  }
}
