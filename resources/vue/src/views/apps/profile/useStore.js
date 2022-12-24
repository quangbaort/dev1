import store from '@/store'
import { ref , watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
const trans = lang
export default function useStore() {
  const listBoard = ref([])
  const company = ref(null)
  const loading = ref(false)
  const selectedCompany = ref([])
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const listTable = ref([])
  const listOrganizations = ref([])
  const organizationName = ref(null)
  const role = ref(false)
    const tableColumnsCompanies = [
      { text: trans('app.company'), value: 'name' , sortable: true },
      { text: trans('app.prefectures'), value: 'prefecture',sortable: true  },
      { text: trans('app.municipalities'), value: 'street', sortable: true },
      { text: trans('app.tel'), value: 'tel', sortable: true },
    ]
  const getTree = () => {
    store
    .dispatch('profile/getTree', {
      organization_id: JSON.parse(localStorage.getItem('Organization')).id
    })
      .then(response => {
        listBoard.value = JSON.parse(JSON.stringify(response.data))
      })
      .catch(error => {

      })
  }
  const fetchCompany = (page = 1 , sort = null) => {
    if(!company.value) return
    loading.value = true
    store
      .dispatch('profile/fetchCompany' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page :  page,
        name : company.value,
        sort : sort
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
  const changeItemPerPage = (limit = 5) => {
  }
  const searchOrganization = () => {


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
