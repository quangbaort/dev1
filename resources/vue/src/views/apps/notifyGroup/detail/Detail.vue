<template>
  <div>
    <v-dialog v-model="dialogs.detail" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
        <v-card>
          <v-card-title class="headline">{{this.$trans('app.notifyGroup_setting')}}</v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow>
            <v-tab>
              {{ this.$trans('app.name') }}
              <v-icon>{{ icons.mdiEmailFastOutline  }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans('app.notify_before') }}
              <v-icon>{{ icons.mdiAccountSupervisorOutline }}</v-icon>
            </v-tab>
          </v-tabs>
          <v-card-text>
            <v-tabs-items v-model="currentTab" >
              <v-tab-item :eager="true" class="mx-8" :transition="false">
                <v-row class="pt-6">
                  <v-col cols="12" md="6" class="my-2">
                    <v-text-field v-model="inputVal.name" dense hide-details="auto"
                      :rules="[validators.requiredField(inputVal.name, this.$trans('app.name_title')) , validators.maxLengthValidator(inputVal.name, 255, this.$trans('app.name_title'))]"
                      :error-messages="errorMessages.name">
                    <template v-slot:label>
                      {{ labelName }} <b class="required-field">*</b>
                    </template>
                </v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item :eager="true" :transition="false">
                <v-row class="pt-6">
                  <v-col  cols="6" md="3" class="my-2 h-full">
                    <!-- Scrollable Area -->
                    <perfect-scrollbar :settings="perfectScrollbarSettings" class="ps-email-left-sidebar">
                      <v-treeview class="tree-board" style="width:260px" selectable :items="listBoard"
                      v-model="boardId" selection-type="independent"></v-treeview>
                    </perfect-scrollbar>
                  </v-col>
                  <v-col cols="12" md="9" class="pa-5">
                    <v-data-table  item-key="id" :headers="tableUserColumns" :items="arrMergeUser" :loading="loading" :page.sync="paginate.currentPage"
                      v-model="arrUser" :value="inputVal.users" :items-per-page="-1" show-select  class="elevation-1" :loading-text="this.$trans('app.loading_item')"
                      :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                       @update:items-per-page="changeItemPerPage" @update:page="getUser($event, boardId, true)" :server-items-length="paginate.total"
                    >
                    <template v-slot:no-data>
                      {{ textNoData }}
                    </template>
                      <template #[`item.company`]="{item}">
                        {{ item.company.name }}
                      </template>
                    </v-data-table>
                  </v-col>
                </v-row>
              </v-tab-item>
            </v-tabs-items>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-row class="justify-end py-3">
              <v-btn color="default" class="mx-2" @click="close()">{{ this.$trans('app.close') }}</v-btn>
              <v-btn color="success" class="mx-2" type="submit">{{ this.$trans('app.save') }}</v-btn>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>
    <loading-component :loading="loadingComponent"></loading-component>
  </div>
</template>
<script>
import store from "@/store";
import { requiredField , maxLengthValidator , lengthValidator , integerValidator } from '@core/utils/validation'
import { ref } from '@vue/composition-api'
import useStore from '../add/useStore'
import AlertComponent from '@/common/components/snackBar.vue'
import { itemPerPage, textNoData } from '@/common'
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import pagination  from '@/common/components/Pagination.vue'
import {
  mdiAccountSupervisorOutline,
  mdiEmailFastOutline
} from '@mdi/js'
import loadingComponent from '@/common/components/loading.vue'

export default {
  components: {
    AlertComponent,
    PerfectScrollbar,
    pagination,
    loadingComponent,
  },
  props : {
    dialogs: {
      type : Object,
      default : { detail : false}
    },
    inputVal : { required : true },
    departmentOfUser : { required : true },
    listBoard: {required : true}
  },
  watch: {
    departmentOfUser (newVal) {
      this.boardId = newVal
    },
    boardId (value) {
      if(value.length > 0 ){
        this.getUser(1, value)
      }
    },
    inputVal(value) {
      this.listUser = value.users
      this.arrUser = value.users
    },
    listUser(value) {
      this.mergeUser()
    }
  },
  data() {
    return {
      arrMergeUser : [],
      labelName: this.$trans('app.name'),
      loadingComponent : false,
      errorMessages: {},
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      boardId : [],
      arrUser : []
    }
  },
  mounted() {
    this.mergeUser()
  },
  methods: {
    changeUser(value) {
      this.arrUser = value
    },
    mergeUser () {
      let ids = new Set(this.arrUser.map(d => d.id))
      this.arrMergeUser = [...this.arrUser, ...this.listUser.filter(d => !ids.has(d.id))]
      this.paginate.total = this.arrMergeUser.length
      this.paginate.pageCount = Math.ceil(this.arrMergeUser.length / this.paginate.perPage)
    },
    handleFormSubmit() {
      if(!this.$refs.form.validate()){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      if(this.arrUser.length <=0 ){
        this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'error'
          this.snackbar.message = trans('app.notify_before') + trans('message.required_select')
          return
      }
      const queryParams = {
        id: this.inputVal.id,
        organization_id:  this.inputVal.organization_id,
        name: this.inputVal.name,
        user_id: this.arrUser.map(item => item.id),
      }
      if(!this.loadingComponent){
        this.loadingComponent = true
      store.dispatch('notifyGroup/addNew', queryParams)
        .then(response => {
          this.loadingComponent = false
          this.dialogs.detail = false
          store.commit('setSnackbar',{
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.update_success')
          })
          this.$refs.form.reset()
          this.currentTab = null
          this.$emit('refetch-data')
        })
      }
    },
    close() {
      this.dialogs.detail= false
      this.boardId = []
      this.currentTab = null
    }
  },
  setup() {
    const currentTab = ref(null)
    const {
      listUser, paginate,
      loading, getUser,
      tableUserColumns, changeItemPerPage
    } = useStore()
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 70,
      wheelPropagation: false,
      wheelSpeed: 0.7,
    }
    return {
      currentTab,
      getUser,
      listUser,
      paginate,
      tableUserColumns,
      loading,
      itemPerPage,
      perfectScrollbarSettings,
      textNoData,
      changeItemPerPage,
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator
      },
      icons: {
        mdiEmailFastOutline,
        mdiAccountSupervisorOutline
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
.required-field {
  color : red !important;
}
</style>
