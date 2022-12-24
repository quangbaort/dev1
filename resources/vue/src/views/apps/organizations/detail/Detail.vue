<template>
  <div>
    <v-dialog v-model="dialogs.detail" @input="close" max-width="600" persistent scrollable>
    <v-form ref="form" @submit.prevent="handleFormSubmit">
      <v-card>
        <v-card-title>
          <span class="headline">{{ this.$trans('app.organization_setting') }}</span>
        </v-card-title>
      <v-tabs v-model="currentTab" icons-and-text grow >
        <v-tab> {{ this.$trans('app.organization_name') }}
          <v-icon>{{ icons.mdiOfficeBuildingOutline }}</v-icon>
        </v-tab>
        <v-tab> {{ this.$trans('app.address') }}
          <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
        </v-tab>
        <v-tab> TEL/FAX
          <v-icon>{{ icons.mdiPhoneOutline  }}</v-icon>
        </v-tab>
        <v-tab> {{ this.$trans('app.contract') }}
          <v-icon>{{ icons.mdiFileDocumentEditOutline  }}</v-icon>
        </v-tab>
      </v-tabs>
      <v-card-text>
        <v-tabs-items v-model="currentTab" class="px-8">
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.name" :label="labelName" dense
                  :rules="[validators.requiredField(inputVal.name, this.$trans('app.organization_name')), validators.maxLengthValidator(inputVal.name, 255, this.$trans('app.organization_name'))]"
                  :error-messages="errorMessages.organizationName" :readonly="!role()"
                >
                  <template v-slot:label>
                  {{ labelName }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.name_kana" :label="labelFurigana"
                  dense :rules="[validators.requiredField(inputVal.name_kana, this.$trans('app.organization_name_furigana')), validators.maxLengthValidator(inputVal.name_kana, 255, this.$trans('app.organization_name_furigana'))]"
                  :error-messages="errorMessages.organizationNameFurigana" :readonly="!role()"
                >
                <template v-slot:label>
                  {{ labelFurigana }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.simple_name" :label="labelNameAbbreviation"
                  dense :rules="[validators.requiredField(inputVal.simple_name, this.$trans('app.organization_abbreviation')), validators.maxLengthValidator(inputVal.simple_name, 255, this.$trans('app.organization_abbreviation'))]"
                  :error-messages="errorMessages.organizationNameAbbreviation" :readonly="!role()"
                >
                <template v-slot:label>
                  {{ labelNameAbbreviation }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.zip_code" :label="labelZipCode" dense
                  :rules="[validators.requiredField(inputVal.zip_code, this.$trans('app.zip_code')), validators.integerValidator(inputVal.zip_code, this.$trans('app.zip_code')), validators.lengthValidator(inputVal.zip_code, 7, this.$trans('app.zip_code'))]"
                  :error-messages="errorMessages.zipCode" :readonly="!role()"
                >
                <template v-slot:label>
                  {{ labelZipCode }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row class="">
              <v-col cols="12" md="6">
                <v-select hide-details="auto" v-model="inputVal.prefecture" item-value="Pref_code" item-text="Pref_name" :items="prefectureArr"  :label="labelPrefectures"
                :rules="[validators.requiredSelect(inputVal.prefecture, this.$trans('app.prefectures'))]" :readonly="!role()">
                <template v-slot:label>
                  {{ labelPrefectures }} <b class="required">*</b>
                </template>
                </v-select>
              </v-col>
              <v-col class="align-self-end mb-1" cols="12" md="6">
                <v-text-field v-model="inputVal.city" :label="labelMunicipalities"
                  dense hide-details="auto" :readonly="!role()"
                  :rules="[validators.requiredField(inputVal.city, this.$trans('app.municipalities')), validators.maxLengthValidator(inputVal.city, 15, this.$trans('app.municipalities'))]"
                  :error-messages="errorMessages.city"
                >
                <template v-slot:label>
                  {{ labelMunicipalities }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.street" :label="labelHouseNumber" dense :readonly="!role()"
                  :rules="[validators.requiredField(inputVal.street, this.$trans('app.municipalities')), validators.maxLengthValidator(inputVal.street, 127, this.$trans('app.municipalities')) ]"
                  :error-messages="errorMessages.street"
                >
                <template v-slot:label>
                  {{ labelHouseNumber }} <b class="required">*</b>
                </template>p
                </v-text-field>
              </v-col>
            </v-row>
            <v-row class="pt-3">
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="inputVal.building" :label="labelBuildingName"
                  dense :rules="[validators.maxLengthValidator(inputVal.building, 127, labelBuildingName) ]" :readonly="!role()"
                  :error-messages="errorMessages.building"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field v-model="inputVal.tel" :label="this.$trans('app.tel')" :readonly="!role()"
                  dense hide-details="auto" :rules="[validators.integerValidator(inputVal.tel, this.$trans('app.tel')), validators.maxLengthValidator(inputVal.tel, 11, this.$trans('app.tel'))]" :error-messages="errorMessages.tel"
                ></v-text-field>
              </v-col>
              <v-col  cols="12" md="6">
                <v-text-field
                  v-model="inputVal.fax" :label="this.$trans('app.fax')" dense hide-details="auto" :error-messages="errorMessages.fax" :readonly="!role()"
                  :rules="[validators.integerValidator(inputVal.fax, this.$trans('app.fax')), validators.maxLengthValidator(inputVal.fax, 11)]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col  cols="12" md="6">
                <v-text-field
                  v-model="inputVal.account_limit" :label="labelNumber_account" :readonly="!role()"
                  dense hide-details="auto" :rules="[validators.requiredField(inputVal.account_limit, this.$trans('app.number_account') ), validators.integerValidator(inputVal.account_limit, this.$trans('app.number_account')), validators.between(inputVal.account_limit, 1, 10000, this.$trans('app.number_account'))]"
                  :error-messages="errorMessages.numberAccount"
                >
                <template v-slot:label>
                  {{ labelNumber_account }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="inputVal.storage_limit" :label="labelAccountLimit" :readonly="!role()"
                  dense hide-details="auto" :rules="[validators.requiredField(inputVal.storage_limit, this.$trans('app.storage_capacity') ), validators.integerValidator(inputVal.storage_limit, this.$trans('app.storage_capacity')), validators.between(inputVal.storage_limit, 1, 500, this.$trans('app.storage_capacity'))]"
                  :error-messages="errorMessages.storageCapacity"
                >
                <template v-slot:label>
                  {{ labelAccountLimit }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch :readonly="!role()" v-model="inputVal.news_enabled" hide-details="auto" :label="this.$trans('app.notify')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch :readonly="!role()" hide-details="auto" v-model="inputVal.calendar_enabled" :label="this.$trans('app.event')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch :readonly="!role()" v-model="inputVal.safety_check_enabled" hide-details="auto" :label="this.$trans('app.safe_confirm')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch :readonly="!role()" v-model="inputVal.library_enabled" hide-details="auto" :label="this.$trans('app.archive')"></v-switch>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="justify-end">
        <v-row class="py-3 justify-end">
          <v-btn color="default" class="mx-2" @click="close">
            {{ this.$trans('app.close') }}
          </v-btn>
          <v-btn v-if="role()" color="success" class=" mx-2" type="submit">
            {{ this.$trans('app.save') }}
          </v-btn>
        </v-row>
      </v-card-actions>
      </v-card>
    </v-form>
    </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>
  <loading-component :loading="loading"></loading-component>
  </div>
</template>

<script>
import store from "@/store";
import { role } from "@/common";
import { address } from "@/common/address";
import  loadingComponent from '@/common/components/loading.vue'
import AlertComponent from '@/common/components/snackBar.vue'
import axios from '@axios'
import { requiredField, maxLengthValidator, lengthValidator, integerValidator ,requiredSelect, between, minLengthValidator} from '@core/utils/validation'
import {
  mdiPhone,
  mdiHeartOutline,
  mdiAccountBoxOutline,
  mdiAccountCircle,
  mdiOfficeBuildingOutline,
  mdiAccountSupervisorOutline,
  mdiLockOutline,
  mdiPlus,
  mdiMapMarkerOutline,
  mdiPhoneInTalkOutline,
  mdiNotebookEditOutline,
  mdiFileDocumentEditOutline,
  mdiPhoneOutline
} from '@mdi/js'

export default {
  props : {
    dialogs: {
      type : Object,
      default : { detail : false}
    },
    inputVal : { required : true, }
  },
  components: {
    loadingComponent,
    AlertComponent
  },
  data(){
    return {
      labelName : this.$trans('app.organization_name'),
      labelFurigana : this.$trans('app.organization_name_furigana'),
      labelNameAbbreviation : this.$trans('app.organization_abbreviation'),
      labelMunicipalities :this.$trans('app.municipalities'),
      labelPrefectures: this.$trans('app.prefectures'),
      labelZipCode : this.$trans('app.zip_code'),
      labelHouseNumber : this.$trans('app.house_number'),
      labelBuildingName : this.$trans('app.building_name'),
      labelNumber_account : this.$trans('app.number_account'),
      labelAccountLimit : this.$trans('app.storage_capacity') + 'GB',
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      currentTab : null,
      prefectureArr : address,
      organizationName : this.inputVal.name,
      organizationNameFurigana : this.inputVal.name_kana ?? '',
      organizationNameAbbreviation : this.inputVal.name_kana ?? '',
      zipCode : this.inputVal.zip_code ?? '',
      prefectures : this.inputVal.prefecture ?? '',
      municipalities : this.inputVal.street ?? '',
      address : this.inputVal.city ?? '',
      buildingName : this.inputVal.building ?? '',
      tel : this.inputVal.tel ?? '',
      fax : this.inputVal.fax ?? '',
      numberAccount : this.inputVal.account_limit ? this.inputVal.account_limit.toString() : '',
      storageCapacity : this.inputVal.storage_limit  ?? '',
      errorMessages : {},
      loading : false
    }
  },
  methods: {
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      // if (!isFormValid) return
      const queryParams = {
        id : this.inputVal.id,
        name : this.inputVal.name,
        simple_name : this.inputVal.name_kana,
        name_kana : this.inputVal.name_kana,
        zip_code : this.inputVal.zip_code,
        prefecture : this.inputVal.prefecture,
        city : this.inputVal.city,
        building : this.inputVal.building,
        tel : this.inputVal.tel,
        fax : this.inputVal.fax,
        street : this.inputVal.street,
        account_limit : this.inputVal.account_limit,
        storage_limit : this.inputVal.storage_limit,
        calendar_enabled : this.inputVal.calendar_enabled,
        news_enabled : this.inputVal.news_enabled,
        safety_check_enabled : this.inputVal.safety_check_enabled,
        library_enabled : this.inputVal.library_enabled,
      }
      if(!this.loading){
        this.loading = true
        store.dispatch('organization/addNew', queryParams)
        .then(response => {
          this.loading = false
          this.dialogs.detail = false
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'success'
          this.snackbar.message = trans('message.update_success')
          this.$refs.form.reset()
          this.$emit('refetch-data')
          let organizationActive = localStorage.getItem('Organization') !== null ?
            JSON.parse(localStorage.getItem('Organization')) : {}
          if(organizationActive.id === queryParams.id){
            axios.get('/menu?organization_id=' + organizationActive.id).then(response => {
              let menu = response.data
              localStorage.setItem('userMenu', JSON.stringify(menu))
              window.location.reload()
            }).catch(error => {
              this.loading = false
              this.snackbar.isColorSnackbarVisible = true
              this.snackbar.color = 'error'
              this.snackbar.message = trans('message.error')
            })
          }
        })
        .catch(error => {
          this.loading = false
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'error'
          this.snackbar.message = trans('message.error')
        })
      }
    },
    close() {
      this.dialogs.detail = false
      this.$refs.form.reset()
      this.currentTab = null
    }
  },
  setup() {
    return {
      address,role,
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect,
        between,
        minLengthValidator
      },
      icons: {
        mdiPhone,
        mdiHeartOutline,
        mdiAccountBoxOutline,
        mdiAccountCircle,
        mdiOfficeBuildingOutline,
        mdiAccountSupervisorOutline,
        mdiLockOutline,
        mdiPlus,
        mdiMapMarkerOutline,
        mdiPhoneInTalkOutline,
        mdiNotebookEditOutline,
        mdiFileDocumentEditOutline,
        mdiPhoneOutline
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 300px !important;
}
.justify-right{
  justify-content: right !important;
}
.required {
  color : red !important;
}
</style>
