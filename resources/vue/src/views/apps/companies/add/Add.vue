<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="600" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
        <v-card>
          <v-card-title>
            <span class="headline">{{ this.$trans('app.company_setting') }}</span>
          </v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow>
            <v-tab>
              {{ this.$trans('app.company_name') }}
              <v-icon>{{ icons.mdiOfficeBuildingOutline }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans('app.address') }}
              <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
            </v-tab>
            <v-tab>
              TEL/FAX
              <v-icon>{{ icons.mdiPhoneOutline }}</v-icon>
            </v-tab>
          </v-tabs>
          <v-card-text>
            <v-tabs-items v-model="currentTab" class="px-8">
              <v-tab-item :eager="true" class="pt-6" :transition="false">
                <v-row class="">
                  <v-col cols="12" md="6">
                    <v-text-field v-model="companyName" :label="this.$trans('app.company_name') + this.$trans('app.required_valid')"
                                  dense hide-details="auto" :rules="[validators.requiredField(companyName, this.$trans('app.company_name')) , validators.maxLengthValidator(companyName , 255, this.$trans('app.company_name'))]"
                                  :error-messages="errorMessages.companyName"
                    >
                      <template v-slot:label>
                        {{ labelName }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="companyNameFurigana" :label="this.$trans('app.company_name_furigana') + this.$trans('app.required_valid')"
                                  dense hide-details="auto" :rules="[validators.requiredField(companyNameFurigana, this.$trans('app.company_name_furigana')) , validators.maxLengthValidator(companyNameFurigana , 255, this.$trans('app.company_name_furigana'))]"
                                  :error-messages="errorMessages.companyNameFurigana"
                    >
                      <template v-slot:label>
                        {{ labelNameFurigana }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item :eager="true" class="pt-6" :transition="false">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="zipCode" :label="this.$trans('app.zip_code') + this.$trans('app.required_valid')" dense
                                  hide-details="auto" :rules="[validators.requiredField(zipCode, this.$trans('app.zip_code')) , validators.integerValidator(zipCode, this.$trans('app.zip_code')), validators.lengthValidator(zipCode, 7, this.$trans('app.zip_code'))]"
                                  :error-messages="errorMessages.zipCode"
                    >
                      <template v-slot:label>
                        {{ labelZipCode }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-select hide-details="auto" v-model="prefectures" item-value="Pref_code" item-text="Pref_name" :label="this.$trans('app.prefectures') + this.$trans('app.required_valid')"
                              :items="prefectureArr" :rules="[validators.requiredSelect(prefectures, this.$trans('app.prefectures'))]">
                      <template v-slot:label>
                        {{ labelPrefectures }} <b class="required">*</b>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field style="padding-top : 5px !important" v-model="city" :label="this.$trans('app.municipalities') + this.$trans('app.required_valid')"
                                  dense hide-details="auto" :rules="[validators.requiredField(city, this.$trans('app.municipalities')) , validators.maxLengthValidator(city, 15, this.$trans('app.municipalities'))]"
                                  :error-messages="errorMessages.city"
                    >
                      <template v-slot:label>
                        {{ labelMunicipalities }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="street" :label="this.$trans('app.house_number') + this.$trans('app.required_valid')"
                                  dense hide-details="auto" :rules="[validators.requiredField(street, this.$trans('app.house_number')) , validators.maxLengthValidator(street, 127, this.$trans('app.house_number')) ]"
                                  :error-messages="errorMessages.street"
                    >
                      <template v-slot:label>
                        {{ labelHouseNumber }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-2" cols="12" md="6">
                    <v-text-field v-model="buildingName" :label="this.$trans('app.building_name')"
                                  dense hide-details="auto" :rules="[validators.maxLengthValidator(buildingName, 127, this.$trans('app.building_name')) ]"
                                  :error-messages="errorMessages.buildingName"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item :eager="true" class="pt-6" :transition="false">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="tel" :label="this.$trans('app.tel')"
                                  dense hide-details="auto" :rules="[validators.integerValidator(tel, this.$trans('app.tel')), validators.maxLengthValidator(tel, 11, this.$trans('app.tel'))]"
                                  :error-messages="errorMessages.tel"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="fax" :label="this.$trans('app.fax')"
                                  dense hide-details="auto" :error-messages="errorMessages.fax" :rules="[validators.integerValidator(fax, this.$trans('app.fax')), validators.maxLengthValidator(fax, 11, this.$trans('app.fax'))]"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
            </v-tabs-items>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-row class="justify-end py-3">
              <v-btn color="default" class="mx-2" @click="close">{{ this.$trans('app.close') }}
              </v-btn>
              <v-btn color="success" class="mx-2" type="submit">{{ this.$trans('app.save') }}
              </v-btn>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
  </div>
</template>

<script>
import store from "@/store";
import {requiredField,maxLengthValidator,lengthValidator,integerValidator ,requiredSelect} from '@core/utils/validation'
import {role} from '@/common'
import { address } from '@/common/address'
import useStore from '../useStore'
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
  mdiPhoneOutline
} from '@mdi/js'

export default {
  props : {
    dialogs: {
      type : Object,
      default : { addUser : false}
    },
  },
  data(){
    return {
      loadingComponent : false,
      labelName : this.$trans('app.company_name'),
      labelNameFurigana : this.$trans('app.company_name_furigana'),
      labelMunicipalities :this.$trans('app.municipalities'),
      labelPrefectures: this.$trans('app.prefectures'),
      labelZipCode : this.$trans('app.zip_code'),
      labelHouseNumber : this.$trans('app.house_number'),
      labelBuildingName : this.$trans('app.building_name'),
      currentTab: null,
      prefectureArr: address,
      organizationID: JSON.parse(localStorage.getItem('Organization')).id,
      companyName: '',
      companyNameFurigana: '',
      zipCode: '',
      prefectures: '',
      city: '',
      street: '',
      buildingName: '',
      tel: '',
      fax: '',
      errorMessages: {},
    }
  },
  methods: {
    handleFormSubmit() {
      if(!this.$refs.form.validate()){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      const queryParams = {
        organization_id: this.organizationID,
        name: this.companyName,
        name_kana: this.companyNameFurigana,
        zip_code: this.zipCode,
        prefecture: this.prefectures,
        city: this.city,
        street: this.street,
        building: this.buildingName,
        tel: this.tel,
        fax : this.fax,
      }
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('company/addNew', queryParams)
        .then(response => {
          this.dialogs.addNew = false
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.add_success')
          })
          this.$refs.form.reset()
          this.$emit('refetch-data')
          this.currentTab = null
          store.commit('TOGGLE_LOADING' , false)
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
    const {
      listOrganizations, nameOrganization
    } = useStore()
    return {
      role, listOrganizations, nameOrganization,
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect
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
        mdiPhoneOutline
      }
    }
  },
}
</script>
<style scoped>
.v-item-group {
  min-height: 350px !important;
}

.justify-right {
  justify-content: right !important;
}
.v-text-field{
  padding-top: 0 !important;
}
.required {
  color : red !important;
}
</style>
