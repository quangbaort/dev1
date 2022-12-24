<template>
<div>
	<v-card>
    <action-component v-if="role()" :listHandel="listHandel" :dialogs="dialogs" @changeAction="changeAction"
                      :executionAction="executionAction"  :disableAction="disableAction"></action-component>
    <v-divider class="my-2 mx-3"  v-if="role()" ></v-divider>
    <!-- search -->
		<v-row class="ma-0">
      <v-col cols="12" sm="4" md="3">
        <v-text-field v-model="nameOrganization" @change="search" @keyup.enter="search(nameOrganization)" :label="this.$trans('app.organization_name')" outlined
          hide-details dense class="">
        </v-text-field>
      </v-col>
      <v-col cols="12" sm="4" md="3">
        <v-select clearable outlined dense hide-details="auto" @change="search" @keyup.enter="search(prefecture)"  v-model="prefecture" :items="prefectureArr" item-text="Pref_name" item-value="Pref_code" :label="this.$trans('app.prefectures')"
          >
        </v-select>
      </v-col>
      <v-col cols="12" sm="4" md="3">
        <v-text-field v-model="city"  @change="search" @keyup.enter="search(city)"  :label="this.$trans('app.municipalities')" outlined hide-details dense
          class="">
        </v-text-field>
      </v-col>
      <v-col cols="12" sm="4" md="3">
        <v-text-field v-model="tel" @change="search" @keyup.enter="search(tel)" :label="this.$trans('app.tel')" outlined hide-details dense
        class="">
        </v-text-field>
      </v-col>
			<v-spacer></v-spacer>
		</v-row>
    <!-- end search -->
    <v-row class="ma-0">
      <v-col cols="12" sm="12" md="12" class="px-3">
      <v-data-table locale="ja" v-model="organizationArr" :header-props="headerProps"
       :headers="role() ? headerSystemAdmin : headerNormalUser" :items="listTable" :loading="loading"
        :items-per-page="-1" item-key="id" :server-items-length.sync="paginate.total" :page.sync="paginate.currentPage"
        @update:sort-by="updateSort($event)" :sort-desc="true" :loading-text="this.$trans('app.loading_item')"
        :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }" @update:items-per-page="changeItemPerPage"
        show-select class="elevation-1" @update:page="fetchAll" @click:row="clickRow" @update:sort-desc="updateSortDesc($event)">
        <template v-slot:no-data>
          {{ textNoData }}
        </template>
        <template #[`item.prefecture`]="{item}">
          {{ replacePrefecture(item.prefecture , address) }}
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
  <add-organization v-if="role()" :dialogs="dialogs" @refetch-data="fetchAll"></add-organization>
  <detail-organization :inputVal="organization" @refetch-data="fetchAll" :dialogs="dialogs"></detail-organization>
  <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.organization')"></confirm-component>
  <alert-component :snackbar="snackbar"></alert-component>
  <loading-component :loading="loadingComponent"></loading-component>
</div>
</template>
<script>
import { mdiDotsVertical, mdiPlus, mdiFileDocumentOutline, mdiDeleteOutline } from '@mdi/js'
import store from '@/store'
import StoreModule from './StoreModule'
import useStore from './useStore'
import selectHandel from '@/common/components/selectHandel.vue'
import addOrganization from '@/views/apps/organizations/add/Add.vue'
import detailOrganization from '@/views/apps/organizations/detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import { listHandel , listAction , registerModule , itemPerPage , textNoData , replacePrefecture , role, headerProps } from '@/common/'
import { ref } from '@vue/composition-api'
import { address } from '@/common/address'
import ActionComponent from '@/common/components/ActionComponent.vue'
import loadingComponent from '@/common/components/loading.vue'
import AlertComponent from '@/common/components/snackBar.vue'
export default{
	components : {
		selectHandel,
    addOrganization,
    ConfirmComponent,
    detailOrganization,
    ActionComponent,
    loadingComponent,
    AlertComponent
	},
  data(){
    return {
      action: null,
      disableAction: true
    }
  },
  watch:{
    organizationArr (){
      this.checkEnableExecute()
    }
  },
  methods :{
    checkEnableExecute(){
      if(this.action && this.organizationArr.length > 0){
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
        let arrId = []
        let arrBoard = []
        arrBoard = JSON.parse(JSON.stringify(this.organizationArr))
        arrBoard.forEach(organization => {
          if(organization.id){
            arrId.push(organization.id)
          }
        });
        if(this.action == 'delete'){
          this.ids = arrId
          this.deleteMulti = true
          this.dialogs.dialog = true
        }
        else if (this.action == "export") {
          this.exportCsv(arrId)
        }
    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    }
  },

	setup() {
    registerModule(StoreModule , 'organization')
    const dialogs = ref({ dialog : false, addNew : false , detail : false})
    const snackbar = ref({ isColorSnackbarVisible : false, message : '', color : 'error' })
    const id = ref(null)
    const loadingComponent = ref(false)
    const search = (event) => {
        fetchAll()
    }
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const clickRow = (id) => {
      onDetail(id)
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
    const onDelete = () => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('organization/deleteID', { id : id.value })
        .then(response => {
          fetchAll(1)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : response.data.message
          })
          store.commit('TOGGLE_LOADING' , false)
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })
    }

    }
    const organization = ref({})
    const onDetail = (item) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('organization/fetchOne', {id : item.id})
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          response.data.data.account_limit = response.data.data.account_limit.toString()
          response.data.data.storage_limit = response.data.data.storage_limit.toString()
          organization.value = response.data.data
          dialogs.value.detail = true
        })
      .catch(error => {
        store.commit('TOGGLE_LOADING' , false)
      })
      }
    }
    const organizationArr = ref([])
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('organization/deleteSelected', { organizationIds : ids.value })
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          fetchAll(1)
          organizationArr.value = []
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : response.data.message
          })
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          organizationArr.value = []
          snackbar.value.isColorSnackbarVisible = true
          snackbar.value.color = 'error'
          snackbar.value.message =  error.msg
        })
        deleteMulti.value = false
      }
    }
    const exportCsv = (ids) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('organization/export', { organizationIds : ids })
          .then((response) => {
            store.commit('TOGGLE_LOADING' , false)
            var blob = new Blob(["\uFEFF" + response.data], {
              type: response.headers["content-type"],
            });
            var link = document.createElement("a")
            link.href = window.URL.createObjectURL(blob)
            var date = new Date()
            var fileName =
              String(date.getFullYear()) +
              String(date.getMonth() + 1) +
              String(date.getDate()) +
              String(date.getHours()) +
              String(date.getMinutes()) +
              String(date.getSeconds())
            link.download = "Organization" + fileName + ".csv"
            link.click()
          })
          .catch(error => {
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true
            snackbar.value.color = 'error'
            snackbar.value.message =  error.msg
          })
      }

    }
   	const {
			listTable,headerNormalUser,loading,fetchAll,paginate,nameOrganization,
      prefecture,city,tel,changeItemPerPage,prefectureArr,headerSystemAdmin, sortBy,

		} = useStore()
		return {
      search,
      listAction,listHandel,fetchAll,headerSystemAdmin,headerNormalUser,loading,listTable,paginate,
      nameOrganization,prefecture,city,tel,dialogs,snackbar,onDelete,onDialog,onDetail,
      changeItemPerPage,registerModule,id,organization,itemPerPage, textNoData,
      address,replacePrefecture,prefectureArr,deleteSelected,exportCsv,organizationArr,
      clickRow,updateSort,loadingComponent,role, updateSortDesc, headerProps,
      ids, deleteMulti,sortBy,
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

