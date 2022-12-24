<template>
  <div>
    <v-card>
      <action-component :listHandel="listHandel" :dialogs="dialogs" @changeAction="changeAction"
                        :executionAction="executionAction" :disableAction="disableAction"></action-component>
      <v-divider class="my-2 mx-3"></v-divider>

      <!-- end search -->
      <v-row class="ma-0">
        <v-col md="2" sm="3" cols="6">
          <v-text-field :label="this.$trans('app.holiday_name')" @keyup.enter="search(name)" @change="search" v-model="name" outlined hide-details dense class=""></v-text-field>
        </v-col>
        <v-col cols="12" sm="12" md="12">
          <v-data-table v-model="selectedHoliday" :header-props="headerProps" @update:sort-by="updateSort($event)" :headers="tableColumns" :items="listTable" :loading="loading" :page.sync="paginate.currentPage"
          :items-per-page="-1" :server-items-length="paginate.total" :loading-text="this.$trans('app.loading_item')"
          :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }" @update:items-per-page="changeItemPerPage"
          show-select class="elevation-1" @update:page="fetchAll" @click:row="clickRow" @update:sort-desc="updateSortDesc($event)" :sort-desc="true">
          <template v-slot:no-data>
            {{ textNoData }}
          </template>
          <template v-slot:loading>
            {{ loadingItem }}
          </template>
          <template #[`item.date`]="{item}">
            <span>{{formatDate(item.date)}}</span>
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
                  <v-list-item link>
                    <v-list-item-title @click="onDetail(item)">
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
                      <span>{{ listAction.delete }}</span>
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card>
    <add-holiday :dialogs="dialogs" @refetch-data="fetchAll"></add-holiday>
    <detail-holiday :inputVal="holiday" :dialogs="dialogs" @refetch-data="fetchAll"></detail-holiday>
    <confirm-component :dialogs="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.holiday')"></confirm-component>
  </div>
</template>
<script>
import {mdiDotsVertical, mdiPlus, mdiFileDocumentOutline, mdiDeleteOutline} from '@mdi/js'
import store from '@/store'
import StoreModule from './StoreModule'
import use_store from './use_store'
import selectHandel from '@/common/components/selectHandel.vue'
import addHoliday from '@/views/apps/holidays/add/Add.vue'
import detailHoliday from '@/views/apps/holidays/detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import {listHandel, listAction, registerModule, itemPerPage , formatDate , textNoData , loadingItem, headerProps } from '@/common/'
import {ref} from '@vue/composition-api'
import ActionComponent from '@/common/components/ActionComponent.vue'
export default {
  components: {
    selectHandel,
    addHoliday,
    ConfirmComponent,
    detailHoliday,
    ActionComponent,
  },
  data() {
    return {
      action: null,
      disableAction: true
    }
  },
  watch:{
    selectedHoliday (){
      this.checkEnableExecute()
    }
  },
  methods : {
    checkEnableExecute(){
      if(this.action && this.selectedHoliday.length > 0){
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
        let arrHoliday = []
        let arrId = []
        arrHoliday = JSON.parse(JSON.stringify(this.selectedHoliday))
        arrHoliday.forEach(holiday => {
          if(holiday.id){
            arrId.push(holiday.id)
          }
        });
      //delete company
      if(this.action == 'delete'){
        this.ids = arrId
        this.deleteMulti = true
        this.dialogs.dialog = true
      }
      //export company
      else if(this.action == 'export'){
        this.exportCsv(arrId)
      }
    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    }
  },
  setup() {
    registerModule(StoreModule , 'holiday')
    const dialogs = ref({dialog: false, addNew: false, detail: false})
    const id = ref(null)
    const search = (value) => {
        fetchAll()
    }
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const loadingComponent = ref(false)
    const clickRow = (row) => {
      onDetail(row)
    }
    const fetchData = () => {
      fetchAll()
    }
    const onDelete = () => {
      dialogs.value.dialog = false
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('holiday/deleteID', {id: id.value})
        .then(response => {
          fetchAll()
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : response.data.message
          })
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        })
      }
    }
    const holiday = ref({})
    const onDetail = (item) => {
      dialogs.value.detail = true
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
      store.dispatch('holiday/fetchOne', {id: item.id})
        .then(response => {
          holiday.value = response.data.data
          store.commit('TOGGLE_LOADING' , false)
        })
        .catch(error => store.commit('TOGGLE_LOADING' , false))
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
    const selectedHoliday = ref([])
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('holiday/deleteSelected', { ids : ids.value })
        .then(response => {
          fetchAll()
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : response.data.message
          })
          selectedHoliday.value = []
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        })
        deleteMulti.value = false
      }
    }
    const exportCsv = (ids) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('holiday/exportCsv', { ids : ids})
        .then(response => {
          var headers = response.headers;
          var blob = new Blob(["\uFEFF" + response.data], {
            type: headers["content-type"],
          });
          var link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          var date = new Date();
          var fileName =
            String(date.getFullYear()) +
            String(date.getMonth() + 1) +
            String(date.getDate()) +
            String(date.getHours()) +
            String(date.getMinutes()) +
            String(date.getSeconds());
          link.download = "Holidays" + fileName + ".csv";
          link.click();
          store.commit('TOGGLE_LOADING' , false)
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        })
      }
    }

   	const {
			listTable,tableColumns,loading,fetchAll,paginate,changeItemPerPage,name, sortBy
		} = use_store()
		return {
      search,headerProps,
      listAction,listHandel,fetchAll,tableColumns,loading,listTable,paginate,
      dialogs,onDelete,onDialog,onDetail,textNoData,
      changeItemPerPage,registerModule,id,holiday,itemPerPage,formatDate,fetchData,
      deleteSelected, selectedHoliday, exportCsv, clickRow, name,loadingItem,updateSort,loadingComponent,updateSortDesc,
      ids, deleteMulti, sortBy,
			icons: {
				mdiDotsVertical,
        mdiPlus,
        mdiFileDocumentOutline,
        mdiDeleteOutline,
			}
		}
	},
}
</script>

