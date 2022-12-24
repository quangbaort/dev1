import useNav from '@core/layouts/composable/useNav'
import { useRouter } from '@core/utils'
import { ref, watch } from '@vue/composition-api'
import store from '@/store'
export default function useVerticalNavMenuLink(item) {
  const { isNavLinkActive, navLinkProps } = useNav()
  const { route } = useRouter()

  const isActive = ref(false)

  const linkProps = navLinkProps.value(item)

  watch(
    () => {
      store.commit('TOGGLE_LOADING', true)
      return  route.value.name
    },
    () => {
      store.commit('TOGGLE_LOADING', false)
      isActive.value = isNavLinkActive(item)
    },
    { immediate: true },
  )

  return {
    isActive,
    linkProps,
  }
}
