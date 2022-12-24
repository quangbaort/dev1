<template>
  <v-fade-transition mode="out-in">
    <v-icon
      :key="isDark"
      @click="changeTheme"
    >
      {{ isDark ? icons.mdiWeatherSunny : icons.mdiWeatherNight }}
    </v-icon>
  </v-fade-transition>
</template>

<script>
import { mdiWeatherNight, mdiWeatherSunny } from '@mdi/js'
import useAppConfig from '@core/@app-config/useAppConfig'

export default {
  setup() {
    const { isDark } = useAppConfig()
    const changeTheme  = () => {
      isDark.value = !isDark.value
      if(!isDark.value){
        document.getElementsByClassName('v-contextmenu')[0].style.background = '#fff'
        for(let i = 0 ; i<= document.getElementsByClassName('v-contextmenu-item').length; i++) {
          document.getElementsByClassName('v-contextmenu-item')[i].style.color = '#000'
        }

      }
      else {
        document.getElementsByClassName('v-contextmenu')[0].style.background = '#28243d'
        for(let i = 0 ; i<= document.getElementsByClassName('v-contextmenu-item').length; i++) {
          document.getElementsByClassName('v-contextmenu-item')[i].style.color = '#fff'
        }
      }
    }
    return {
      isDark,changeTheme,

      // Icons
      icons: {
        mdiWeatherNight,
        mdiWeatherSunny,
      },
    }
  },
}
</script>

<style>
</style>
