import store from '@/store'
import { ref , watch, computed } from '@vue/composition-api'
import { lang , role } from '@/common'
export default function useStore() {
  const roleUser = store.state.role
  const listOrganizations = ref([])
  const userData = JSON.parse(localStorage.getItem('userData'))
  const organization = localStorage.getItem('Organization') !== null
    ? JSON.parse(localStorage.getItem('Organization'))
    : []
  const organizations = localStorage.getItem('Organizations') !== null
  ? JSON.parse(localStorage.getItem('Organizations'))
    : []
  const fetchAll = () => {
    if(roleUser !== 5 || userData.is_super_admin) {
      store
        .dispatch('listOrganization/fetchAll' , {
          all : true,
          user_id : !role() ? JSON.parse(localStorage.getItem('userData')).id : null
        })
        .then(response => {
          const { data } = response.data
          listOrganizations.value = data
        })
    } else {
      listOrganizations.value = organizations.length > 0 ? organizations : [organization]
    }

  }

  watch : { fetchAll() }
  return {
    fetchAll , listOrganizations , role
  }
}
