import store from '@/store'
import { ref , watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
const trans = lang
export default function useStore() {
  const userData =localStorage.getItem('userData') !== undefined ? JSON.parse(localStorage.getItem('userData')): null
  const admin = store.state.role != 5 || userData.is_super_admin ? true : false
  const listBoard = ref([])
  const company = ref(null)
  const loading = ref(false)
  const selectedCompany = ref([])
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const listTable = ref([])
  const listOrganizations = ref([])
  const organizationName = ref(null)
  const role = ref(null)
  const roles = ref(null)
    const tableColumnsCompanies = [
      { text: trans('app.company'), value: 'name' , sortable: true },
      { text: trans('app.prefectures'), value: 'prefecture',sortable: true  },
      { text: trans('app.municipalities'), value: 'city', sortable: true },
      { text: trans('app.tel'), value: 'tel', sortable: true },
    ]
  const getTree = () => {
    if(!store.state.loading){
      store.commit('TOGGLE_LOADING', true)
      store
        .dispatch('users/getTree', {
          organization_id: JSON.parse(localStorage.getItem('Organization')).id
        })
        .then(response => {
          listBoard.value = JSON.parse(JSON.stringify(response.data))
          store.commit('TOGGLE_LOADING', false)
        })
    }
  }
  const fetchCompany = (page = 1, sort = null) => {
    // if(!company.value) return
    loading.value = true
    store
      .dispatch('users/fetchCompany' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: paginate.value.itemsPerPage ?? perPage.value,
        name : company.value,
        sort : sort,
        page : page ?? paginate.value.currentPage
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
        selectedCompany.value = []
        loading.value = false
      })
      .catch(error => {
      })
  }
  const changeItemPerPage = ( page = 1 , limit = 10 , sort = null) => {
    loading.value = true
    store
      .dispatch('users/fetchCompany', {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: limit,
        page: paginate.value.currentPage ?? page,
        name: company.value,
      })
      .then((response) => {
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
        loading.value = false
      })
  }
  const searchOrganization = () => {
    if(!loading.value){
      loading.value = true
      store.dispatch('users/searchOrganization' , {
        name : organizationName.value,
        limit: 5
      }).then((response) => {
        loading.value = false
        const { data , meta } = response.data
        listOrganizations.value = data
      }).catch((error) =>{
        loading.value = false
      })
    }

  }
  watch:{
    fetchCompany()
  }

  return {
    listBoard ,
    tableColumnsCompanies,
    getTree,
    listTable,
    selectedCompany,
    fetchCompany,
    company,
    loading,
    paginate,
    changeItemPerPage,
    role,
    organizationName,
    searchOrganization,
    listOrganizations,
  }
}
