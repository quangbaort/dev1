import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang, itemPerPage } from '@/common'
import { mdiDeleteOutline } from "@mdi/js";
const trans = lang
export default function use_store() {
  const listTable = ref([])
  const tableColumns = [
    { text: trans('app.date'), value: 'date', sortable: true, width: '15%' },
    { text: trans('app.holiday_name'), value: 'name', sortable: true, width: '70%' },
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
      width: '15%',
    },
  ]
  const name = ref(null)
  const dateField = (
    { text: trans('app.date') }
  )
  const loading = ref(false)
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const sortBy = ref('')
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING', true)
    store
      .dispatch('holiday/fetchAll', {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page: paginate.value.currentPage ?? page,
        name: name.value,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id,
        sort: sortBy.value
      })
      .then(response => {
        const { data, meta } = response.data
        listTable.value = data
        paginate.value = {
          total: meta.total,
          currentPage: parseInt(meta.current_page),
          itemsPerPage: parseInt(meta.per_page),
          pageStart: meta.from,
          pageStop: meta.last_page,
          pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength: meta.last_page
        }
        loading.value = false
        store.commit('TOGGLE_LOADING', false)

      })
      .catch(error => {
      })
  }
  const changeItemPerPage = (limit = 5) => {
    loading.value = true
    store
      .dispatch('holiday/fetchAll', {
        limit: limit,
        page: paginate.value.currentPage ?? perPage,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id
      })
      .then(response => {
        const { data, meta } = response.data
        listTable.value = data
        paginate.value = {
          total: meta.total,
          currentPage: parseInt(meta.current_page),
          itemsPerPage: parseInt(meta.per_page),
          pageStart: meta.from,
          pageStop: meta.last_page,
          pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength: meta.last_page
        }
        loading.value = false
      })
      .catch(error => {
      })
  }
  const listHandel = [
    { title: trans('app.delete'), value: 'delete', icon: mdiDeleteOutline },
  ]
  watch: { fetchAll() }
  return {
    listTable, tableColumns, loading, fetchAll, paginate, perPage, changeItemPerPage, dateField, listHandel, name, sortBy
  }
}
