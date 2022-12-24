<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="800" persistent scrollable>
      <v-form @submit.prevent="handleFormSubmit" ref="form">
        <v-card>
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
            <v-tab :disabled="disabled">
              {{this.$trans('app.authentication')}}
              <v-icon>{{ icons.mdiLockOutline }}</v-icon>
            </v-tab>
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
                    <v-btn color="primary" @click="fetchCompany(1, sortBy)">
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
                    <v-data-table @input="changeCompany" :header-props="headerProps" single-select show-select class="elevation-1"  @update:items-per-page="changeItemPerPage(paginate.currentPage, $event, sortBy)"
                                  :items="listTable"  @update:page="fetchCompany($event, sortBy)" :loading="loading"
                                  @update:sort-by="updateSort($event)" v-model="selectCompany"
                                  @update:sort-desc="updateSortDesc($event)" :sort-desc="true"
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
              <v-tab-item class="pt-6" :transition="false" :eager="true">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" v-model="mail_address" @change="checkEmail" dense :label="this.$trans('app.mail_address') + this.$trans('app.required_valid')"
                                  :rules="[validators.requiredField(mail_address, this.$trans('app.mail_address')), validators.emailValidator]"
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
                    <v-select v-model="authorization" v-if="roles" :items="roles" item-text="name" item-value="key"
                              :rules="[validators.requiredSelect(authorization, this.$trans('app.authorization'))]">
                      <template v-slot:label>
                        {{ labelAuth }} <b class="required">*</b>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-img class="preview-image" :lazy-src="image" :src="image" v-model="url"></v-img>
                    <v-file-input accept="image/png, image/jpeg, image/bmp" class="mt-4" @change="onFileChange" v-model="avatar" :label="this.$trans('app.file_select')" ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="pt-6" :transition="false" :eager="true">
                <v-treeview style="cursor: pointer"
                            v-model="board"
                            @update:open="changeBoard"
                            return-object open-all selectable selection-type="independent"
                            :items="listBoard">
                </v-treeview>
              </v-tab-item>
              <v-tab-item class="pt-6" :transition="false" :disabled="disabled" :eager="true">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field  hide-details="auto" v-model="password" type="password" dense :label="this.$trans('app.password') + this.$trans('app.required_8_character')"
                                   :rules="!disabled ? [validators.requiredField(password, this.$trans('app.password')), validators.passwordValidator] : []"
                                   :error-messages="errorMessages.password">
                      <template v-slot:label>
                        {{ labelPassword }}  <b class="required">*</b> {{ labelCharecter }}
                      </template>
                                   </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field hide-details="auto" type="password" v-model="confirm_password" dense :label="this.$trans('app.confirm_password') + this.$trans('app.required_8_character')"
                                  :rules="!disabled ? [validators.requiredField(confirm_password, this.$trans('app.confirm_password')), validators.passwordValidator, validators.confirmedValidator(password, confirm_password)] : []"
                                  :error-messages="errorMessages.confirm_password"
                    >
                    <template v-slot:label>
                        {{ labelConfirmPassword }} <b class="required">*</b> {{ labelCharecter }}
                      </template>
                    </v-text-field>
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
    <alert-component :snackbar="snackbar"></alert-component>
    <!-- <loading-component :loading="loadingComponent"></loading-component> -->

  </div>
</template>

<script>
// import store from "@/store";
import { requiredField, required, passwordValidator, maxLengthValidator, emailValidator, lengthValidator, integerValidator, requiredSelect,confirmedValidator,confirmedValidatorEmail } from '@core/utils/validation'
import AlertComponent from '@/common/components/snackBar.vue'
import {ref, watch} from '@vue/composition-api'
import {textNoData, loadingItem, replacePrefecture, headerProps } from '@/common'
import store from '@/store'
import loadingComponent from '@/common/components/loading.vue'
import { address } from '@/common/address'
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
import { itemPerPage } from "@/common";
export default {
  components: {
    AlertComponent,
    loadingComponent,
  },
  props : {
    dialogs: {
      type : Object,
      default : { addUser : false}
    },
    companyD : {
      type : Object,
      default: () => {}
    },
    listBoard: {
      type : Array,
      default : () => []
    },
    roles: {
      type : Array,
      default : () => []
    }
  },
  data(){
    return {
      inputVal : null,
      form : null,
      labelCompany : this.$trans('app.company'),
      labelMail : this.$trans('app.mail_address'),
      labelMailConfirm : this.$trans('app.confirm_mail_address'),
      labelFurigana : this.$trans('app.furigana'),
      labelName : this.$trans('app.name'),
      labelAuth : this.$trans('app.authorization'),
      labelPassword : this.$trans('app.password'),
      labelConfirmPassword: this.$trans('app.confirm_password'),
      labelCharecter: this.$trans('app.required_8_character'),
      nameCompany : null,
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      image : '',
      avatar : null,
      url : '',
      currentTab : null,
      name : null,
      furigana: null,
      password:null,
      board: [],
      confirm_password:null,
      authorization:'',
      mail_address:null,
      confirm_mail_address: null,
      organizationName : null,
      organizationNameFurigana : null,
      organizationNameAbbreviation : null,
      errorMessages : {},
      selectCompany : [],
      errorEmail : null,
      disabled : false,
      idUser : null,
    }
  },
  watch: {
    companyD(value){
      if(value.id){
        this.selectCompany = [value]
        this.listTable = [value]
        this.nameCompany = value.name
      }
    },
  },
  methods: {
    changeCompany(value){
      if(value && value.length > 0){
        return this.nameCompany = value[0].name
      }
    },
    checkEmail(email){
      store.dispatch('users/checkEmail', {email : email }).then(response => {
        if(response.data.data){
          this.idUser = response.data.data.id
          this.disabled = true
          this.errorEmail = trans('message.email_isset')
        }
        else {
          this.disabled = false
          this.errorEmail = null
        }
      })
    },
    changeBoard(event){
    },
    changeCheckbox(event){
    },
    handleFormSubmit(){
      let isFormValid = this.$refs.form.validate()
      if (!isFormValid || this.selectCompany.length <= 0 || this.board.length <= 0){
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
      const queryParams = {
        email: this.mail_address,
        name : this.name,
        name_kana : this.furigana,
        role : this.authorization,
        avatar : this.avatar,
        company_id : arrIDCompany[0],
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        password : this.password,
        password_confirmation : this.confirm_password,
        departments : arrIDBoard
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
      // formData.append('departments',JSON.stringify(arrIDBoard))
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING', true)
        store.dispatch('users/addNew', formData)
          .then(response => {
            this.loadingComponent = false;
            this.dialogs.addNew = false
            store.commit("setSnackbar",{
              isColorSnackbarVisible: true,
              color : 'success',
              message: trans('message.add_success')
            })
            this.close()
            this.$emit('refetch-data')
            if(this.$router.currentRoute.params.method == 'addNew'){
              this.$router.push({name: 'apps-company-list', params: {method: 'addNew', company: this.companyD  }})
            }
          })
      }
    },
    close() {
      this.dialogs.addNew = false
      this.$refs.form.reset()
      this.board = []
      this.listTable = []
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
      tableColumnsCompanies,itemPerPage,
      fetchCompany,company,paginate,updateSortDesc,sortBy,
      listTable,changeItemPerPage,loading,replacePrefecture,
      updateSort,textNoData,loadingItem,role,loadingComponent,address,
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
        emailValidator,
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
.preview-image{
  height: 150px;
  width: 150px;
  border: 1px solid rgb(168, 166, 166);
}
</style>
