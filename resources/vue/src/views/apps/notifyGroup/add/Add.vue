<template>
  <div>
  <v-dialog v-model="dialogs.addNew" max-width="800" persistent scrollable>
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
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="name" dense
                  hide-details="auto" :rules="[validators.requiredField(name, labelName) , validators.maxLengthValidator(name, 255, labelName)]"
                  :error-messages="errorMessages.name">
                <template v-slot:label>
                  {{ labelName }} <b class="required-field">*</b>
                </template>
              </v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="pt-6" :transition="false">
            <v-row >
              <v-col cols="6" md="3" class="h-full">
                <!-- Scrollable Area -->
                <perfect-scrollbar :settings="perfectScrollbarSettings" class="ps-email-left-sidebar">
                  <v-treeview class="tree-board" v-model="boardId" selectable :items="listBoard"
                  selection-type="independent"></v-treeview>
                </perfect-scrollbar>
              </v-col>
              <v-col cols="12" md="9" class="pa-5">
                <v-data-table :headers="tableUserColumns" :items="arrMergeUser" :loading="loading" :page.sync="paginate.currentPage"
                  v-model="arrUser" :items-per-page="-1" show-select  class="elevation-1" :loading-text="this.$trans('app.loading_item')"
                  :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                   @update:items-per-page="changeItemPerPage" @update:page="getUser($event, boardId)" :server-items-length="paginate.total"
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
          <v-btn color="default" class="mx-2" @click="close()"> {{ this.$trans('app.close') }} </v-btn>
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
import useStore from './useStore'
import AlertComponent from '@/common/components/snackBar.vue'
import { itemPerPage, textNoData } from '@/common'
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import pagination  from '@/common/components/Pagination.vue'
import {
  mdiAccountSupervisorOutline,mdiEmailFastOutline
} from '@mdi/js'
import loadingComponent from '@/common/components/loading.vue'
import {watch} from "@vue/composition-api";

export default {
  components: {
    AlertComponent,
    PerfectScrollbar,
    pagination,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { addNew : false}
    },
    listBoard: {required : true}
  },
  watch:{
    boardId (value) {
      if(value.length > 0){
        this.getUser(1, value)
      }
    },
    listUser() {
      this.mergeUser()
    }
  },
  data() {
    return {
      labelName: this.$trans('app.name'),
      loadingComponent : false,
      name: '',
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      currentTab: null,
      errorMessages: {},
      arrUser: [],
      arrMergeUser : [],
      boardId: []
    }
  },
  methods: {
    mergeUser () {
      if (this.arrUser) {
        let ids = new Set(this.arrUser.map(d => d.id))
        this.arrMergeUser = [...this.arrUser, ...this.listUser.filter(d => !ids.has(d.id))]
        this.paginate.total = this.arrMergeUser.length
        this.paginate.pageCount = Math.ceil(this.arrMergeUser.length / this.paginate.perPage)
      }
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
      let arrId = []
      this.arrUser.forEach(user => {
          if(user.id){
            arrId.push(user.id)
          }
        });
      const queryParams = {
        organization_id:  JSON.parse(localStorage.getItem('Organization')).id,
        name: this.name,
        user_id: arrId,
        disp_order: 1
      }
      if(!this.loadingComponent){
        this.loadingComponent = true
        store.dispatch('notifyGroup/addNew', queryParams)
        .then(response => {
          this.loadingComponent = false
          this.dialogs.addNew = false
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'success'
          this.snackbar.message = trans('message.add_success')
          this.$refs.form.reset()
          this.arrUser = []
          this.boardId = []
          this.currentTab = null
          this.$emit('refetch-data')
        })
      }
    },
    close() {
      this.arrUser = []
      this.$refs.form.reset()
      this.dialogs.addNew = false
      this.boardId = []
      this.listUser = []
      this.currentTab = null
    },
  },
  setup() {
    const {
      tableUserColumns,
      listUser,
      paginate,
      loading,
      getUser,
      getBoardTree,
      changeItemPerPage
    } = useStore()
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
      wheelSpeed: 0.7,
    }
    return {
      perfectScrollbarSettings, loading, paginate,
      tableUserColumns,listUser,itemPerPage,
      getUser,getBoardTree,textNoData, changeItemPerPage,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator
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
</style>
<style>
.tree-board{
  width: 460px;
}
.tree-board .v-treeview-node__checkbox{
  margin-left: 0px;
}
.required-field {
  color : red !important;
}
</style>
