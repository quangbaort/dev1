import store from '@/store'
import { ref , watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
import {mdiDeleteOutline} from "@mdi/js";
const trans = lang
export default function useStore() {
  const listTable = ref([])
  const tableColumns = [
    { text: trans('app.notify_destination'), value: 'name' , sortable: false },
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
      width: 40,
    },
  ]
  const listHandel = [
    { title: trans('app.delete'), value: 'delete' , icon : mdiDeleteOutline },
  ]
  const loading = ref(false)
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const selectedNotifyGroup = ref([])
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('notifyGroup/fetchAll' , {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : page,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id,
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
        selectedNotifyGroup.value = []
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)
      })
      .catch(error => {
      })
  }
  const changeItemPerPage = (limit = 5) => {
    loading.value = true
    store
      .dispatch('notifyGroup/fetchAll', {
        limit: limit,
        page: paginate.value.currentPage,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id
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
      })
  }
  watch: { fetchAll() }
  return {
    listHandel, listTable, tableColumns, loading, fetchAll, paginate,
    changeItemPerPage, selectedNotifyGroup
  }
}
