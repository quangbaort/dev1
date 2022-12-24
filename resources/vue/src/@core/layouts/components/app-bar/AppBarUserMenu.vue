<template>
  <div>
    <v-menu
      offset-y
      left
      nudge-bottom="14"
      min-width="230"
      content-class="user-profile-menu-content"
    >
      <template v-slot:activator="{ on, attrs }">
        <div
          bottom
          color="success"
          overlap
          offset-x="12"
          offset-y="12"
          class="ms-4"
          dot
        >
          <v-avatar
            size="40px"
            v-bind="attrs"
            color="primary"
            class="v-avatar-light-bg primary--text"
            v-on="on"
          >
            <v-img
              v-if="userData.avatar_url"
              :src="userData.avatar_url"
            ></v-img>
            <v-img
              v-else
              color="primary"
              size="28"
              :src="asset('assets/images/avatars/1.png')"
            >
            </v-img>
          </v-avatar>
        </div>
      </template>
      <v-list>
        <div class="pb-3 pt-2">
          <div
            bottom
            color="success"
            overlap
            offset-x="12"
            offset-y="12"
            class="ms-4"
            dot
          >
            <v-avatar
              size="40px"
              color="primary"
              class="v-avatar-light-bg primary--text"
            >
              <v-img
                v-if="userData.avatar_url"
                :src="userData.avatar_url"
              ></v-img>
              <v-img
                v-else
                color="primary"
                size="28"
                :src="asset('assets/images/avatars/1.png')"
              >
              </v-img>
            </v-avatar>
          </div>
          <div
            class="d-inline-flex flex-column justify-center ms-3"
            style="vertical-align:middle"
          >
          <span class="text--primary  mb-n1">
            {{ userData.name || userData.username }}
          </span>
            <small class="text--disabled text-capitalize">{{ userData.name_kana }}</small>
          </div>
        </div>

        <!--   <v-divider></v-divider> -->

        <!-- Profile -->
        <v-list-item @click="onDetail()" v-if="!role()">
          <v-list-item-icon class="me-2">
            <v-icon size="22">
              {{ icons.mdiAccountOutline }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>Profile</v-list-item-title>
          </v-list-item-content>
        </v-list-item>


        <v-divider class="my-2"></v-divider>

        <!-- Logout -->
        <v-list-item @click="logoutUser">
          <v-list-item-icon class="me-2">
            <v-icon size="22">
              {{ icons.mdiLogoutVariant }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>
    <Profile :admin="isAdmin" :roles="roles" :dialogs="dialogs" :user="user"></Profile>
  </div>

</template>

<script>
import {
  mdiAccountOutline,
  mdiEmailOutline,
  mdiCheckboxMarkedOutline,
  mdiChatOutline,
  mdiCogOutline,
  mdiCurrencyUsd,
  mdiHelpCircleOutline,
  mdiLogoutVariant,
} from '@mdi/js'
import { useRouter } from '@core/utils'
import { initialAbility } from '@/plugins/acl/config'
import { getCurrentInstance } from '@vue/composition-api'
import { asset, registerModule , role } from '@/common'
import Profile from '@/views/apps/profile/index.vue'
import { ref } from '@vue/composition-api'
import StoreModule from './store/storeModule';
import store from '@/store'
export default {
  components : {
    Profile
  },
  created(){
    this.profile()
  },
  setup() {
    const roles = ref(null)
    const vm = getCurrentInstance().proxy
    registerModule(StoreModule , 'profile')
    const { router } = useRouter()
    const userData = ref(JSON.parse(localStorage.getItem('userData')))
    const loadingComponent = ref(false)
    const dialogs = ref({
      profile : false
    })
    const user = ref(null)
    const logoutUser = () => {
      store.dispatch('logout').then(() => {
        vm.$ability.update(initialAbility)
        router.push({ path: '/login' })
      })
    }
    const fetchRoles = () =>{
      store
        .dispatch('profile/fetchRoles').then(response => {
        const { data } = response.data
        data.forEach(item => {
           item.key = item.key.toString()
        })
        roles.value = data
      })
    }
    const isAdmin = ref(false)
    const onDetail = () => {
      fetchRoles()
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        user.value = null
        store.dispatch('profile/getProfile', {organization_id : JSON.parse(localStorage.getItem('Organization')).id})
        .then(response => {
          const { data } = response.data
          store.commit('TOGGLE_LOADING' , false)
          user.value = data
          isAdmin.value = data.role == 1 ? true : false
          dialogs.value.profile = true
          store.commit('setUser', data)
        })
      }
    }
    const profile = () => {
        store.commit('TOGGLE_LOADING', true)
        user.value = null
        store.dispatch('profile/getProfile', {organization_id: JSON.parse(localStorage.getItem('Organization')).id})
          .then(response => {
            const { data } = response.data
            userData.value = data
            user.value = data
            isAdmin.value = user.role !== 5 && userData.value.is_super_admin ? true : false
            store.commit('setUser', data)
          })
    }
    return {
      logoutUser,
      userData,
      asset,
      dialogs,
      registerModule,
      onDetail,
      user,
      role,
      profile,
      roles,
      isAdmin,
      icons: {
        mdiAccountOutline,
        mdiEmailOutline,
        mdiCheckboxMarkedOutline,
        mdiChatOutline,
        mdiCogOutline,
        mdiCurrencyUsd,
        mdiHelpCircleOutline,
        mdiLogoutVariant,
      },
    }
  },
}
</script>

<style lang="scss">
.user-profile-menu-content {
  .v-list-item {
    min-height: 2.5rem !important;
  }
}
</style>
