<template>
  <v-card>
    <strong class="d-flex justify-end" v-if="organization">{{ this.$trans('app.use_status') }}：{{ organization.storage_used | bytes}} / {{organization.storage_limit}}GB </strong>
    <v-tabs v-model="currentTab" icons-and-text grow @change="changeTab">
      <v-tab>{{ this.$trans('app.event') }}
        <v-icon>{{ icons.mdiCalendarMonthOutline }}</v-icon>
      </v-tab>
      <v-tab>{{ this.$trans('app.notify') }}
        <v-icon>{{ icons.mdiBellOutline }}</v-icon>
      </v-tab>
      <v-tab>{{ this.$trans('app.general_meeting') }}
        <v-icon>{{ icons.mdiAccountGroup  }}</v-icon>
      </v-tab>
      <v-tab>{{ this.$trans('app.share') }}
        <v-icon>{{ icons.mdiFolderAccountOutline  }}</v-icon>
      </v-tab>
    </v-tabs>
    <v-tabs-items v-model="currentTab">
      <v-tab-item :transition="false">
        <v-card class="listFolder my-1 pt-1">
          <v-navigation-drawer v-model="isLeftSidebarOpen"  mobile-breakpoint="sm" absolute :right="$vuetify.rtl" touchless :temporary="$vuetify.breakpoint.smAndDown">
        <list-folder-left :changeFolder="changeFolder" :listFolder="listFolder"></list-folder-left>
        </v-navigation-drawer>
        <div class="email-content-area h-full position-relative" :style="contentStyles">
          <!-- 1st Row -->
          <div class="d-flex align-center pt-3">
            <v-icon v-show="!isLeftSidebarOpen" @click="isLeftSidebarOpen = true">
              {{ icons.mdiMenu }}
            </v-icon>
          </div>
          <v-row class="py-3">
            <v-col cols="12" md="4" class="pt-0">
              <v-text-field v-model="keyword" @change="search()" @keyup.enter="search()" :label="this.$trans('app.event_name')" outlined dense></v-text-field>
            </v-col>
          </v-row>
          <v-data-table id="table-1" :items="itemFolder" :headers="tableColumnsCalendar" class="elevation-1" show-select
                        @update:sort-desc="updateSortDesc($event)"
                        @update:sort-by="updateSort($event)"
                        :sort-desc="true"
                        :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                         @update:page="changePage($event, 'event')" :page.sync="paginate.currentPage" :server-items-length="paginate.total"
                        @update:items-per-page="changeLimit($event, 'event')" :items-per-page="paginate.itemsPerPage" :loading-text="this.$trans('app.loading_item')">
            <template v-slot:no-data>
                {{ textNoData }}
            </template>
            <template #[`item.name`]="{item}">
            {{item.event_title}}
            </template>
            <template #[`item.title`]="{item}">
              <a :href="item.download_url" target="_blank">{{truncateFileName(item.title, 35)}} </a>
              <v-chip color="error" small class="ml-2" v-if="item.is_new">{{ textNew }}</v-chip>
            </template>
            <template #[`item.start`]="{item}">
                {{ formatTimeToDate(item.event_start) }}
            </template>
          </v-data-table>
        </div>
        </v-card>
      </v-tab-item>
      <v-tab-item :transition="false">
        <v-card class="listFolder my-1 pt-1">
          <v-navigation-drawer v-model="isLeftSidebarOpen"  mobile-breakpoint="sm" absolute :right="$vuetify.rtl" touchless :temporary="$vuetify.breakpoint.smAndDown">
            <list-folder-left :changeFolder="changeFolder" :listFolder="listFolder"></list-folder-left>
          </v-navigation-drawer>
          <div class="email-content-area h-full position-relative" :style="contentStyles">
            <!-- 1st Row -->
            <div class="d-flex align-center pt-3">
              <v-icon v-show="!isLeftSidebarOpen" @click="isLeftSidebarOpen = true">
                {{ icons.mdiMenu }}
              </v-icon>
            </div>
            <v-row class="py-3">
              <v-col cols="12" md="4" class="pt-0">
                <v-text-field v-model="keyword" @change="search()" @keyup.enter="search(keyword)" :label="this.$trans('app.title')" outlined dense></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="pt-0">
                <v-text-field v-model="title" @change="search()" @keyup.enter="search(title)" :label="this.$trans('app.file_name')" outlined dense></v-text-field>
              </v-col>
            </v-row>
            <v-data-table :items="itemFolder" :headers="tableColumnsNotify" class="elevation-1" show-select
                          @update:sort-desc="updateSortDesc($event)"
                          @update:sort-by="updateSort($event)"
                          :sort-desc="true"
                          :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                          @update:page="changePage($event, 'news')" :page.sync="paginate.currentPage" :server-items-length="paginate.total"
                          @update:items-per-page="changeLimit($event, 'news')" :items-per-page="paginate.itemsPerPage" :loading-text="this.$trans('app.loading_item')">
            <template v-slot:no-data>
                {{ textNoData }}
            </template>
              <template #[`item.name`]="{item}">
                {{item.news_title}}
              </template>
            <template #[`item.title`]="{item}">
              <a :href="item.download_url" target="_blank">{{truncateFileName(item.title, 35)}} </a>
              <v-chip color="error" small class="ml-2" v-if="item.is_new">{{ textNew }}</v-chip>
            </template>
            <template #[`item.date`]="{item}">
                {{ formatTimeToDate(item.notify_updated_at) }}
            </template>
          </v-data-table>
          </div>
        </v-card>
      </v-tab-item>
      <v-tab-item :transition="false">
        <v-card class="listFolder my-1 pt-1">
          <v-navigation-drawer v-model="isLeftSidebarOpen"  mobile-breakpoint="sm" absolute :right="$vuetify.rtl" touchless :temporary="$vuetify.breakpoint.smAndDown">
            <list-folder-left :changeFolder="changeFolder" :listFolder="listFolder"></list-folder-left>
          </v-navigation-drawer>
          <div class="email-content-area h-full position-relative mt-3" :style="contentStyles">
            <!-- 1st Row -->
            <div class="d-flex align-center pt-3">
              <v-icon v-show="!isLeftSidebarOpen" @click="isLeftSidebarOpen = true">
                {{ icons.mdiMenu }}
              </v-icon>
            </div>
            <v-data-table :items="itemFolder" :headers="tableColumnsCalendar" class="elevation-1" show-select
                          @update:sort-desc="updateSortDesc($event)"
                          @update:sort-by="updateSort($event)"
                          :sort-desc="true"
                          :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                          @update:page="changePage($event, 'meeting')" :page.sync="paginate.currentPage" :server-items-length="paginate.total"
                          @update:items-per-page="changeLimit($event, 'meeting')" :items-per-page="paginate.itemsPerPage" :loading-text="this.$trans('app.loading_item')">
            <template v-slot:no-data>
                {{ textNoData }}
            </template>
              <template #[`item.name`]="{item}">
                {{item.meeting_title}}
              </template>
            <template #[`item.title`]="{item}">
              <a :href="item.download_url" target="_blank">{{truncateFileName(item.title, 35)}} </a>
              <v-chip color="error" small class="ml-2" v-if="item.is_new">{{ textNew }}</v-chip>
            </template>
            <template #[`item.start`]="{item}">
                {{ formatTimeToDate(item.meeting_start) }}
            </template>
          </v-data-table>
          </div>
        </v-card>
      </v-tab-item>
      <v-tab-item :transition="false">
        <v-card class="listFolder my-1 pt-1">
          <v-navigation-drawer v-model="isLeftSidebarOpen"  mobile-breakpoint="sm" absolute :right="$vuetify.rtl" touchless >
            <list-folder-left @refetch-data="refetch" :changeFolder="changeFolder" :status-change="true" :listFolder="listFolder"></list-folder-left>
          </v-navigation-drawer>
          <div class="mt-3" :style="contentStyles">
            <div class="d-flex align-center pt-3">
              <v-icon v-show="!isLeftSidebarOpen" @click="isLeftSidebarOpen = true">
                {{ icons.mdiMenu }}
              </v-icon>
            </div>
            <v-row class="py-3">
              <v-col cols="12" md="4" class="pt-0">
                <v-text-field v-model="title" @change="search()" @keyup.enter="search(title)" :label="this.$trans('app.file_name')" outlined dense></v-text-field>
              </v-col>
            </v-row>
             <v-data-table :items="itemFolder" :headers="tableColumns" class="elevation-1" show-select
                          :header-props="headerProps"
                           @update:sort-desc="updateSortDesc($event)"
                           @update:sort-by="updateSort($event)"
                           :sort-desc="true"
                           :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                           @update:page="changePage($event, 'folder')" :page.sync="paginate.currentPage" :server-items-length="paginate.total"
                           @update:items-per-page="changeLimit($event, 'folder')" :items-per-page="paginate.itemsPerPage" :loading-text="this.$trans('app.loading_item')">
              <template v-slot:no-data>
                {{ textNoData }}
              </template>
              <template #[`item.title`]="{item}">
              <a :href="item.download_url" target="_blank">{{truncateFileName(item.title, 35)}} </a>
              <v-chip color="error" small class="ml-2">{{ textNew }}</v-chip>
              </template>
              <template #[`item.actions`]="{item}">
                <div @click="onDialog(item)">
                  <v-icon >{{ icons.mdiTrashCan }}</v-icon>
                </div>
              </template>
            </v-data-table>
            <v-row class="mt-4" v-if="path && readOnly" >
              <v-col cols="12">
                <div class="box__upload">
                  <h3>{{ this.$trans('app.file_upload') }}</h3>
                  <p>※{{ this.$trans('app.msg_max_size') }}</p>
                  <v-row >
                    <v-col md="9" cols="12">
                      <v-file-input v-model="file" :label="this.$trans('app.file_select')" hide-details></v-file-input>
                    </v-col>
                    <v-col class="mt-2">
                      <v-btn color="primary" @click="uploadFile">{{ this.$trans('app.file_upload') }}</v-btn>
                    </v-col>
                  </v-row>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-card>
        <confirm-component :title="'削除'" :module="'ファイル'" :dialogs="dialogs" :onDelete="onDelete"></confirm-component>
        <alert-component :snackbar="snackbar"></alert-component>
      </v-tab-item>
    </v-tabs-items>

  </v-card>
</template>
<script>
import { mdiCalendarMonthOutline , mdiBellOutline , mdiAccountGroup , mdiFolderAccountOutline , mdiMenu ,mdiTrashCan   } from '@mdi/js'
import ListFolderLeft from './ListFolderLeft/index.vue'
import { registerModule , itemPerPage ,textNoData, formatTimeToDate, headerProps } from '@/common'
import StoreModule from './StoreModule'
import useStore from './useStore'
import store from '@/store'
import { useResponsiveLeftSidebar } from '@core/comp-functions/ui'
import {ref} from "@vue/composition-api";
import { computed } from '@vue/composition-api'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import AlertComponent from '@/common/components/snackBar.vue'
export default {
  components : {
    ListFolderLeft,
    ConfirmComponent,
    AlertComponent,
  },
  filters: {
    bytes: function (data) {
      const const_term = 1024;
      // Convert the values and concatenate
      if(data <= 1000){
        return data + "KB";
      }
      else if (data > 1000 && data <= 1024000) {
        return +(Math.floor(data / const_term + "e+1") + "e-1") + "MB";
      }
      else if (data > 1024000) {
        return +(Math.floor(data / const_term ** 2 + "e+1") + "e-1") + "GB";
      }
    },
  },
  mounted() {
    const userData = JSON.parse(localStorage.getItem('userData'));
    const admin = store.state.user.role == 1 || userData.is_super_admin ? true : false
    if(admin){
      this.getStorageFree()
    }
  },
  data(){
    return {
      currentTab : null,
    }
  },
  setup() {
    registerModule(StoreModule, 'fileManger')
    const snackbar = ref({ isColorSnackbarVisible : false, message : '', color : 'error' })
    const type = ref('event')
    const path = ref(null)
    const title = ref('')
    const keyword = ref('')
    const organization = ref(null)
    const getStorageFree = () => {
      store.dispatch('fileManger/storageFree', {id : JSON.parse(localStorage.getItem('Organization')).id})
        .then(response => {
          organization.value = response.data.data
        })
    }
    const readOnly = computed(() => {
      if(store.state.user.role){
        return store.state.user.role == 1 ? true : false
      }else if(store.state.user.is_super_admin  == true){
        return true
      }
      return true
    })

    const changeTab = (value) => {
      switch (value) {
        case 0 :
          type.value = 'event';
          break;
        case 1 :
          type.value = 'news';
          break;
        case 2 :
          type.value = 'meeting';
          break;
        default:
          type.value = 'folder';
      }
      getFolders(type.value);
      title.value = ''
      keyword.value = ''
      paginate.value = {}
      getFileList(1,'', '')
    }
    const file = ref(null)
    const changePage = (page, tab) => {
      if(tab == type.value) {
        getFileList(page, type.value, path.value, title.value, keyword.value)
      }
    }
    const changeLimit = (limit, tab) => {
        if(tab == type.value) {
          changeItemPerPage(limit, type.value, path.value, title.value, keyword.value)
        }
    }
    const refetch = () => {
      getFolders(type.value)
    }
    const changeFolder = (value) => {
      if (value[0]) {
        path.value = value[0].path
        getFileList(1, value[0].type, value[0].path)
      }
    }

    const search = (event) => {
        getFileList(1, type.value, path.value, title.value, keyword.value)
    }
    const uploadFile = () => {
      if (file.value.size >= 20971520) {
        store.commit('setSnackbar', {
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('app.msg_max_size')
        })
        return
      }
      if(file.value && !store.state.loading){
        store.commit('TOGGLE_LOADING', true)
        let formData = new FormData()
        formData.append('file', file.value)
        formData.append('path', path.value)
        store.dispatch('fileManger/uploadFile', formData)
        .then((res) =>{
          getStorageFree()
          getFileList(1, type.value, path.value)
          store.commit('TOGGLE_LOADING', false)
          file.value = null
        })
      }
    }
    const sortBy = ref('')
    const updateSort = (sort) => {
      sortBy.value = sort
    }
    const updateSortDesc = (event) => {
      if(event){
        sortBy.value = '-'+sortBy.value
      }
      getFileList(paginate.value.currentPage, type.value, path.value, title.value, keyword.value, sortBy.value)
    }
    const id = ref(null)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const onDelete = () => {
      dialogs.value.dialog = false
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('fileManger/deleteID', { id : id.value })
          .then(response => {
            getFileList(1, type.value, path.value)
            getStorageFree()
            store.commit('TOGGLE_LOADING' , false)
            store.commit('setSnackbar', {
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
    const {
      listFolder,getFileList, getFolders, tableColumnsCalendar, listFolder4, itemFolder, changeItemPerPage,
      tableColumns, tableColumnsNotify, dialogs, textNew, paginate, truncateFileName, countLength } = useStore()
    const { isLeftSidebarOpen, contentStyles } = useResponsiveLeftSidebar({ sidebarWidth: 260 })
    return {
      uploadFile,file,title,keyword, changeLimit,updateSort,sortBy,updateSortDesc,headerProps,readOnly,
      getFolders,changeTab,getFileList,changeFolder,formatTimeToDate,search,type,path, changeItemPerPage,
      dialogs, listFolder,contentStyles,isLeftSidebarOpen,tableColumnsCalendar,changePage,listFolder4,itemFolder,tableColumns,tableColumnsNotify,itemPerPage,
      textNew,textNoData,refetch, onDialog, onDelete, snackbar, organization, getStorageFree, paginate,truncateFileName, countLength,
      icons : {
        mdiCalendarMonthOutline , mdiBellOutline , mdiAccountGroup , mdiFolderAccountOutline , mdiMenu ,mdiTrashCan
      }
    }
  },
}
</script>
<style scoped>
.listFolder{
  min-height: 300px;
}
.v-navigation-drawer {
  min-width: 260px;
}
.box__upload {
  border: 1px solid #cccccc;
  padding : 1rem;
  border-radius: 10px;
}
.v-chip{
  overflow: revert !important;
}

</style>
