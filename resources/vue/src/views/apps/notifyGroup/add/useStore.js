import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { itemPerPage } from '@/common'
export default function useStore () {
  const listUser = ref([])
  const loading = ref(false)
  const tableUserColumns = [
    { text: trans('app.name'), value: 'name' , sortable: false },
    {
      text: trans('app.company_name'), value: 'company' , sortable: false
    },
  ]
  const paginate = ref({})
  const perPage = ref(itemPerPage[0])
  const queryParams = ref({})
  const getUser = (page = 1, boardId = [], state = false) => {
    loading.value = true
      queryParams.value = {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : page,
        board_id: boardId.length > 0 ? boardId : null
      }
    store
    .dispatch('notifyGroup/getUser', queryParams.value)
      .then(response => {
        const { data , meta } = response.data
        listUser.value = JSON.parse(JSON.stringify(data))
        if(!status){
          paginate.value = {
            total : meta.total,
            currentPage : parseInt(meta.current_page),
            itemsPerPage : parseInt(meta.per_page),
            pageStart : meta.from,
            pageStop : meta.last_page,
            pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
            itemsLength :meta.last_page
          }
        }

        loading.value = false
      })
  }
  const changeItemPerPage = (limit = 10, boardId = []) => {
    if(!loading.value && boardId.length > 0) {
      loading.value = true
      queryParams.value = {
        limit: limit,
        page : 1,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id,
        board_id: boardId.length >   0 ? boardId : null
      }
      store
        .dispatch('notifyGroup/getUser', queryParams.value)
        .then(response => {
          const { data , meta } = response.data
          listUser.value = JSON.parse(JSON.stringify(data))
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
  }
  return {
    listUser,
    tableUserColumns,
    getUser,
    paginate,
    loading,
    changeItemPerPage
  }
}

