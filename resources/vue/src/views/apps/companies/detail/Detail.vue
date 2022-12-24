<template>
  <div>
    <v-dialog v-model="dialogs.detail" @input="close" max-width="800" persistent scrollable>
    <v-form ref="form" @submit.prevent="handleFormSubmit">
      <v-card>
        <v-card-title>
          <span class="headline">{{ this.$trans('app.company_setting') }}</span>
        </v-card-title>
      <v-tabs v-model="currentTab" icons-and-text grow >
        <v-tab> {{ this.$trans('app.company_name') }}
          <v-icon>{{ icons.mdiOfficeBuildingOutline }}</v-icon>
        </v-tab>
        <v-tab> {{ this.$trans('app.address') }}
          <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
        </v-tab>
        <v-tab> TEL/FAX
          <v-icon>{{ icons.mdiPhoneInTalkOutline  }}</v-icon>
        </v-tab>
        <v-tab @change="getUserCompany"> {{ this.$trans('app.user') }}
          <v-icon>{{ icons.mdiAccountSupervisorOutline }}</v-icon>
        </v-tab>
      </v-tabs>
      <v-card-text>
        <v-tabs-items v-model="currentTab" class="mx-6">
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="name" :label="this.$trans('app.company_name') + this.$trans('app.required_valid')" dense
                              :rules="[validators.requiredField(name, this.$trans('app.company_name')) , validators.maxLengthValidator(name, 255, this.$trans('app.company_name'))]"
                              :error-messages="errorMessages.name"
                >
                  <template v-slot:label>
                    {{ labelName }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="name_kana" :label="this.$trans('app.company_name_furigana') + this.$trans('app.required_valid')"
                              dense :rules="[validators.requiredField(name_kana, this.$trans('app.company_name_furigana')) , validators.maxLengthValidator(name_kana, 255, this.$trans('app.company_name_furigana'))]"
                              :error-messages="errorMessages.name_kana"
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
                <v-text-field hide-details="auto" v-model="zip_code" :label="this.$trans('app.zip_code') + this.$trans('app.required_valid')" dense
                              :rules="[validators.requiredField(zip_code, this.$trans('app.zip_code')) ,  validators.integerValidator(zip_code, this.$trans('app.zip_code')), validators.lengthValidator(zip_code, 7, this.$trans('app.zip_code'))]"
                              :error-messages="errorMessages.zip_code"
                >
                  <template v-slot:label>
                    {{ labelZipCode }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-select hide-details="auto" v-model="prefecture" item-text="Pref_name" item-value="Pref_code" :items="prefectureArr" :label="this.$trans('app.prefectures') + this.$trans('app.required_valid')"
                          :rules="[validators.requiredField(prefecture, this.$trans('app.prefectures')) , validators.maxLengthValidator(prefectures, 15, this.$trans('app.prefectures'))]">
                  <template v-slot:label>
                    {{ labelPrefectures }} <b class="required">*</b>
                  </template>
                </v-select>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" style="padding-top : 5px !important" v-model="city" :label="this.$trans('app.municipalities') + this.$trans('app.required_valid')"
                              dense :rules="[validators.requiredField(city, this.$trans('app.municipalities')) , validators.maxLengthValidator(city, 15, this.$trans('app.municipalities'))]"
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
                <v-text-field hide-details="auto" v-model="street" :label="this.$trans('app.house_number') + this.$trans('app.required_valid')" dense
                              :rules="[validators.requiredField(street, this.$trans('app.house_number')) , validators.maxLengthValidator(street, 127, this.$trans('app.house_number')) ]"
                              :error-messages="errorMessages.address"
                >
                  <template v-slot:label>
                    {{ labelHouseNumber }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="building" :label="this.$trans('app.building_name')"
                              :rules="[validators.maxLengthValidator(building, 127, this.$trans('app.building_name'))]"
                              :error-messages="errorMessages.building"
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
                              dense hide-details="auto" :error-messages="errorMessages.fax"
                              :rules="[validators.integerValidator(fax, this.$trans('app.fax')), validators.maxLengthValidator(fax, 11, this.$trans('app.fax'))]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="nameUser" @change="search" @keyup.enter="search()"  :label="'名前'" outlined dense hide-details="auto">
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-btn color="primary"
                       @click="$router.push({name : 'apps-user-list', params: {method: 'addNew', company : company}})">
                  <v-icon>{{ icons.mdiPlus }}</v-icon>
                  <span>{{ this.$trans('app.add_new') }}</span>
                </v-btn>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="12" cols="12" class="pa-4">
                <v-data-table
                  :header-props="headerProps"
                  :items="user"
                  :headers="headers"
                  class="elevation-1"
                  :no-results-text="textNoData"
                  @click:row="clickRow"
                  :server-items-length="paginate.total"
                  @update:sort-desc="updateSortDesc($event)" :sort-desc="true"
                  @update:sort-by="updateSort($event)"
                  @update:items-per-page="getUserCompany(paginate.current_page ? paginate.current_page : 1, $event)"
                  @update:page="getUserCompany($event, paginate.item_per_page ? paginate.item_per_page : itemPerPage[0])"
                  :items-per-page="paginate.itemsPerPage"
                  :loading-text="this.$trans('app.loading_item')"
                  :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage') }"
                >
                  <template v-slot:no-data>
                    {{textNoData}}
                  </template>
                  <template #[`item.departments`]="{item}">
                    <span class="d-block" v-for="department in item.departments" :key="department.id">
                      {{ department.name }}
                    </span>
                  </template>
                  <template #[`item.actions`]="{item}">
                    <v-menu bottom left>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn icon v-bind="attrs" v-on="on">
                          <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item link>
                          <v-list-item-title @click="$router.push({name: 'apps-user-list', params:{method: 'detail', user: item}})">
                            <v-icon size="20" class="me-2">
                              {{ icons.mdiFileDocumentOutline }}
                            </v-icon>
                            <span>{{ listAction.detail }}</span>
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item link @click="onDialog(item)">
                          <v-list-item-title>
                            <v-icon size="20" class="me-2">
                              {{ icons.mdiDeleteOutline }}
                            </v-icon>
                            <span>{{listAction.delete}}</span>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="justify-right">
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
    <confirm-component :dialogs="dialogsUser" :onDelete="onDelete" @close="dialogsUser.delete = false" :module="this.$trans('app.user')"></confirm-component>
  </div>
</template>

<script>
import store from "@/store";
import { address } from '@/common/address'
import { ref } from '@vue/composition-api'
import { listAction, textNoData, itemPerPage, headerProps } from '@/common'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import { requiredField , maxLengthValidator , lengthValidator , integerValidator } from '@core/utils/validation'
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
  mdiDotsVertical,
  mdiDeleteOutline,
  mdiFileDocumentOutline,
} from '@mdi/js'

export default {
  props : {
    dialogs: {
      type : Object,
      default : { detail : false}
    },
    inputVal : { type: Object, default: () => ({}) },
    company : { type: Object, default: () => ({}) },
  },
  components : {
    'confirm-component' : ConfirmComponent,
  },
  created(){
    if(this.$router.currentRoute.params.method === 'addNew'){
      this.getUserCompany(1, this.paginate.item_per_page ? this.paginate.item_per_page : itemPerPage[0])
      this.currentTab = 3
    }
  },
  data(){
    return {
      dialogsUser : {
        dialog : false
      },
      loadingComponent : false,
      labelName : this.$trans('app.company_name'),
      labelNameFurigana : this.$trans('app.company_name_furigana'),
      labelMunicipalities :this.$trans('app.municipalities'),
      labelPrefectures: this.$trans('app.prefectures'),
      labelZipCode : this.$trans('app.zip_code'),
      labelHouseNumber : this.$trans('app.house_number'),
      labelBuildingName : this.$trans('app.building_name'),
      name: null,
      name_kana: null,
      zip_code: null,
      user: [],
      currentTab : null,
      prefectureArr : address,
      organizationName: null,
      organizationNameFurigana: null,
      zipCode: null,
      prefectures: null,
      city: null,
      street: null,
      building: null,
      tel : null,
      fax : null,
      prefecture: null,
      organizationID: JSON.parse(localStorage.getItem('Organization')).id,
      errorMessages : {},
      paginate : {},
      sortBy : '',
      UserId : null,
    }
  },
  watch: {
    company(value){
      this.name = value.name
      this.name_kana = value.name_kana
      this.zip_code = value.zip_code
      this.prefecture = value.prefecture
      this.city = value.city
      this.street = value.street
      this.building = value.building
      this.tel = value.tel
      this.fax = value.fax

    }
  },
  methods: {
    getUserCompany(page = 1, perPage = itemPerPage[0]){
      this.loadingComponent = true
      store.dispatch('company/getUserCompany', {
        company_id : this.company.id,
        limit: this.paginate.itemsPerPage ?? perPage,
        page : this.paginate.currentPage ?? page,
        name : this.nameUser,
        sort : this.sortBy
      }).then((response) => {
        const { data, meta } = response.data
        this.user = data
        this.paginate  = {
          total : meta.total, currentPage : parseInt(meta.current_page),
          itemsPerPage : parseInt(meta.per_page),pageStart : meta.from, pageStop : meta.last_page,
          pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),itemsLength : meta.last_page
        }
        this.loadingComponent = false
      })
    },
    clickRow(item){
      this.$router.push({ name : 'apps-user-list', params : {method: 'detail', user: item }})
    },
    onDialog(item){
      this.dialogsUser.dialog = true
      this.UserId = item.id
    },
    onDelete(){
      store.commit('TOGGLE_LOADING', true)
        store.dispatch('company/deleteUser', { id : this.UserId })
          .then(response => {
            store.commit("setSnackbar",{
              isColorSnackbarVisible: true,
              color : 'success',
              message: trans('message.delete_success')
            })
            this.getUserCompany()
            this.dialogsUser.dialog = false
            store.commit('TOGGLE_LOADING', false)
          })
    },
    searchNameUser(nameUser){
      store.dispatch('company/fetchAll', {
        name_user : nameUser
      }).then(response => {
      })
    },
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      const queryParams = {
        id : this.company.id,
        organization_id: this.organizationID,
        name : this.name,
        name_kana : this.name_kana,
        zip_code : this.zip_code,
        prefecture : this.prefecture,
        city : this.city,
        street : this.street,
        building : this.building,
        tel : this.tel,
        fax : this.fax
      }
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('company/addNew' , queryParams)
        .then(response => {
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message :trans('message.update_success')
          })
          this.dialogs.detail = false
          this.$refs.form.reset()
          this.currentTab = null
          this.$emit('refetch-data')
          store.commit('TOGGLE_LOADING' , false)
        })
      }

    },
    close() {
      this.dialogs.detail = false
      this.currentTab = null
    },
    search(event){
      this.getUserCompany()
    },
    updateSort(sort){
      this.sortBy = sort.toString()
    },
    updateSortDesc(event){
      if(event){
        this.sortBy  = '-'+this.sortBy
      }
      this.getUserCompany(1, itemPerPage[0])
    }
  },
  setup() {
    const nameUser = ref('')
    const headers = ref([
      {text : '所属', sortable : true, value : 'departments'},
      {text : '名前', sortable : false, value : 'name'},
      {text : 'メールアドレス', sortable : false, value : 'email'},
      {
        text: '',
        value: 'actions',
        align: 'right',
        sortable: false,
      },
    ])
    return {
      itemPerPage,
      address,
      headers,
      listAction,
      nameUser,
      textNoData,
      headerProps,
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator
      },
      icons: {
        mdiDotsVertical,
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
        mdiDeleteOutline,
        mdiFileDocumentOutline
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 350px !important;
}
.justify-right{
  justify-content: right !important;
}
.v-text-field{
  padding-top: 0 !important;
}
.required {
  color : red !important;
}
</style>
