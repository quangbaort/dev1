import store from '@/store'
import { ref , watch } from '@vue/composition-api'
import { lang , itemPerPage } from '@/common'
const trans = lang
export default function useStore() {
  const listTable = ref([])
  const tableColumns = [
    { text: trans('app.company_name'), value: 'name' , sortable: true },
    { text: trans('app.prefectures'), value: 'prefecture',sortable: true  },
    { text: trans('app.municipalities'), value: 'city', sortable: true },
    { text: trans('app.tel'), value: 'tel', sortable: true },
    {
      text: '',
      value: 'actions',
      align: 'right',
      sortable: false,
    },
  ]
  const loading = ref(false)
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const nameCompany = ref(null)
  const prefecture = ref(null)
  const city = ref(null)
  const tel = ref(null)
  const company = ref(null)

  const selectedCompany = ref([])
  const sortBy = ref('')
  const fetchAll = (page = 1) => {
    loading.value = true
    store.commit('TOGGLE_LOADING' ,true)
    store
      .dispatch('company/fetchAll' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : paginate.value.currentPage ?? page,
        name : nameCompany.value,
        prefecture : prefecture.value,
        city : city.value,
        tel : tel.value,
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
        selectedCompany.value = []
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)

      })
  }
  const changeItemPerPage = (limit = 5) => {
    loading.value = true
    store
      .dispatch('company/fetchAll', {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        limit: limit,
        page: 1
      })
      .then((response) => {
        const data  = response.data
        listTable.value = data.data
        paginate.value = {
          total : data.meta.total, currentPage : parseInt(data.meta.current_page),
          itemsPerPage : parseInt(data.meta.per_page),pageStart : data.meta.from, pageStop : data.meta.last_page,
          pageCount : parseInt(Math.ceil(data.meta.total / data.meta.per_page)),itemsLength :data.meta.last_page
        }
        loading.value = false
      })
  }
  return {
    listTable,tableColumns,loading,fetchAll,paginate,perPage,nameCompany,prefecture,city,tel,changeItemPerPage,company,selectedCompany, sortBy
  }
}
