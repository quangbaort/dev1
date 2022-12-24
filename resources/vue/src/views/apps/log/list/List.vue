<template>
<div>
	<v-card>
    <action-component :module="module" :listHandel="listHandel" :dialogs="dialogs"
                      @changeAction="changeAction" :executionAction="executionAction" :disableAction="disableAction"
    ></action-component>
    <v-divider class="my-2 mx-3"></v-divider>
    <v-row class="ma-0">
      <v-col cols="12"  md="2">
        <v-text-field v-model="detailSearch" @change="search" @keyup.enter="search(detailSearch)" hide-details="auto" :label="this.$trans('app.detail')" outlined dense>
        </v-text-field>
      </v-col>
      <v-col cols="12" md="2">
        <v-select v-model="typeSearch" :label="this.$trans('app.category')" :items="listTypeLog" @change="fetchAll"
                  item-value="type" item-text="text" outlined clearable dense hide-details >
          <template #item="data">
            {{ data.item.text }}
          </template>
        </v-select>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="12" class="px-5">
        <v-data-table v-model="selectedLog" :header-props="headerProps"
        :headers="tableColumns" :items="userListTable" :loading="loading" :page.sync="paginate.currentPage"
          :items-per-page="paginate.itemsPerPage" :server-items-length="paginate.total" show-select class="elevation-1"
          :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
          :loading-text="this.$trans('app.loading_item')" @update:sort-by="updateSort($event)"
          @update:items-per-page="changeItemPerPage" @update:page="fetchAll"  @update:sort-desc="updateSortDesc($event)" :sort-desc="true">
          <template v-slot:no-data>
            {{ textNoData }}
          </template>
          <template #[`item.date`]="{item}">
            <span>{{formatTimeToDate(item.date)}}</span>
          </template>
          <template #[`item.function`]="{item}">
            {{ functionToScreen(item.function , screen) }}
          </template>
          <template #[`item.type`]="{item}">
            <span v-if="item.type == 1">{{ listTypeLog[0].text }}</span>
            <span v-if="item.type == 2">{{ listTypeLog[1].text }}</span>
            <span v-if="item.type == 3">{{ listTypeLog[2].text }}</span>
          </template>
          <template #[`item.organization_name`]="{item}">
            <span v-if="item.organization_name !== '0'">{{item.organization_name}}</span>
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
                <v-list-item>
                  <v-list-item-title>
                    <v-icon size="20" class="me-2">
                      {{ icons.mdiFileDocumentOutline }}
                    </v-icon>
                    <span>{{ listAction.detail }}</span>
                  </v-list-item-title>
                </v-list-item>
                <v-list-item link >
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
  <alert-component :snackbar="snackbar"></alert-component>
  <confirm-component :dialogs.sync="dialogs" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.log')" ></confirm-component>

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
  mdiMagnify
} from '@mdi/js'
import store from '@/store'
import { onUnmounted, ref } from '@vue/composition-api'
import StoreModule from '../StoreModule'
import useStore from './useStore'
import { avatarText } from '@core/utils/filter'
import selectHandel from '@/common/components/selectHandel.vue'
import { registerModule , itemPerPage, formatTimeToDate, textNoData, functionToScreen, headerProps } from '@/common'
import router from '@/router'
import { screen } from '@/common/screen'
import ActionComponent from '@/common/components/ActionComponent.vue'
import AlertComponent from '@/common/components/snackBar.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
export default{
	components : {
		selectHandel,
    ActionComponent,
    ConfirmComponent,
    AlertComponent
	},
  data() {
    return {
      action: null,
      module: 'Log',
      disableAction: true
    };
  },
  watch:{
    selectedLog (){
      this.checkEnableExecute()
    }
  },
  methods : {
    checkEnableExecute(){
      if(this.action && this.selectedLog.length > 0){
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
      let arrLog = []
      let arrId = []
      arrLog = JSON.parse(JSON.stringify(this.selectedLog))
      arrLog.forEach(log => {
        if(log.id){
          arrId.push(log.id)
        }
      });
      //delete log
      if(this.action == 'delete'){
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
  props : ['selectCategory'],
	setup() {
    registerModule(StoreModule, 'log')
    const dialogs = ref({dialog: false, addNew: false, detail: false})
    const snackbar = ref({isColorSnackbarVisible: false, message: '', color: 'error'})
    const isAlertVisible = ref(false)
    if(router.currentRoute.params.status){
      isAlertVisible.value = true
    }
    const search = (event) => {
        fetchAll()
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
    const selectedLog = ref([])
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('log/deleteSelected', { ids : ids.value })
          .then(response => {
            fetchAll()
            store.commit('TOGGLE_LOADING' , false)
            store.commit('setSnackbar' ,{
              isColorSnackbarVisible : true,
              color : 'success',
              message : response.data.message
            })
            selectedLog.value = []
          })
          .catch(error => {
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true
            snackbar.value.color = 'error'
            snackbar.value.message =  error.msg
          })
        deleteMulti.value = false
      }
    }
		const {
			userListTable,
			tableColumns,
			loading,
			fetchAll,
			searchQuery,
			paginate,
			listAction,
			listHandel,
      perPage,
      detailSearch,
      typeSearch,
      listTypeLog,
      changeItemPerPage,
      sortBy
		} = useStore()
		return {
      headerProps,
      search,
			fetchAll,
      dialogs,
      snackbar,
			tableColumns,
			loading,
			userListTable,
			avatarText,
			searchQuery,
			paginate,
			listAction,
			listHandel,
      perPage,
      isAlertVisible,
      itemPerPage,
      detailSearch,
      typeSearch,
      listTypeLog,
      formatTimeToDate,
      textNoData,
      screen,
      functionToScreen,
      selectedLog,
      deleteSelected,
      changeItemPerPage,
      updateSort,updateSortDesc,
      ids, deleteMulti, sortBy,
			 // icons
			icons: {
				mdiSquareEditOutline,
				mdiFileDocumentOutline,
				mdiDotsVertical,
				mdiDeleteOutline,
				mdiPlus,
				mdiExportVariant,
				mdiAccountOutline,
				mdiExport,
        mdiMagnify
			}
		}
	},
}
</script>
<style lang="scss">

</style>
