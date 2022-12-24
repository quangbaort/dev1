import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang, itemPerPage } from '@/common'
import {mdiDeleteOutline} from '@mdi/js'
const trans = lang
export default function useStore() {
  const tableColumns = [
    {text: trans('app.branch_committee_name'), value: 'name', sortable: false},
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
      width: 50,
    },
  ]
  const headerProps = {
    'sortByText': "Custom Sort By Text"
  }
  const listHandel = [
    {
      title: trans('app.delete'),
      value: 'delete',
      icon: mdiDeleteOutline
    },
  ]
  const listAction = {
    delete: trans('app.delete'),
    detail: trans('app.detail')
  }
  const listTable = ref([])
  const loading = ref(false)
  const nameSearch = ref('')
  const perPage = ref(10)
  const paginate = ref({})
  const name = ref('')
  const boardIdArr = ref([])
  const treeDepartment = ref([])
  const parentId = ref('')
  const dialogs = ref({
    dialog: false,
    addDepartment: false,
  })
  const listBoard = ref([])
  const listBoardEdit = ref([])
  const getTree = () => {
    loading.value = true
    store
    .dispatch('apps/getTree', {
      organization_id: JSON.parse(localStorage.getItem('Organization')).id
    })
      .then(response => {
        listBoard.value = JSON.parse(JSON.stringify(response.data))
        loading.value = false
      })
      .catch(error => {
      })
  }

  const fetchAll = (page = 1) => {
    store.commit('TOGGLE_LOADING' ,true)
    loading.value = true
    store
      .dispatch('apps/fetchAll', {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page: page,
        name: nameSearch.value,
        parent_id: parentId.value,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
      })
      .then(response => {
        boardIdArr.value = []
        const {data, meta} = response.data
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
        store.commit('TOGGLE_LOADING' ,false)

      })
      .catch(error => {
        loading.value = false
      })
  }
  const changeItemPerPage = (limit) => {
    loading.value = true
    store
      .dispatch('apps/fetchAll', {
        limit: limit,
        page: paginate.value.currentPage,
        name: nameSearch.value,
        parent_id: parentId.value,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id
      })
      .then((response) => {
        const {data, meta} = response.data
        listTable.value = data
        // totalListTable.value = data.meta.total
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
        loading.value = false
      })
  }
  watch : {
    getTree()
    fetchAll()
  }
  return {
    listHandel, perPage, name, nameSearch, listTable, tableColumns, loading, listAction, listBoard, listBoardEdit, boardIdArr, paginate,dialogs,
    changeItemPerPage, fetchAll, getTree, treeDepartment, parentId, headerProps
  }
}
