<template>
  <div>
    <v-dialog v-model="dialogs.profile" @input="close" max-width="800" min-height="400" persistent scrollable>
      <v-form @submit.prevent="handleFormSubmit" ref="form">
      <v-card style="height:90%;">
        <v-card-title>
          <span class="headline">{{this.$trans('app.user_setting')}}</span></v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow>
            <v-tab>
              {{this.$trans('app.company')}}
              <v-icon>{{ icons.mdiOfficeBuildingOutline }}</v-icon>
            </v-tab>
            <v-tab>
              {{this.$trans('app.basic_information')}}
              <v-icon>{{ icons.mdiAccountCircle }}</v-icon>
            </v-tab>
            <v-tab>
              {{this.$trans('app.belong')}}
              <v-icon>{{ icons.mdiHumanMaleBoard  }}</v-icon>
            </v-tab>
            <v-tab>
              {{this.$trans('app.authentication')}}
              <v-icon>{{ icons.mdiLockOutline }}</v-icon>
            </v-tab>
          </v-tabs>

          <v-card-text>
          <v-tabs-items v-model="currentTab" class="px-8">
            <v-tab-item class="pt-6" :transition="false">
              <v-row>
                <v-col cols="12">
                  <v-row>
                    <v-col cols="6" md="2">
                      <span>所属会社:</span>
                    </v-col>
                    <v-col cols="6" md="10">
                      <span>{{nameCompany}}</span>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
              <v-divider class="my-2"></v-divider>
              <v-row v-if="admin">
                <v-col class="mt-2" cols="12" md="6">
                  <v-text-field hide-detail="auto" v-model="company" dense :label="this.$trans('app.company') + this.$trans('app.required_valid')"
                    :error-messages="errorMessages.company">
                    <template v-slot:label>
                      {{ labelCompany }}
                    </template>
                    </v-text-field>
                </v-col>

                <v-col cols="12" md="3">
                  <v-btn color="primary" @click="fetchCompany">
                    <span>{{ this.$trans('app.search') }}</span>
                  </v-btn>
                </v-col>
                <v-col  cols="12" md="3" class="text-right">
                  <v-btn color="primary">
                    <router-link :to="{name : 'apps-company-list'}" style="color : #fff; text-decoration: none">
                      <v-icon>{{ icons.mdiPlus }}</v-icon>
                      <span>{{ this.$trans('app.add_new') }}</span>
                    </router-link>
                  </v-btn>
                </v-col>
              </v-row>
              <v-row class="py-1">
                <v-col cols="12" md="12">
                  <v-data-table @input="changeCompany" single-select class="elevation-1"  @update:items-per-page="changeItemPerPage"
                    :items="listTable && listTable.length > 0 ? listTable : [arrCompany]"  @update:page="fetchCompany" :loading="loading"
                    @update:sort-by="updateSort($event)" :show-select="admin" v-model="selectCompany"
                    :items-per-page="-1" item-key="id" :server-items-length.sync="paginate.total" :page.sync="paginate.currentPage"
                    :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage') }"
                    :headers="tableColumnsCompanies">
                    <template v-slot:no-data>
                      {{ textNoData }}
                    </template>
                    <template v-slot:loading>
                      {{ loadingItem }}
                    </template>
                    <template #[`item.prefecture`]="{item}">
                      {{ replacePrefecture(item.prefecture, address) }}
                    </template>
                  </v-data-table>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="pt-6" :transition="false">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field   hide-details="auto" v-model="mail_address" dense :label="this.$trans('app.mail_address') + this.$trans('app.required_valid')"
                    :rules="[validators.requiredField(mail_address, this.$trans('app.mail_address'))]"
                    :error-messages="errorMessages.mail_address" :readonly="!admin">
                    <template v-slot:label>
                      {{ labelMail }} <b class="required">*</b>
                    </template>
                  </v-text-field>
                  <p style="color : red">{{ errorEmail }}</p>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field hide-details="auto" :readonly="!admin"  v-model="confirm_mail_address" dense :label="this.$trans('app.confirm_mail_address') + this.$trans('app.required_valid')"
                    :rules="[validators.requiredField(confirm_mail_address, this.$trans('app.confirm_mail_address')), validators.confirmedValidatorEmail(mail_address,confirm_mail_address)]"
                    :error-messages="errorMessages.confirm_mail_address">
                    <template v-slot:label >
                      {{ labelMailConfirm }} <b class="required">*</b>
                    </template>
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field hide-details="auto" v-model="name" dense :label="this.$trans('app.name') + this.$trans('app.required_valid')"
                    :rules="[validators.requiredField(name, this.$trans('app.name'))]"
                    :error-messages="errorMessages.name" :readonly="!admin">
                    <template v-slot:label>
                      {{ labelName }} <b class="required">*</b>
                    </template>
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field hide-details="auto" v-model="furigana" dense :label="this.$trans('app.furigana') + this.$trans('app.required_valid')"
                    :rules="[validators.requiredField(furigana, this.$trans('app.furigana'))]"
                    :error-messages="errorMessages.furigana" :readonly="!admin">
                    <template v-slot:label>
                      {{ labelFurigana }} <b class="required">*</b>
                    </template>
                  </v-text-field>
                </v-col>
                <v-col v-model="authorization" cols="12" md="6">
                  <v-select :label="this.$trans('app.authorization')" :readonly="!admin" v-model="authorization" :items="roles" item-text="name" item-value="key" :rules="[validators.requiredSelect(authorization, this.$trans('app.authorization'))]">
                    <template v-slot:label >
                      {{ labelAuth }} <b class="required">*</b>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-img :lazy-src="image" :src="image" v-model="image"></v-img>
                  <v-file-input accept="image/png, image/jpeg, image/bmp"  class="mt-4" @change="onFileChange" v-model="avatar" :label="this.$trans('app.file_select')" ></v-file-input>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="pt-6" :transition="false" v-if="!role()">
              <v-treeview :item-disabled="!admin ? 'id' : null" style="cursor: pointer" v-model="board"  item-key="id" :multiple-active="true"  @update:open="changeBoard" return-object open-all selectable selection-type="independent" :items="listBoard">
              </v-treeview>
            </v-tab-item>
            <v-tab-item class="pt-6" :transition="false" :disabled="disabled">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field type="password" hide-details="auto" v-model="password" dense :label="this.$trans('app.password') + this.$trans('app.required_8_character')"
                      :rules="password && password.length > 0 ? [ validators.passwordValidator] : []"
                      :error-messages="errorMessages.password"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field type="password" hide-details="auto" v-model="confirm_password" dense :label="this.$trans('app.confirm_password') + this.$trans('app.required_8_character')"
                      :rules="password && password.length > 0 ? [ validators.confirmedValidator(password, confirm_password)] : []"
                      :error-messages="errorMessages.confirm_password"
                    ></v-text-field>
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
              <v-btn color="success" class="mx-2" type="submit">
                {{ this.$trans('app.save') }}
              </v-btn>
            </v-row>
          </v-card-actions>
      </v-card>
      </v-form>
    </v-dialog>
  </div>
</template>

<script>

import {passwordValidator, requiredField, required, maxLengthValidator, lengthValidator, integerValidator, requiredSelect, confirmedValidator, confirmedValidatorEmail } from '@core/utils/validation'
import AlertComponent from '@/common/components/snackBar.vue'
import {ref, watch} from '@vue/composition-api'
import { itemPerPage } from "@/common";
import {textNoData, loadingItem, replacePrefecture, role} from '@/common'
import { address } from '@/common/address'
import store from '@/store'
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
  mdiHumanMaleBoard
} from '@mdi/js'
import useStore from "./useStore";
import loadingComponent from '@/common/components/loading.vue'

export default {
  components: {
    AlertComponent,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { profile : false}
    },
    user : {
      type : Object,
      default : {}
    },
    admin : {
      type : Boolean,
      default : false
    },
    roles : {
      type : Array,
      default : []
    }
  },
  data(){
    return {
      arrCompany : {},
      form : null,
      labelCompany : this.$trans('app.company'),
      labelMail : this.$trans('app.mail_address'),
      labelMailConfirm : this.$trans('app.confirm_mail_address'),
      labelFurigana : this.$trans('app.furigana'),
      labelName : this.$trans('app.name'),
      labelAuth : this.$trans('app.authorization'),
      nameCompany : null,
      image : null,
      avatar : null,
      url : '',
      currentTab : 1,
      name : null,
      furigana: null,
      password:null,
      board: [],
      confirm_password:null,
      authorization: null,
      mail_address: null,
      confirm_mail_address: null,
      organizationName : null,
      organizationNameFurigana : null,
      organizationNameAbbreviation : null,
      errorMessages : {},
      selectCompany : null,
      errorEmail : null,
      disabled : true,
    }
  },
  watch : {
    user(val){
      if(this.user){
        this.currentTab = 1
        this.nameCompany = this.user.company ? this.user.company.name : '',
        this.name = this.user.name
        this.furigana = this.user.name_kana
        this.password = null
        this.confirm_password = null
        this.arrCompany = val.company ? val.company : {}
        this.mail_address = this.user.email
        this.confirm_mail_address = this.user.email
        this.selectCompany = [this.user.company]
        this.image = this.user.avatar_url
        this.disabled = true
        this.authorization =  val.role ? val.role.toString() : ''
        this.board = val.departments && val.departments.length > 0 ? val.departments : null
      }
    },
    roles(value){
    }
  },
  methods: {
    changeCompany(value){
      if(value.length > 0){
        return this.nameCompany = value[0].name
      }
    },
    checkEmail(email){

    },
    changeBoard(event){

    },
    changeCheckbox(event){
    },

    handleFormSubmit(){
      let isFormValid = this.$refs.form.validate()
      let arrIDCompany = this.selectCompany.map(company =>{
        return company.id
      })
      let arrIDBoard = this.board.map(board => {
        return board.id
      })
      if (!isFormValid || arrIDCompany.length <= 0 || arrIDBoard.length <= 0 || this.errorEmail !== null){
        store.commit('setSnackbar', {
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error'),
        })
        return
      }


      const queryParams = {
        id : this.user.id,
        email: this.mail_address,
        name : this.name,
        name_kana : this.furigana,
        role : this.authorization,
        avatar : this.avatar,
        company_id : arrIDCompany[0],
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        password : this.password,
        password_confirmation : this.confirm_password,
        departments : arrIDBoard,
      }
      let formData = new FormData();
      for (const key in queryParams) {
        if(queryParams[key]){
          if(key == 'departments'){
            for(let i = 0; i < arrIDBoard.length; i++){
              formData.append(`departments[]`, arrIDBoard[i])
            }
          }else {
            formData.append(key, queryParams[key])
          }
        }
      }
      if(!store.state.loading){
        this.loadingComponent = true;
        store.commit('TOGGLE_LOADING', true)
        store.dispatch('profile/addNew', formData)
        .then(response => {
          this.dialogs.profile= false
          store.commit('setSnackbar', {
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.update_success'),
          })
          this.listTable = []
          this.$emit('refetch-data')
          window.location.reload()
          store.commit('TOGGLE_LOADING', false)
        })
      }
    },
    close() {
      this.dialogs.profile= false
      this.$refs.form.reset()
      this.currentTab = null
    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image =  ''
      }
    }
  },
  setup(props) {
    const updateSort = (sort) => {
      const sortBy = sort.toString()
      fetchCompany(1, sortBy)
    }
    const loadingComponent = ref(false)
    const {
      company,
      listBoard,
      tableColumnsCompanies,
      getTree,
      fetchCompany,
      listTable,
      paginate,
      changeItemPerPage,
      loading,
      listOrganizations,organizationName,searchOrganization
    } = useStore()
    const itemSelection = ref([
      { title : trans('app.common'), value : 1 },
      { title : trans('app.admin'), value : 2 },
    ])
    watch(props.dialogs, (newValue) => {
      if(props.dialogs.profile){
        getTree()
      }
    })
    return {
      listOrganizations,organizationName,searchOrganization,
      listBoard,tableColumnsCompanies,itemPerPage,
      getTree,fetchCompany,company,paginate,
      listTable,changeItemPerPage,loading,replacePrefecture,address,
      updateSort,textNoData,loadingItem,loadingComponent,role,
      // validation rules
      validators: {
        requiredField,
        required,itemSelection,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect,
        confirmedValidator,
        confirmedValidatorEmail,
        passwordValidator
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
        mdiHumanMaleBoard
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 400px !important;
}
.justify-right{
  justify-content: right !important;
}
.selected {
    background-color: red
}
.custom-placeholder {
  padding : 1rem;
}
.v-text-field{
  padding-top: 0 !important;
}
.v-treeview-node__root{
  padding-left: 0 !important;
}
.required {
  color : red !important;
}
</style>
