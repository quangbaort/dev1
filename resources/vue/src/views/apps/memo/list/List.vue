<template>
<div>
	<v-card>
    <action-component :listHandel="listHandel" :dialogs="dialogs" @changeAction="changeAction"
                      :executionAction="executionAction" :disableAction="disableAction"></action-component>
		<v-divider class="my-2 mx-3"></v-divider>
    <v-row class="ma-0">
      <v-col sm="2">
        <v-text-field @change="search" @keyup.enter="search(titleSearch)" v-model="titleSearch" :label="this.$trans('app.title')" outlined dense hide-details>
        </v-text-field>
      </v-col>
      <v-spacer></v-spacer>
    </v-row>
    <v-row class="ma-0">
      <v-col cols="12" md="12" sm="12">
        <v-data-table v-model="selectedMemo" :header-props="headerProps"	:headers="tableColumns"	:items="listTable" :loading="loading" :page.sync="paginate.currentPage"
          :items-per-page="-1" :server-items-length="paginate.total" @update:sort-by="updateSort($event)" @click:row="onDetail"
           :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage')}" @update:items-per-page="changeItemPerPage($event)"
            show-select class="elevation-1" @update:page="fetchAll($event)" :loading-text="this.$trans('app.loading_item')"
                      @update:sort-desc="updateSortDesc($event)" :sort-desc="true">
          <template v-slot:no-data>
            {{ textNoData }}
          </template>
          <template #[`item.start`]="{item}">
             <span>{{formatTimeToDate(item.start)}}</span>
           </template>
           <template #[`item.end`]="{item}">
             <span>{{formatTimeToDate(item.end)}}</span>
           </template>
          <!-- actions -->
          <template #[`item.actions`]="{item}">
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on">
                  <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                </v-btn>
              </template>

              <v-list>
                <v-list-item  @click="onDetail(item)">
                  <v-list-item-title>
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
	</v-card>
  <add-memo :dialogs="dialogs" @refetch-data="fetchAll"></add-memo>
  <detail-memo :inputVal="memo" @refetch-data="fetchAll" :dialogs="dialogs"></detail-memo>
  <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.memo')"></confirm-component>
  <alert-component :snackbar="snackbar"></alert-component>
  <loading-component :loading="loadingComponent"></loading-component>
</div>
</template>
<script>
import {
  mdiSquareEditOutline,
  mdiDotsVertical,
  mdiPlus,
  mdiFileDocumentOutline,
  mdiDeleteOutline,
  mdiExportVariant,
  mdiAccountOutline,
  mdiExport,
} from '@mdi/js'
import store from '@/store'
import { onUnmounted, ref } from '@vue/composition-api'
import StoreModule from '../StoreModule'
import useStore from './useStore'
import { avatarText } from '@core/utils/filter'
import selectHandel from '@/common/components/selectHandel.vue'
import { itemPerPage , registerModule , formatTimeToDate , textNoData, headerProps } from '@/common'
import router from '@/router'
import AlertComponent from '@/common/components/snackBar.vue'
import addMemo from '@/views/apps/memo/add/Add.vue'
import detailMemo from '@/views/apps/memo/detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import ActionComponent from '@/common/components/ActionComponent.vue'
import loadingComponent from '@/common/components/loading.vue'
export default {
	components : {
		selectHandel,
    AlertComponent,
    addMemo,
    detailMemo,
    ConfirmComponent,
    ActionComponent,
    loadingComponent,
	},
  data(){
    return {
      title : '',
      action: null,
      disableAction: true
    }
  },
  watch:{
    selectedMemo (){
      this.checkEnableExecute()
    }
  },
  methods: {
    checkEnableExecute(){
      if(this.action && this.selectedMemo.length > 0){
        this.disableAction = false
      }
      else {
        this.disableAction = true
      }
    },
    changeAction(action){
      this.action = action
      this.checkEnableExecute()
    },
    executionAction(){
        let arrCompany = []
        let arrId = []
        arrCompany = JSON.parse(JSON.stringify(this.selectedMemo))
      //delete memo
      if(this.action == 'delete'){
        arrCompany.forEach(company => {
          if(company.id){
            arrId.push(company.id)
          }
        });
        this.ids = arrId
        this.deleteMulti = true
        this.dialogs.dialog = true
      }
    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    }
  },
	setup() {
    registerModule(StoreModule , 'memos')
    const snackbar = ref({ isColorSnackbarVisible : false, message : '', color : 'error' })
    const isAlertVisible = ref(false)
    if(router.currentRoute.params.status){
      isAlertVisible.value = true
    }
    const search = (event) => {
        fetchAll()
    }
    const loadingComponent = ref(false)
    const id = ref(null)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const onDelete = () => {
      dialogs.value.dialog = false
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('memos/deleteID', { id : id.value })
        .then(response => {
          fetchAll()
          store.commit('TOGGLE_LOADING' , false)
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'success'
          snackbar.value.message = response.data.message
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'error'
          snackbar.value.message =  error.response.data.errors
        })
      }
    }
    const memo = ref({})
    const onDetail = (item) => {
      dialogs.value.detailMemo = true
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('memos/fetchOne', {id : item.id})
        .then(response => {
          memo.value = response.data.data
          store.commit('TOGGLE_LOADING' , false)
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'error'
          snackbar.value.message =  error.response.data.errors
        } )
      }
    }
    const updateSort = (sort) => {
      sortBy.value = sort.toString()
    }
    const updateSortDesc = (event) => {
      if(event){
        sortBy.value = '-'+sortBy.value
      }
      fetchAll(1)
    }
    const selectedMemo = ref([])
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('memos/deleteSelected', { ids : ids.value })
        .then(response => {
          fetchAll()
          store.commit('TOGGLE_LOADING' , false)
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'success'
          snackbar.value.message = response.data.message
          selectedMemo.value = []
        })
        .catch(error => {
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'error'
          snackbar.value.message =  error.msg
        })
        deleteMulti.value = false
      }
    }
		const {
			listTable, tableColumns, totalListTable, loading, fetchAll, searchQuery, paginate, listAction, listHandel,titleSearch,
      roleOptions, perPage, dialogs,changeItemPerPage, sortBy,
		} = useStore()
		return {
      search,headerProps,
			fetchAll, tableColumns, loading, listTable, avatarText,	totalListTable,	searchQuery, paginate, listAction,titleSearch,
			listHandel,	roleOptions, itemPerPage, perPage, isAlertVisible,dialogs,onDetail,memo,changeItemPerPage,onDelete,onDialog,snackbar,
      formatTimeToDate,textNoData, selectedMemo, deleteSelected, updateSort, loadingComponent,updateSortDesc,
      ids, deleteMulti,sortBy,
			 // icons
			icons: {
				mdiSquareEditOutline,
				mdiFileDocumentOutline,
				mdiDotsVertical,
				mdiDeleteOutline,
				mdiPlus,
				mdiExportVariant,
				mdiAccountOutline,
				mdiExport
			}
		}
	},
}
</script>
<style lang="scss">
</style>
