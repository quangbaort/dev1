import store from '@/store'
import { ref , watch } from '@vue/composition-api'
import { lang } from '@/common'
import { itemPerPage , role } from '@/common/'
import { address } from '@/common/address'
const trans = lang
export default function useStore() {
  const listTable = ref([])
  const headerNormalUser = [
    { text: trans('app.organization_name'), value: 'name' , sortable: true },
    { text: trans('app.prefectures'), value: 'prefecture',sortable: true  },
    { text: trans('app.municipalities'), value: 'city', sortable: true },
    { text: trans('app.tel'), value: 'tel', sortable: true },
  ]
  const headerSystemAdmin = [
    { text: trans('app.organization_name'), value: 'name' , sortable: true },
    { text: trans('app.prefectures'), value: 'prefecture',sortable: true  },
    { text: trans('app.municipalities'), value: 'city', sortable: true },
    { text: trans('app.tel'), value: 'tel', sortable: true },
    { text: '', value: 'actions', align: 'right', sortable: false },
  ]
  const loading = ref(false)
  const perPage = ref(itemPerPage[0])
  const paginate = ref({})
  const nameOrganization = ref(null)
  const prefectureArr = address
  const prefecture = ref(null)
  const city = ref(null)
  const tel = ref(null)
  const organization = ref(null)
  const sortBy = ref('')
  const fetchAll = (page = 1) => {
    if(!loading.value){
      loading.value = true
      store.commit('TOGGLE_LOADING' ,true)
      store
      .dispatch('listOrganization/fetchAll' , {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page : paginate.value.currentPage ?? page,
        name : nameOrganization.value,
        prefecture :prefecture.value,
        city : city.value,
        tel : tel.value,
        sort : sortBy.value,
        user_id : !role() ? JSON.parse(localStorage.getItem('userData')).id : null
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
        loading.value = false
        store.commit('TOGGLE_LOADING' ,false)
      })
      .catch(error => {
        loading.value = false
      })
    }
  }
  const changeItemPerPage = (limit) => {
    if(!loading.value){
      loading.value = true
      store
        .dispatch('listOrganization/fetchAll', {
          limit: limit,
          page : paginate.value.currentPage,
          name : nameOrganization.value,
          prefecture : prefecture.value,
          city : city.value,
          tel : tel.value,
          user_id : !role() ? JSON.parse(localStorage.getItem('userData')).id : null
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
  }
  watch : { fetchAll() }
  return {
    listTable,headerNormalUser,loading,fetchAll,paginate,prefectureArr,address,
    perPage,nameOrganization,prefecture,city,tel,changeItemPerPage,organization,
    role,headerSystemAdmin, sortBy
  }
}
