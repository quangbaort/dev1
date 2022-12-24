<template>
<div>
<div class="auth-wrapper auth-v2" v-if="!confirm">
    <div class="auth-inner">
      <v-row class="auth-row ma-0">
        <v-col lg="12" class="d-flex align-center auth-bg">
          <v-row>
            <v-col cols="12"sm="8" md="6"lg="12"class="mx-auto">
              <v-card flat>
              <div class="d-flex justify-content-center" style="justify-content: center">
                <v-img  :src="isDark ? appLogoDark : appLogoLight" alt="logo" contain class="py-2" max-width="190px" max-height="86px"></v-img>
              </div>
                <v-card-text>
                  <h2 class="mt-2">
                    {{this.$trans('app.password_reset')}} 🔒
                  </h2>
                  <p class="my-2">{{ this.$trans('app.please_password') }}</p>
                </v-card-text>
                <v-card-text>
                  <v-form ref="form" @submit.prevent="handleFormSubmit">
                    <v-text-field v-model="password" outlined  :type="!isPasswordVisible ? 'text' : 'password'" :label="this.$trans('app.password_new')" hide-details="auto" class="mb-6"
                      :rules="[validators.requiredField(password , this.$trans('app.password')) , validators.passwordValidator(password)]"
                      :append-icon="isPasswordVisible ? icons.mdiEyeOffOutline:icons.mdiEyeOutline"
                      @click:append="isPasswordVisible = !isPasswordVisible"
                    >
                    </v-text-field>
                    <v-text-field v-model="passwordConfirm" :type="!isPasswordVisible1 ? 'text' : 'password'" outlined :label="this.$trans('app.password_confirm')" hide-details="auto" class="mb-6"
                      :rules="[validators.requiredField(passwordConfirm , this.$trans('app.password')) , validators.confirmedValidator(passwordConfirm, password) , validators.passwordValidator(passwordConfirm)]"
                      :append-icon="isPasswordVisible1 ? icons.mdiEyeOffOutline:icons.mdiEyeOutline"
                      @click:append="isPasswordVisible1 = !isPasswordVisible1"
                    >
                    </v-text-field>
                    <v-row v-if="errorMessages.length > 0" class="my-4 justify-center">
                        <v-col cols="1" md="1" sm="1" style="background:red; max-width : 5px; flex: 0; padding : 5px"></v-col>
                        <v-col cols="11" md="11">
                          <p color="red" style="color : red" class="text-danger">{{errorMessages}}</p>
                        </v-col>
                      </v-row>
                    <v-btn  block color="primary" type="submit" class="my-4" style="width:100%">
                      {{ this.$trans('app.password_reset') }}
                    </v-btn>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
  </div>
  <div class="auth-wrapper auth-v2" v-else>
    <div class="auth-inner">
      <v-row class="auth-row ma-0">
        <v-col lg="12" class="d-flex align-center auth-bg">
          <v-row>
            <v-col cols="12"sm="8" md="6"lg="12"class="mx-auto">
              <v-card flat>
              <div class="d-flex justify-content-center" style="justify-content: center">
                <v-img :src="appLogo" alt="logo" contain class="py-2" max-width="190px" max-height="86px"></v-img>
              </div>
                <v-card-text>
                  <h2 class="mt-2">
                    {{ this.$trans('app.password_reset_completed') }}🔒
                  </h2>
                </v-card-text>
                <v-card-text>
                  <v-row  class="mb-2 justify-center">
                      <v-col cols="1" md="1" style="background:#30529e; max-width : 5px; flex: 0; padding : 5px"></v-col>
                      <v-col cols="11" md="11">
                        <p   class="text-danger">{{ this.$trans('app.title_password_reset_completed' )}}</p>
                      </v-col>
                      <v-col cols="12" md="12">
                        {{ this.$trans('app.message_password_reset_completed') }}
                      </v-col>
                  </v-row>
                   <router-link :to="{name:'auth-login'}" class="my-2">
                      {{this.$trans('app.back_to_login')}}
                  </router-link>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
  </div>
<loader v-if="loading" object="#335cff" color1="#ffffff" color2="#17fd3d" size="16" speed="2" bg="#343a40" objectbg="#999793" opacity="80" disableScrolling="false" name="spinning"></loader>
</div>

</template>
<script>
import { ref } from '@vue/composition-api'
import { mdiChevronLeft , mdiEyeOffOutline , mdiEyeOutline } from '@mdi/js'
import themeConfig from '@themeConfig'
import { emailValidator , requiredField , confirmedValidator , passwordValidator } from '@core/utils/validation'
import axios from '@axios'
import { useRouter } from '@core/utils'
import useAppConfig from '@core/@app-config/useAppConfig'
export default {
  setup() {
    const { route } = useRouter()
    const { isDark } = useAppConfig()
    const isPasswordVisible = ref(true)
    const isPasswordVisible1 = ref(true)
    const form = ref(null)
    const password = ref('')
    const passwordConfirm = ref('')
    const loading = ref(false)
    const errorMessages = ref([])
    const confirm = ref(false)
    const token = route.value.params.token
    const email = route.value.params.email
    const handleFormSubmit = () => {

      const isFormValid = form.value.validate()
      if (!isFormValid) return
      if(!loading.value){
        loading.value = true
        errorMessages.value = []
        axios.post('/auth/reset-password',
        { email : email,  password: password.value, token: token, password_confirmation: passwordConfirm.value}).then(response => {
          loading.value = false
          if(response.data.message) return confirm.value = true
        })
        .catch(error => {
          loading.value = false
          console.log(error);
          return errorMessages.value = error.response.data.message ?? error.response.data.errors.link[0]
        })
      }
    }
    return {
      form,
      isPasswordVisible,
      isPasswordVisible1,
      password,
      email,
      handleFormSubmit,
      errorMessages,
      confirm,
      loading,
      passwordConfirm,
      isDark,

      // themeConfig
      appName: themeConfig.app.name,
      appLogoLight: themeConfig.app.logoLight,
      appLogoDark: themeConfig.app.logoDark,

      icons: {
        mdiChevronLeft,
        mdiEyeOffOutline,
        mdiEyeOutline
      },
      validators: {
        requiredField,
        emailValidator,
        confirmedValidator,
        passwordValidator,
      }
    }
  }
}
</script>
<style lang="scss">
@import '@core/preset/preset/pages/auth.scss';
</style>
