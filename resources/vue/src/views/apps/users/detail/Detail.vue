<template>
  <div>
    <v-dialog v-model="dialogs.detail" @input="close" max-width="800" scrollable persistent >
      <v-form @submit.prevent="handleFormSubmit" ref="form">
        <v-card style="height:90%;">
          <v-card-title>
            <span class="headline">{{this.$trans('app.user_setting')}}</span>
          </v-card-title>
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
            <!-- <v-tab :disabled="disabled">
              {{this.$trans('app.authentication')}}
              <v-icon>{{ icons.mdiLockOutline }}</v-icon>
            </v-tab> -->
          </v-tabs>
          <v-card-text>
            <v-tabs-items v-model="currentTab" class="px-8">
              <v-tab-item class="pt-6" :transition="false" :eager="true">
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
                <v-row>
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
                    <v-data-table @input="changeCompany" :header-props="headerProps" single-select show-select class="elevation-1"  @update:items-per-page="changeItemPerPage($event, sortBy)"
                                  :items="listTable && listTable.length > 0 ? listTable : selectCompany"  @update:page="fetchCompany($event, sortBy)" :loading="loading"
                                  @update:sort-by="updateSort($event)" v-model="selectCompany"
                                  @update:sort-desc="updateSortDesc($event)"
                                  :sort-desc="true"
                                  :items-per-page="-1" item-key="id" :server-items-length.sync="paginate.total" :page.sync="paginate.currentPage"
                                  :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage') }"
                                  :headers="tableColumnsCompanies" >
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
              <v-tab-item class="pt-6" :transition="false" :eager="true">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" v-model="mail_address" dense :label="this.$trans('app.mail_address') + this.$trans('app.required_valid')"
                                  :rules="[validators.requiredField(mail_address, this.$trans('app.mail_address'))]"
                                  :error-messages="errorMessages.mail_address">
                      <template v-slot:label>
                        {{ labelMail }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                    <p style="color : red">{{ errorEmail }}</p>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" v-model="confirm_mail_address" dense :label="this.$trans('app.confirm_mail_address') + this.$trans('app.required_valid')"
                                  :rules="[validators.requiredField(confirm_mail_address, this.$trans('app.confirm_mail_address')), validators.confirmedValidatorEmail(mail_address,confirm_mail_address)]"
                                  :error-messages="errorMessages.confirm_mail_address">
                      <template v-slot:label>
                        {{ labelMailConfirm }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" v-model="name" dense :label="this.$trans('app.name') + this.$trans('app.required_valid')"
                                  :rules="[validators.requiredField(name, this.$trans('app.name'))]"
                                  :error-messages="errorMessages.name">
                      <template v-slot:label>
                        {{ labelName }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" v-model="furigana" dense :label="this.$trans('app.furigana') + this.$trans('app.required_valid')"
                                  :rules="[validators.requiredField(furigana, this.$trans('app.furigana'))]"
                                  :error-messages="errorMessages.furigana">
                      <template v-slot:label>
                        {{ labelFurigana }} <b class="required">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col v-model="authorization" cols="12" md="6">
                    <v-select v-if="roles" :label="this.$trans('app.authorization')" v-model="authorization" :items="roles" item-text="name" item-value="key" :rules="[validators.requiredSelect(authorization, this.$trans('app.authorization'))]">
                      <template v-slot:label>
                        {{ labelAuth }} <b class="required">*</b>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-img class="preview-image" :lazy-src="image" :src="image" v-model="image"></v-img>
                    <v-file-input accept="image/png, image/jpeg, image/bmp"  class="mt-4" @change="onFileChange" v-model="avatar" :label="this.$trans('app.file_select')" ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="pt-6" :transition="false" :eager="true">
                <v-treeview   style="cursor: pointer" :value="board" id-key="id" v-model="board" return-object  selectable open-on-click  selection-type="independent" :items="listBoard">
                </v-treeview>
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
    <alert-component :snackbar="snackbar"></alert-component>
    <!-- <loading-component :loading="loadingComponent"></loading-component> -->
  </div>
</template>

<script>
// import store from "@/store";
import { requiredField, required, maxLengthValidator, lengthValidator, integerValidator, requiredSelect, confirmedValidator, confirmedValidatorEmail } from '@core/utils/validation'
import AlertComponent from '@/common/components/snackBar.vue'
import {ref} from '@vue/composition-api'
import {textNoData, loadingItem, replacePrefecture } from '@/common'
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
import { itemPerPage, headerProps } from "@/common";
import loadingComponent from '@/common/components/loading.vue'
export default {
  components: {
    AlertComponent,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { addUser : false}
    },
    user : {
      type : Object,
      default : {}
    },
    departmentsIDs : {
      type : Array,
      default : []
    },
    listBoard: {
      type : Array,
      default : []
    },
    roles: {
      type : Array,
      default : []
    }
  },
  watch: {
    user(){
      if(this.user){
        this.selectCompany = [this.user.company]
        this.name = this.user.name
        this.furigana = this.user.name_kana
        this.board = this.user.departments
        this.image = this.user.avatar_url
        this.mail_address = this.user.email
        this.authorization = this.user.role.toString()
        this.confirm_mail_address = this.user.email
        this.nameCompany = this.user.company.name
        this.oldAvatar = this.user.avatar_url
        this.companies = this.user.companies
        this.company = this.user.company.name
        if(this.user.avatar_url){
          let myBlob = new Blob()
          this.avatar = this.blobToFile(myBlob, this.user.avatar_name)
        }
        else {
          this.avatar = null
        }
      }
    }
  },
  data(){
    return {
      form : null,
      labelCompany : this.$trans('app.company'),
      labelMail : this.$trans('app.mail_address'),
      labelMailConfirm : this.$trans('app.confirm_mail_address'),
      labelFurigana : this.$trans('app.furigana'),
      labelName : this.$trans('app.name'),
      labelAuth : this.$trans('app.authorization'),
      nameCompany : null,
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      image : this.user ? this.user.avatar_url : null,
      avatar : null,
      oldAvatar: null,
      url : '',
      currentTab : null,
      name : this.user ? this.user.name : null,
      furigana: this.user ? this.user.name_kana :null,
      password:null,
      board: [],
      confirm_password:null,
      authorization:  this.user ?  this.user.roles[0].role.toString() : null,
      mail_address: this.user ?  this.user.email : null,
      confirm_mail_address: this.user ?  this.user.email : null,
      organizationName : null,
      organizationNameFurigana : null,
      organizationNameAbbreviation : null,
      errorMessages : {},
      selectCompany :this.user ?  this.user.company :null,
      errorEmail : null,
      disabled : true,
      departments : [],
      companies : null,
    }
  },
  methods: {
    changeCompany(value){
      if(value.length > 0){
        return this.nameCompany = value[0].name
      }
    },
    updateAll(){
      return true
    },
    blobToFile(theBlob, fileName){
      theBlob.lastModifiedDate = new Date();
      theBlob.name = fileName;
      return theBlob;
    },
    getCompany(item = null){
      let arr = []
      if(item != null) return []
      if(this.listTable !== undefined){
        if(this.listTable.length > 0 ){
          return this.listTable
        }
      }else {
        if(this.user !== undefined){
          if(this.user.company !== undefined){
            arr.push(this.user.company)
            return arr
          }
        }
      }
      return []
    },
    checkEmail(email){
      store.dispatch('users/checkEmail', {email : email }).then(response => {
        if(Object.keys(response.data.data).length > 0){
          this.errorEmail = this.$trans('message.email_isset')
          this.disabled = true
        }
        else if(response.data.data.length <= 0) {
          this.errorEmail = null
          this.disabled = false
        }
      }).catch(error => {
      })
    },
    changeBoard(event){
    },
    changeCheckbox(event){
    },
    handleFormSubmit(){
      let isFormValid = this.$refs.form.validate()
      if (!isFormValid || this.selectCompany.length <= 0 || this.board.length <= 0 || this.errorEmail !== null){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      let arrIDBoard = this.board.map(board => {
        return board.id
      })
      let arrIDCompany = this.selectCompany.map(company =>{
        return company.id
      })
      if(this.oldAvatar){
        this.avatar = null
      }
      const queryParams = {
        id : this.user.id,
        email: this.mail_address,
        name : this.name,
        name_kana : this.furigana,
        role : this.authorization,
        avatar : this.avatar,
        old_avatar : this.oldAvatar,
        company_id : arrIDCompany[0],
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        departments : arrIDBoard,
      }
      let formData = new FormData();
      for (const key in queryParams) {
        if(queryParams[key]){
          if(key == 'departments'){
            for(let i = 0; i < arrIDBoard.length; i++){
              formData.append(`departments[]`, arrIDBoard[i])
            }
          } else {
            formData.append(key, queryParams[key])
          }
        }
      }
      // formData.append('departments',JSON.stringify(arrIDBoard))
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING', true)
        store.dispatch('users/addNew', formData)
          .then(response => {
            store.commit('TOGGLE_LOADING', false)
            this.dialogs.detail= false
            store.commit("setSnackbar",{
              isColorSnackbarVisible: true,
              color : 'success',
              message: trans('message.update_success')
            })
            if(this.$router.currentRoute.params.method == 'detail'){
              this.$router.push({name: 'apps-company-list', params: {method: 'addNew', company: this.user.company  }})
            }
            this.listTable = []
            this.$refs.form.reset()
            this.getCompany('reset')
            this.currentTab = null
            this.$emit('refetch-data')
          })
      }
    },
    close() {
      this.dialogs.detail = false
      this.$refs.form.reset()
      this.board = []
      this.selectCompany = []
      this.companyD = {}
      this.currentTab = null
      this.nameCompany = ''
    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image =  ''
      }
      this.oldAvatar = null
    }
  },
  setup() {
    const sortBy = ref('')
    const updateSort = (sort) => {
      sortBy.value = sort.toString()
    }
    const updateSortDesc = (event) => {
      if(event){
        sortBy.value = '-'+sortBy.value
      }
      fetchCompany(1, sortBy.value)
    }
    const loadingComponent = ref(false)
    const {
      company,
      tableColumnsCompanies,
      getTree,
      fetchCompany,
      listTable,
      paginate,
      changeItemPerPage,
      loading,
      role,listOrganizations,organizationName,searchOrganization
    } = useStore()
    const itemSelection = ref([
      { title : trans('app.common'), value : 1 },
      { title : trans('app.admin'), value : 2 },
    ])
    return {
      listOrganizations,organizationName,searchOrganization,
      tableColumnsCompanies,itemPerPage,updateSortDesc,sortBy,
      getTree,fetchCompany,company,paginate,
      listTable,changeItemPerPage,loading,replacePrefecture,address,
      updateSort,textNoData,loadingItem,role,loadingComponent,
      headerProps,
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
.preview-image{
  height: 150px;
  width: 150px;
  border: 1px solid rgb(168, 166, 166);
}
</style>
