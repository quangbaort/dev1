<template>
  <div>
    <v-dialog v-model="dialogs.addNew" @input="close" max-width="600" persistent scrollable>
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
          <v-icon>{{ icons.mdiPhoneOutline   }}</v-icon>
        </v-tab>
        <v-tab> {{ this.$trans('app.contract') }}
          <v-icon>{{ icons.mdiFileDocumentEditOutline  }}</v-icon>
        </v-tab>
      </v-tabs>
      <v-card-text>
        <v-tabs-items v-model="currentTab" class="px-8">
          <v-tab-item :eager="true" class="pt-6"  :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="organizationName" dense :label="labelName"
                  :rules="[validators.requiredField(organizationName, this.$trans('app.organization_name')), validators.maxLengthValidator(organizationName , 255, this.$trans('app.organization_name'))]"
                  :error-messages="errorMessages.organizationName"
                >
                  <template v-slot:label>
                  {{ labelName }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="organizationNameFurigana" :label="labelFurigana"
                  dense :rules="[validators.requiredField(organizationNameFurigana, this.$trans('app.organization_name_furigana')), validators.maxLengthValidator(organizationNameFurigana, 255, this.$trans('app.organization_name_furigana'))]"
                  :error-messages="errorMessages.organizationNameFurigana"
                >
                <template v-slot:label>
                  {{ labelFurigana }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6" class="">
                <v-text-field hide-details="auto" v-model="organizationNameAbbreviation" :label="labelNameAbbreviation"
                  dense :rules="[validators.requiredField(organizationNameAbbreviation, this.$trans('app.organization_abbreviation') ), validators.maxLengthValidator(organizationNameAbbreviation, 255, this.$trans('app.organization_abbreviation'))]"
                  :error-messages="errorMessages.organizationNameAbbreviation">
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
                <v-text-field hide-details="auto"  v-model="zipCode" :label="labelZipCode" dense
                  :rules="[validators.requiredField(zipCode ,this.$trans('app.zip_code') ), validators.integerValidator(zipCode ,this.$trans('app.zip_code')), validators.lengthValidator(zipCode, 7, this.$trans('app.zip_code'))]"
                  :error-messages="errorMessages.zipCode"
                >
                <template v-slot:label>
                  {{ labelZipCode }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row class="">
              <v-col cols="12" md="6">
                <v-select hide-details="auto" v-model="prefectures" :items="prefectureArr" item-text="Pref_name" item-value="Pref_code" :label="labelPrefectures"
                :rules="[validators.requiredSelect(prefectures ,this.$trans('app.prefectures'))]">
                <template v-slot:label>
                  {{ labelPrefectures }} <b class="required">*</b>
                </template>
                </v-select>
              </v-col>
              <v-col class="align-self-end" cols="12" md="6">
                <v-text-field v-model="municipalities" :label="labelMunicipalities"
                  dense hide-details="auto"
                  :rules="[validators.requiredField(municipalities, this.$trans('app.municipalities')), validators.maxLengthValidator(municipalities, 15, this.$trans('app.municipalities'))]"
                  :error-messages="errorMessages.municipalities"
                >
                <template v-slot:label>
                  {{ labelMunicipalities }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field hide-details="auto" v-model="address" :label="labelHouseNumber" dense
                  :rules="[validators.requiredField(address, this.$trans('app.address')), validators.maxLengthValidator(address, 127, this.$trans('app.address')) ]"
                  :error-messages="errorMessages.address"
                >
                <template v-slot:label>
                  {{ labelHouseNumber }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field hide-details="auto" v-model="buildingName" :label="labelBuildingName"
                  dense :rules="[validators.maxLengthValidator(buildingName, 127, labelBuildingName) ]"
                  :error-messages="errorMessages.buildingName"
                >
                </v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field v-model="tel" :label="this.$trans('app.tel')"
                  dense hide-details="auto" :rules="[ validators.integerValidator(tel, this.$trans('app.tel')), validators.maxLengthValidator(tel, 11, this.$trans('app.tel'))]"
                  :error-messages="errorMessages.tel"
                >
                </v-text-field>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-text-field v-model="fax" :label="this.$trans('app.fax')" dense hide-details="auto"
                  :error-messages="errorMessages.fax"
                  :rules="[validators.integerValidator(fax, this.$trans('app.fax')), validators.maxLengthValidator(fax, 11, this.$trans('app.fax'))]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col class="" cols="12" md="6">
                <v-text-field v-model="numberAccount" :label="labelNumber_account"
                  dense hide-details="auto" :rules="[validators.requiredField(numberAccount , this.$trans('app.number_account') ), validators.integerValidator(numberAccount, this.$trans('app.number_account')) , validators.between(numberAccount, 1, 10000, this.$trans('app.number_account'))]"
                  :error-messages="errorMessages.numberAccount"
                >
                <template v-slot:label>
                  {{ labelNumber_account }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-text-field v-model="storageCapacity" :label="labelAccountLimit"
                  dense hide-details="auto" :rules="[validators.requiredField(storageCapacity , this.$trans('app.storage_capacity') ), validators.integerValidator(storageCapacity, this.$trans('app.storage_capacity')) ,validators.between(storageCapacity, 1, 500, this.$trans('app.storage_capacity'))]"
                  :error-messages="errorMessages.storageCapacity"
                >
                <template v-slot:label>
                  {{ labelAccountLimit }} <b class="required">*</b>
                </template>
                </v-text-field>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch hide-details="auto" v-model="newsEnabled" :label="this.$trans('app.notify')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch hide-details="auto" v-model="calendarEnabled" :label="this.$trans('app.event')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch hide-details="auto" v-model="safetyCheckEnabled" :label="this.$trans('app.safe_confirm')"></v-switch>
              </v-col>
              <v-col class="" cols="12" md="6">
                <v-switch hide-details="auto" v-model="libraryEnabled" :label="this.$trans('app.archive')"></v-switch>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="py-3 justify-end">
          <v-btn color="default" class="mx-2" @click="close">
            {{ this.$trans('app.close') }}
          </v-btn>
          <v-btn color="success" class=" mx-2" type="submit">
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
import { requiredField , maxLengthValidator , lengthValidator , integerValidator , requiredSelect ,between } from '@core/utils/validation'
import AlertComponent from '@/common/components/snackBar.vue'
import { address } from '@/common/address'
import  loadingComponent from '@/common/components/loading.vue'
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
  components: {
    AlertComponent,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { addNew : false}
    },
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
      organizationName : '',
      organizationNameFurigana : '',
      organizationNameAbbreviation : '',
      zipCode : '',
      prefectures : null,
      municipalities : '',
      address : '',
      buildingName : '',
      tel : '',
      fax : '',
      numberAccount : '',
      storageCapacity : '',
      errorMessages : {},
      loading : false,
      calendarEnabled : 0,
      newsEnabled :0,
      safetyCheckEnabled : 0,
      libraryEnabled : 0,
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
        name : this.organizationName,
        simple_name : this.organizationNameFurigana,
        name_kana : this.organizationNameAbbreviation,
        zip_code : this.zipCode,
        prefecture : this.prefectures,
        city : this.municipalities,
        street : this.address,
        building : this.buildingName,
        tel : this.tel,
        fax : this.fax,
        account_limit : this.numberAccount,
        storage_limit : this.storageCapacity,
        calendar_enabled : this.calendarEnabled,
        news_enabled : this.newsEnabled,
        safety_check_enabled : this.safetyCheckEnabled,
        library_enabled : this.libraryEnabled,
      }
      if(!this.loading){
        this.loading = true
        store.dispatch('organization/addNew' , queryParams)
          .then(response => {
            this.loading = false
            this.dialogs.addNew = false
            this.snackbar.isColorSnackbarVisible = true
            this.snackbar.color = 'success'
            this.snackbar.message = trans('message.update_success')
            this.$refs.form.reset()
            this.$emit('refetch-data')
            window.location.reload()
          })
          .catch(error => {
            this.loading = false
            this.snackbar.isColorSnackbarVisible = true
            this.snackbar.color = 'error'
            this.snackbar.message = trans('message.cant_delete', {':item' : trans('app.organization')})
          })
      }
    },
    close() {
      this.dialogs.addNew = false
      this.$refs.form.reset()
      this.currentTab = null
    }
  },
  setup() {
    return {
      address,
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect,
        between
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
.v-list-item{
  padding: 0 16px !important;
}
.v-text-field{
  padding-top: 0 !important;
}
.required {
  color : red !important;
}
</style>
