<template>
<div>
	<v-card>
    <action-component :listHandel="listHandel" :onAdd="onAdd" :dialogs="dialogs" @changeAction="changeAction"
                      :executionAction="executionAction" :disableAction="disableAction"></action-component>
		<v-divider class="my-2 mx-3"></v-divider>
    <!-- end search -->
    <v-row class="ma-0">
      <v-col cols="12" sm="12" md="12">
        <v-data-table v-model="selectedNotifyGroup" id="notify__list" :headers="tableColumns" :items="listTable" :loading="loading" :page.sync="paginate.currentPage"
          :items-per-page.sync="paginate.itemsPerPage" :server-items-length="paginate.total" :loading-text="this.$trans('app.loading_item')"
          :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
          @update:items-per-page="changeItemPerPage" @update:page="fetchAll" show-select class="elevation-1"
          @click:row="clickRow">
          <template v-slot:body="props">
            <draggable :list="props.items" tag="tbody" @change="onDrop($event)">
              <tr v-for="item in props.items" :key="item.id">
                <td class="pb-2">
                  <v-checkbox class="align-self-center" :value="item" v-model="selectedNotifyGroup" hide-details></v-checkbox>
                </td>
                <td @click="clickRow(item)"> {{ item.name }} </td>
                <td>
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
                          <span>{{listAction.delete}}</span>
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
            </draggable>
          </template>
          <template v-slot:no-data>
            {{ textNoData }}
          </template>
        </v-data-table>
      </v-col>
    </v-row>
	</v-card>
  <add-notifyGroup :listBoard="listBoard" :dialogs="dialogs" @refetch-data="fetchAll"></add-notifyGroup>
  <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.notify_before')"></confirm-component>
 <detail-notifyGroup :inputVal="notifyGroup" :departmentOfUser="departmentOfUser" :listBoard="listBoard" :dialogs="dialogs" @refetch-data="fetchAll"></detail-notifyGroup>
  <alert-component :snackbar="snackbar"></alert-component>
  <loading-component :loading="loadingComponent"></loading-component>
</div>
</template>
<script>
import { mdiDotsVertical, mdiPlus, mdiFileDocumentOutline, mdiDeleteOutline } from '@mdi/js'
import store from '@/store'
import StoreModule from './StoreModule'
import useStore from './useStore'
import draggable from 'vuedraggable'
import selectHandel from '@/common/components/selectHandel.vue'
import addNotifyGroup from '@/views/apps/notifyGroup/add/Add.vue'
import detailNotifyGroup from '@/views/apps/notifyGroup/detail/Detail.vue'
import ActionComponent from '@/common/components/ActionComponent.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import {  listAction , registerModule, itemPerPage , textNoData} from '@/common/'
import { ref } from '@vue/composition-api'
import loadingComponent from '@/common/components/loading.vue'
import AlertComponent from '@/common/components/snackBar.vue'
export default {
	components : {
		selectHandel,
    draggable,
    addNotifyGroup,
    ConfirmComponent,
    detailNotifyGroup,
    ActionComponent,
    loadingComponent,
    AlertComponent
	},
  data() {
    return {
      action: null,
      disableAction: true
    }
  },
  watch:{
    selectedNotifyGroup (){
      this.checkEnableExecute()
    }
  },
  methods : {
    checkEnableExecute(){
      if(this.action && this.selectedNotifyGroup.length > 0){
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
      if(this.action == 'delete'){
        let arrId = []
        this.arrBoard = JSON.parse(JSON.stringify(this.selectedNotifyGroup))
        this.arrBoard.forEach(board => {
          if(board.id){
            arrId.push(board.id)
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
    },
    onDrop(val){
      this.updateDispOrder(val.moved.element.id, val.moved.oldIndex, val.moved.newIndex)
    },
  },
	setup() {
    registerModule(StoreModule, 'notifyGroup')
	  const filter = (value , search , item) => {
		  return value != null &&
			search != null &&
			typeof value === 'string' &&
			value.toString().toLocaleUpperCase().indexOf(search) !== -1
	  }
    const dialogs = ref({ dialog : false, addNew : false , detail : false})
    const snackbar = ref({ isColorSnackbarVisible : false, message : '', color : 'error' })
    const id = ref(null)
    const loadingComponent = ref(false)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const clickRow = (id) => {
      onDetail(id)
    }
    const onDelete = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('notifyGroup/deleteID', { id : id.value })
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
        })
      }
    }
    const listUser = ref([])
    const listBoard = ref([])
    const notifyGroup = ref({})
    const departmentOfUser = ref([])
    const onDetail = (item) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store
        .dispatch('notifyGroup/getTree', {
          organization_id: JSON.parse(localStorage.getItem('Organization')).id
        })
        .then(response => {
          listBoard.value = JSON.parse(JSON.stringify(response.data))
          store
            .dispatch('notifyGroup/fetchOne', {id : item.id})
            .then(response => {
              if(departmentOfUser.value.length > 0) {
                departmentOfUser.value = []
              }
              dialogs.value.detail = true
              store.commit('TOGGLE_LOADING' , false)
              notifyGroup.value = response.data.data
            })
          })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    }
    const onAdd = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.addNew = true
        store
        .dispatch('notifyGroup/getTree', {
          organization_id: JSON.parse(localStorage.getItem('Organization')).id
        })
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          listBoard.value = JSON.parse(JSON.stringify(response.data))
          })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    }
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('notifyGroup/deleteSelected', { ids : ids.value })
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
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'error'
          snackbar.value.message =  error.msg
        })
        deleteMulti.value = false
      }
    }
    const updateDispOrder = (id,oldIndex, newIndex) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('notifyGroup/updateDispOrder', {
          id : id,
          old_index : oldIndex,
          new_index : newIndex
        })
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    }
   	const {
			listTable, tableColumns, loading, fetchAll, paginate, changeItemPerPage, listHandel,
      selectedNotifyGroup
		} = useStore()
		return {
      listAction,listHandel,fetchAll,tableColumns,loading,listTable,filter,paginate, listBoard,
      dialogs,snackbar,onDelete,onDialog,onDetail,selectedNotifyGroup,
      changeItemPerPage,registerModule,id,notifyGroup,itemPerPage,textNoData, deleteSelected,
      listUser, onAdd,clickRow,loadingComponent,departmentOfUser,
      ids, deleteMulti, updateDispOrder,
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
<style>
#notify__list .v-data-table-header-mobile__select{
  display: block;
}
</style>
