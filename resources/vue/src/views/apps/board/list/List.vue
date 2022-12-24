<template>
<div>
  <v-card ref="refBoard" >
    <action-component :listHandel="listHandel" :dialogs="dialogs" @changeAction="changeAction"
                      :executionAction="executionAction" :disableAction="disableAction"></action-component>
    <v-divider class="my-2 mx-3"></v-divider>
    <v-card style="min-height: 450px; background-color: transparent">
      <v-navigation-drawer v-model="isLeftSidebarOpen" width="260" mobile-breakpoint="sm"
        absolute :right="$vuetify.rtl" touchless :temporary="$vuetify.breakpoint.smAndDown">
        <list-board-left :getAll="getAll" :listBoard="listBoard" :showChildren="showChildren" :treeDepartment="treeDepartment"></list-board-left>
      </v-navigation-drawer>
      <div class="email-content-area h-full position-relative" :style="contentStyles">
        <!-- 1st Row -->
        <div class="d-flex align-center pb-3">
          <v-icon v-show="!isLeftSidebarOpen" @click="isLeftSidebarOpen = true">
            {{ icons.mdiMenu }}
          </v-icon>
        </div>
          <v-row>
            <v-col cols="12" md="4">
              <v-text-field @change="search" @keyup.enter="search(nameSearch)" v-model="nameSearch" hide-details="auto" dense :label="this.$trans('app.branch_committee_name')" outlined></v-text-field>
            </v-col>
            <v-col cols="12" md="12">
              <v-data-table locale="ja_Jp" :header-props="headerProps" v-model="boardIdArr" id="board__list" @click:row="clickRow" :headers="tableColumns" :items="listTable" :loading="loading" item-key="id"
                :page.sync="paginate.currentPage" :items-per-page="paginate.itemsPerPage" :server-items-length="paginate.total"
                :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage')}"
                @update:items-per-page="changeItemPerPage($event)" show-select class="elevation-1 board_list" @update:page="fetchAll($event)"
                :loading-text="this.$trans('app.loading_item')">
              <template v-slot:body="props">
                <draggable :list="props.items" tag="tbody" @change="onDrop($event)">
                  <tr v-for="item in props.items" :key="item.id">
                    <td class="pb-2">
                      <v-checkbox class="align-self-center" :value="item" v-model="boardIdArr" hide-details></v-checkbox>
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
                            <v-list-item-title  @click="onDetail(item)">
                              <v-icon size="20" class="me-2">
                                {{ icons.mdiFileDocumentOutline }}
                              </v-icon>
                              <span>{{ listAction.detail }}</span>
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item link >
                            <v-list-item-title link @click="onDialog(item)">
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
      </div>
    </v-card>
  </v-card>
  <add-board :dialogs="dialogs" @refetch-data="fetchAll" @refetch-tree="getTree" :listBoard="listBoard"></add-board>
  <detail-board :inputVal="department" :dialogs="dialogs" @refetch-data="fetchAll" @refetch-tree="getTree" :listBoard="listBoardEdit"></detail-board>
  <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.branch_committee')" ></confirm-component>
</div>
</template>
<script>
import {
  mdiMenu,
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
import selectHandel from '@/common/components/selectHandel.vue'
import useStore from './useStore'
import StoreModule from '../StoreModule'
import ListBoardLeft from './ListBoardLeft.vue'
import { useResponsiveLeftSidebar } from '@core/comp-functions/ui'
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import draggable from 'vuedraggable'
import pagination from '@/common/components/Pagination.vue'
import { itemPerPage,registerModule } from '@/common'
import addBoard from '@/views/apps/board/add/Add.vue'
import detailBoard from '@/views/apps/board/Detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import ActionComponent from '@/common/components/ActionComponent.vue'
import { textNoData } from '@/common'
export default {
  components: {
    selectHandel,
    ListBoardLeft,
    PerfectScrollbar,
    draggable,
    pagination,
    addBoard,
    detailBoard,
    ConfirmComponent,
    ActionComponent,
  },
  data() {
    return {
      action: null,
      disableAction: true,
    }
  },
  watch:{
    boardIdArr (){
      this.checkEnableExecute()
    }
  },
  created() {
    this.tableMobile()
    console.log(document.getElementsByClassName('board_list'));
    console.log('absence');
  },
  methods : {
    tableMobile(){
    },
    checkEnableExecute(){
      if(this.action && this.boardIdArr.length > 0){
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
        this.arrBoard = JSON.parse(JSON.stringify(this.boardIdArr))
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
      this.updateDispOrder(val.moved.element.id, this.parentId, val.moved.oldIndex, val.moved.newIndex)
    },
  },
  setup() {
    registerModule(StoreModule)
    const dialogs = ref({ dialog : false, addNew : false , detail : false})
    const {
      listHandel, tableColumns, perPage, nameSearch, name, listTable, parentId, headerProps,
      loading, listAction, listBoard, listBoardEdit, boardIdArr, paginate, fetchAll, changeItemPerPage, getTree,treeDepartment
    } = useStore()
    const showChildren = (value) => {
      nameSearch.value = ''
      parentId.value = value[0]
      fetchAll()
    }
    const clickRow = (id) => {
      onDetail(id)
    }
    const getAll =()=>{
      treeDepartment.value = []
      nameSearch.value = ''
      fetchAll()
    }
    const search = () => {
     fetchAll()
    }
    // ————————————————————————————————————
    //* ——— Left Sidebar
    // ————————————————————————————————————

    const { isLeftSidebarOpen, contentStyles } = useResponsiveLeftSidebar({ sidebarWidth: 260 })

    const id = ref(null)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const onDelete = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('apps/deleteID', { id : id.value })
          .then(response => {
            fetchAll()
            getTree()
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
    const  department = ref({})
    const onDetail = (item) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.detail = true
        store.dispatch('apps/getTree', {
          id : item.id,
          organization_id : JSON.parse(localStorage.getItem('Organization')).id
          })
        .then(responseTree => {
          listBoardEdit.value = JSON.parse(JSON.stringify(responseTree.data))
          store.dispatch('apps/fetchOne', {id : item.id})
          .then(response => {
            department.value = response.data.data
            store.commit('TOGGLE_LOADING' , false)
          })
        })
          .catch(error =>  {
            store.commit('TOGGLE_LOADING' , false)
            store.commit('setSnackbar' ,{
              isColorSnackbarVisible : true,
              color : 'error',
              message : error.response.data.errors
            })
          })
      }
    }
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('apps/deleteSelected', { ids : ids.value })
        .then(response => {
          fetchAll()
          getTree()
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
        deleteMulti.value = false
      }
    }
    const updateDispOrder = (id, parentId,oldIndex, newIndex) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('apps/updateDispOrder', {
          id : id,
          parent_id : parentId,
          old_index : oldIndex,
          new_index : newIndex
        })
        .then(response => {
          getTree()
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    }
    return {
      isLeftSidebarOpen,textNoData,
      contentStyles, listHandel,tableColumns,
      perPage,
      nameSearch, showChildren,
      name,
      listTable,
      loading,
      listAction,
      listBoard,
      listBoardEdit,
      boardIdArr,
      changeItemPerPage,
      paginate,clickRow,
      fetchAll,dialogs,itemPerPage, getTree,treeDepartment, search, getAll,
      onDelete,onDialog, parentId, deleteSelected,
      onDetail, department,ConfirmComponent,
      ids, deleteMulti,updateDispOrder,
      headerProps,
      //icon
      icons : {
        mdiMenu,
        mdiDotsVertical,
        mdiPlus,
        mdiFileDocumentOutline,
        mdiDeleteOutline,
        mdiExportVariant,
        mdiAccountOutline,
        mdiExport,
      }
    }
  },
}
</script>
<style>

#board__list .v-data-table-header-mobile__select{
  display: block;
}
</style>

