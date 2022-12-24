import store from '@/store'
import { getVuetify } from '@core/utils'
import { computed } from '@vue/composition-api'

export default function useConfig() {
  const $vuetify = getVuetify()
  const loading = computed({
    get : () => store.state.loading,
    set: value => {
      store.commit('TOGGLE_LOADING', value)
    },
  })
  return { loading }
}
