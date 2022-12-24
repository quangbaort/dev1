import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang } from '@/common'
import { useRouter } from '@core/utils'
export default function useStore (){
  const { router } = useRouter()
  const responseData = ref('')
  const status = ref(false)
  const messages = ref('')
  const onAlert = ref(false)
  const errorArr = ref({})
  const listOrganizations = ref([])
  const nameOrganization = ref('')
  const addNew = (queryParams) => {
    store
      .dispatch('apps/addNew' , queryParams)
      .then(response => {
        responseData.value = "success";
        onAlert.value = true
        status.value = true
        messages.value = 'success'
        router.push({name : 'apps-company-list' , params: { status: status.value }})
      })
      .catch(error => {
        errorArr.value = error.response.data.errors
        responseData.value = error.response.data.message;
        onAlert.value = true
        status.value = false;
        messages.value = 'error'
      })
  }
  const back = () => {
    return window.history.back()
  }
  return {
    addNew,
    back,
    responseData,
    status,
    messages,
    onAlert,
    errorArr,
    listOrganizations,
    nameOrganization
  }
}

