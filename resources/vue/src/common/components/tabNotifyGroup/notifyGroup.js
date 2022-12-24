import store from '@/store'
import { ref, watch, computed } from '@vue/composition-api'
import { itemPerPage } from '@/common'
export default function useStore()
{
    const treeBoard = ref([])
    const listUser = ref([])
    const userSelected = ref([])
    const boardId = ref([])
    const notifyGroupId = ref([])
    const paginate = ref({})
    const perPage = ref(itemPerPage[0])
    const loading = ref(false)
    const listUserMailingList = ref([])
    const isEvent = computed(() =>  store.state.moduleName == 'calendar')
    const tableHeader = [
      { text : trans('app.name') , value : 'name' , sortable: false},
      { text : trans('app.company_name') , value : 'company' , sortable: false }
    ]
    const boardIds = ref([])
    const getTree = (userId = null) => {
        store
        .dispatch('tabNotifyGroup/getTree', {
            organization_id: JSON.parse(localStorage.getItem('Organization')).id,
        })
        .then(response => {
          treeBoard.value = JSON.parse(JSON.stringify(response.data))

        })
    }
    const listNotifyGroup = ref([])
    const getAllNotifyGroup = () => {
        loading.value = true
        store
        .dispatch('tabNotifyGroup/getNotifyGroup', {
            organization_id: JSON.parse(localStorage.getItem('Organization')).id
        })
    .then(response => {
          listNotifyGroup.value = JSON.parse(JSON.stringify(response.data.data))
          loading.value = false
        })
    }
    const statusGetUser = ref(true)
    const getUser = (page = 1, state = false) => {
        if (!loading.value) {
            loading.value = true
            statusGetUser.value = true
            let queryParams = null
            if (!boardId.value || boardId.value.length == 0) {
                queryParams = {
                    limit: paginate.value.itemsPerPage ?? perPage.value,
                    page :  page,
                    organization_id: JSON.parse(localStorage.getItem('Organization')).id,
                    board_id: [''],
                }
            } else {
                queryParams = {
                    limit: paginate.value.itemsPerPage ?? perPage.value,
                    page : paginate.value.currentPage ?? page,
                    organization_id: JSON.parse(localStorage.getItem('Organization')).id,
                    board_id: boardId.value,
                }
            }
            store
            .dispatch('tabNotifyGroup/getUser', queryParams)
            .then(response => {
                const { data , meta } = response.data
                listUser.value = JSON.parse(JSON.stringify(data))
                boardIds.value = data.length > 0 ? data.map(item => item.departments ? item.departments.map(d => d.id) : []) : []
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
                statusGetUser.value = true
            })
        }

    }
    const getUserMailingList = (page = 1) => {
      store
        .dispatch('tabNotifyGroup/getUser', {
            limit: paginate.value.itemsPerPage ?? perPage.value,
            page :  page,
            organization_id: JSON.parse(localStorage.getItem('Organization')).id,
            notify_group_id: notifyGroupId.value,
        } )
        .then(response => {
          const { data , meta } = response.data
          listUserMailingList.value = JSON.parse(JSON.stringify(data))
          boardIds.value = data.length > 0 ? data.map(item => item.departments ? item.departments.map(d => d.id) : []) : []
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
          statusGetUser.value = true
        })
    }
    const changeItemPerPage = (limit = 10) => {
        loading.value = true
        let queryParams = null
        if (!boardId.value || boardId.value.length == 0) {
            queryParams = {
                limit: limit,
                page :  1,
                organization_id: JSON.parse(localStorage.getItem('Organization')).id,
                notify_group_id: notifyGroupId.value,
                board_id: [''],
            }
        } else {
            queryParams = {
                limit: limit,
                page: 1,
                organization_id: JSON.parse(localStorage.getItem('Organization')).id,
                notify_group_id: notifyGroupId.value,
                board_id: boardId.value,
            }
        }
        store
        .dispatch('tabNotifyGroup/getUser', queryParams)
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
          })
      loading.value = false
    }
    watch: { getTree() , getAllNotifyGroup()}
    watch(boardId,(newValue, oldValue) => {
        const sameArray = JSON.stringify(newValue) === JSON.stringify(oldValue);
        if (!sameArray && boardId.value.length > 0) {
            getUser()
        }
    })

    return {
      listUserMailingList,getUserMailingList,
        treeBoard, listUser, changeItemPerPage, tableHeader,userSelected,loading,isEvent,statusGetUser,
        getUser, getTree, boardId, paginate,getAllNotifyGroup, listNotifyGroup, notifyGroupId,boardIds
    }
}
