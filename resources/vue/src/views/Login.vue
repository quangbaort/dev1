<template>
<div>
<div class="auth-wrapper auth-v2" id="auth-login">
    <div class="auth-inner">
      <v-row class="auth-row">
        <v-col lg="12" class="d-flex align-center auth-bg">
          <v-row>
            <v-col cols="12" sm="8" md="4" lg="4" class="mx-auto">
              <v-card class="">
              <v-card-title class="d-flex justify-center">
                <v-img  :src="isDark ? appLogoDark : appLogoLight" alt="logo" contain class="py-2"></v-img>
              </v-card-title>
                <!-- login form -->
                <v-card-text>
                  <v-form ref="loginForm" @submit.prevent="handleFormSubmit">
                    <v-text-field autofocus  v-model="email" outlined :placeholder="this.$trans('app.mail_address')"  class="mb-6"
                      :rules="[validators.requiredField(email, this.$trans('app.mail_address')) , validators.emailValidator]"
                                  hide-details="auto"
                                   type="email"

                    >
                    </v-text-field>
                    <v-text-field  v-model="password" outlined :type="isPasswordVisible ? 'text' : 'password'"
                      :placeholder="this.$trans('app.password')"
                                  hide-details="auto"
                      :append-icon="isPasswordVisible ? icons.mdiEyeOffOutline:icons.mdiEyeOutline"
                      :rules="[validators.requiredField(password, this.$trans('app.password'))]" class="mb-2" @click:append="isPasswordVisible = !isPasswordVisible"
                    ></v-text-field>
                      <v-row v-if="errorMessages.length > 0" class="my-4 justify-center">
                        <v-col cols="1" md="1" style="background:red; max-width : 5px; flex: 0; padding : 5px"></v-col>
                        <v-col cols="11" md="11">
                          <p color="red" style="color : red" class="text-danger">{{errorMessages}}</p>
                        </v-col>
                      </v-row>

                    <v-btn block color="primary" type="submit" class="my-6 mg-top-button" style="width:100%;">
                      {{this.$trans('app.login')}}
                    </v-btn>
                    <!-- forget link -->
                      <router-link :to="{name:'auth-forgot-password'}" class="ms-3 my-2">
                        {{this.$trans('app.forgot_password')}}
                      </router-link>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
  </div>
  <loader v-if="loading" object="#335cff" color1="#ffffff" color2="#17fd3d" size="10" speed="2" bg="#343a40" objectbg="#999793" opacity="80" disableScrolling="false" name="spinning"></loader>
</div>

</template>

<script>
// eslint-disable-next-line object-curly-newline
import { mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js'
import {ref, getCurrentInstance, computed} from '@vue/composition-api'
import store from '@/store'
import { required, emailValidator , requiredField } from '@core/utils/validation'
import axios from '@axios'
import themeConfig from '@themeConfig'
import useAppConfig from '@core/@app-config/useAppConfig'
import { useRouter } from '@core/utils'


export default {
  created() {
    store.dispatch('logout').then(() => {

    })
    document.body.click()
  },
  setup() {
    const { isDark } = useAppConfig()
    const { router } = useRouter()
    // Template Ref
    const loginForm = ref(null)
    const loading = ref(false)
    const vm = getCurrentInstance().proxy
    const isPasswordVisible = ref(false)
    const email = ref('')
    const password = ref('')
    const errorMessages = ref([])
    const handleFormSubmit = () => {
      const isFormValid = loginForm.value.validate()
      if (!isFormValid) return

      /*
        1. Make HTTP request to get accessToken
        2. Store received token in localStorage for future use
        3. Make another HTTP request for getting user information
        4. On successful response of user information redirect to home page

        ? We have use promise chaining to get user data from access token
        ? Promise Chaining: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Using_promises#chaining
      */
      if(!loading.value){
        loading.value = true
        axios.post('/auth/login', { email: email.value, password: password.value })
        .then(response => {
          errorMessages.value = []
          let user = response.data.data
          let accessToken  = user.jwt.access_token
          let userAbility = [
            {
              action : "manage",
              subject : "all"
            }
          ]
          localStorage.setItem('accessToken', accessToken)
          localStorage.setItem('userAbility', JSON.stringify(userAbility))
          return user
        }).then(response => {
            localStorage.setItem('userData', JSON.stringify(response))
            let usersData = JSON.parse(localStorage.getItem('userData'))
            if(usersData.is_super_admin) {
                axios.get('organizations').then(response => {
                    let organizations = response.data.data[0]
                    localStorage.setItem('Organization', JSON.stringify(organizations))
                    let organizationID = JSON.parse(localStorage.getItem('Organization')).id
                    axios.get('/menu?organization_id='+organizationID).then(response => {
                      axios.get('/total-unanswered-notify', {
                        headers: {
                          OrganId: organizationID
                        }
                      }).then(res => {
                        localStorage.setItem('totalUnansweredNotify', JSON.stringify(res.data.count))
                        let menu = response.data
                        localStorage.setItem('userMenu', JSON.stringify(menu))
                        router.go(0)
                      })
                    })
                }).catch(error => {
                    window.localStorage.removeItem('userData')
                    loading.value = false
                })
            }else {
              if(usersData.organizations && usersData.organizations.length > 0) {
                  localStorage.setItem('Organizations', JSON.stringify(usersData.organizations))
              }
              if(usersData.organization) {
                let organization = usersData.organization;
                localStorage.setItem('Organization', JSON.stringify(organization))
                let organizationID = organization.id;
                axios.get('/menu?organization_id=' + organizationID).then(response => {
                  axios.get('/total-unanswered-notify', {
                    headers: {
                      OrganId: organizationID
                    }
                  }).then(res => {
                    window.localStorage.setItem('totalUnansweredNotify', JSON.stringify(res.data.count))
                    let menu = response.data
                    localStorage.setItem('userMenu', JSON.stringify(menu))
                    router.go(0)
                  })
                }).catch(error => {
                  window.localStorage.removeItem('userData')
                  window.localStorage.removeItem('Organization')
                  window.localStorage.removeItem('Organizations')
                  loading.value = false
                })
              }
            }
        }).catch(error => {
          loading.value = false
          errorMessages.value = error.response.data.errors.email[0]
        })
      }
    }
    return {
      handleFormSubmit,
      loading,
      isDark,
      isPasswordVisible,
      email,
      password,
      errorMessages,
      requiredField,
      icons: {
        mdiEyeOutline,
        mdiEyeOffOutline
      },
      validators: {
        required,
        emailValidator,
        requiredField
      },

      // themeConfig
      appName: themeConfig.app.name,
      appLogoLight: themeConfig.app.logoLight,
      appLogoDark: themeConfig.app.logoDark,

      // Template Refs
      loginForm,
    }
  },
}
</script>

<style lang="scss">
@import '@core/preset/preset/pages/auth.scss';
</style>
<style scoped>
.loader-spinner{
  width: 90px !important;
  height: 90px !important;
}
#overlay-spinner{
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: 99999 !important;
}
.mg-top-button{
  margin-top: 60px !important;
}
</style>

