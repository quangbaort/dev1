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
                <v-img :src="appLogo" alt="logo" contain class="py-2" max-width="190px" max-height="86px"></v-img>
              </div>
                <v-card-text>
                  <h2 class="my-2">
                    {{this.$trans('app.title_forgot_pass')}} 🔒
                  </h2>
                  <p class="mb-2">
                    {{this.$trans('app.msg_forgot_pass')}}
                  </p>
                </v-card-text>
                <v-card-text>
                  <v-form ref="form" @submit.prevent="handleFormSubmit">
                    <v-text-field v-model="email" outlined :label="this.$trans('app.mail_address')" hide-details="auto" class="mb-6"
                      :rules="[validators.requiredField(email , this.$trans('app.email')) , validators.emailValidator]"
                    >
                    </v-text-field>
                    <v-row v-if="errorMessages.length > 0" class="my-4 justify-center">
                        <v-col cols="1" md="1" sm="1" style="background:red; max-width : 5px; flex: 0; padding : 5px"></v-col>
                        <v-col cols="11" md="11">
                          <p color="red" style="color : red" class="text-danger">{{errorMessages}}</p>
                        </v-col>
                      </v-row>
                    <v-btn  block color="primary" type="submit" class="my-4" style="width:100%">
                      {{this.$trans('app.password_reset')}}
                    </v-btn>
                      <router-link :to="{name:'auth-login'}" class="ms-3 my-2">
                        {{this.$trans('app.back_to_login')}}
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
                    {{this.$trans('app.title_forgot_pass_confirm')}} 🔒
                  </h2>
                </v-card-text>
                <v-card-text>
                  <v-row  class="mb-2 justify-center">
                      <v-col cols="1" md="1" style="background:#f2b14f; max-width : 5px; flex: 0; padding : 5px"></v-col>
                      <v-col cols="11" md="11">
                        <p   class="text-danger">{{ this.$trans('app.warning_forgot_pass')}}</p>
                      </v-col>
                      <v-col cols="12" md="12">
                          {{  this.$trans('app.title_forgot_pass_confirm1') }}
                      </v-col>
                      <v-col cols="12" md="12">
                        {{ this.$trans('app.msg_forgot_pass_confirm') }}
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
import { mdiChevronLeft } from '@mdi/js'
import { ref } from '@vue/composition-api'
import themeConfig from '@themeConfig'
import { emailValidator , requiredField } from '@core/utils/validation'
import axios from '@axios'
export default {
  setup() {
    const isPasswordVisible = ref(true)
    const form = ref(null)
    const email = ref('')
    const loading = ref(false)
    const errorMessages = ref([])
    const confirm = ref(false)
    const handleFormSubmit = () => {

      const isFormValid = form.value.validate()
      if (!isFormValid) return
      if(!loading.value){
        loading.value = true
        axios.post('/auth/forgot-password', { email: email.value }).then(response => {
          loading.value = false
          errorMessages.value = []
          if(response.data.message) return confirm.value = true
        })
        .catch(error => {
          loading.value = false
          return errorMessages.value = error.response.data.message ?? ''
        })
      }
    }
    return {
      form,
      isPasswordVisible,
      email,
      handleFormSubmit,
      errorMessages,
      confirm,
      loading,
      // themeConfig
      appName: themeConfig.app.name,
      appLogo: themeConfig.app.logo,

      icons: {
        mdiChevronLeft,
      },
      validators: {
        requiredField,
        emailValidator,
      },
    }
  },
}
</script>

<style lang="scss">
@import '@core/preset/preset/pages/auth.scss';
</style>
