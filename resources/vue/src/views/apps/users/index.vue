<template>
<div>
	<v-card>
    <action-component :listHandel="listHandel" :disabled="isDisabled"
      :dialogs="dialogs" @changeAction="changeAction"
      :executionAction="executionAction"  :disableAction="disableAction"
      :module="'users'"
    >
    </action-component>
    <v-divider class="my-2 mx-3"></v-divider>
		<v-row class="ma-0">
      <div  class="input__search">
        <v-text-field v-model="nameOrganization" :label="this.$trans('app.name')" outlined @change="search" @keyup.enter="search(nameOrganization)" hide-details dense class=""></v-text-field>
      </div>
      <div  class="input__search">
        <v-text-field v-model="nameDepartment" :label="this.$trans('app.department_name')" outlined @change="search" @keyup.enter="search(nameDepartment)" hide-details dense class=""></v-text-field>
      </div>
      <div class="input__search">
        <v-text-field :label="this.$trans('app.company_name')" v-model="company" @change="search" @keyup.enter="search(company)"	outlined	hide-details dense>
        </v-text-field>
      </div>
      <div  class="input__search">
        <v-select clearable outlined dense hide-details="auto" @change="search" @keyup.enter="search(prefecture)" v-model="prefecture" :items="prefectureArr" item-text="Pref_name" item-value="Pref_code" :label="this.$trans('app.prefectures')">
        </v-select>
      </div>
      <div class="input__search">
        <v-text-field	:label="this.$trans('app.municipalities')" v-model="city" @change="search" @keyup.enter="search(city)"	outlined	hide-details	dense>
        </v-text-field>
      </div>
			<v-spacer></v-spacer>
		</v-row>
    <!-- end search -->
    <v-row class="ma-0">
      <v-col cols="12" sm="12" md="12">
        <v-data-table :headers="tableColumns" :header-props="headerProps"
         @input="changeUser" v-model="userArr" :items="listTable" :loading="loading" :page.sync="paginate.currentPage"
        :items-per-page="-1" item-key="id" :server-items-length.sync="paginate.total" @update:items-per-page="changeItemPerPage($event)"
        show-select class="elevation-1" @update:page="fetchAll($event)" :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
         :loading-text="this.$trans('app.loading_item')" @update:sort-by="updateSort($event)" @click:row="clickRow"
         @update:sort-desc="updateSortDesc($event)" :sort-desc="true">
        <template v-slot:no-data>
          {{ textNoData }}
        </template>
        <template v-slot :loading="loading">
            {{ loadingItem }}
        </template>
        <template #[`item.prefecture`]="{item}">
          {{ replacePrefecture(item.prefecture , address) }}
        </template>
        <template #[`item.departments`]="{item}">
          <span class="d-block" v-if="item.departments.length > 0" v-for="department in item.departments" :key="department.id">{{department.name}}</span>
        </template>
        <template #[`item.company`]="{item}">
          {{item.company.name ? item.company.name : ''}}
        </template>
        <template #[`item.prefecture`]="{item}">
            {{   replacePrefecture(item.company.prefecture, address) }}
        </template>
        <template #[`item.city`]="{item}">
            {{ item.company.city }}
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
             <v-list-item link @click="onDetail(item)">
              <!-- <v-list-item link @click="dialogs.addNew = true"> -->
                <v-list-item-title >
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
  <add-user :roles="roles" :companyD="companyD" :listBoard="listBoard" :dialogs="dialogs"  @refetch-data="fetchAll"></add-user>
  <detail-user :listBoard="listBoard" :roles="roles" :departmentsIDs="departmentIDs" :user="user" @refetch-data="fetchAll" :dialogs="dialogs"></detail-user>
  <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.user')"></confirm-component>
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
import addUser from '@/views/apps/users/add/Add.vue'
import detailUser from '@/views/apps/users/detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import { listHandel, listAction, registerModule, itemPerPage, textNoData, loadingItem, replacePrefecture, headerProps } from '@/common/'
import { ref , watch, computed } from '@vue/composition-api'
import { address } from '@/common/address'
import ActionComponent from '@/common/components/ActionComponent.vue'
import loadingComponent from '@/common/components/loading.vue'
import AlertComponent from '@/common/components/snackBar.vue'
export default{
	components : {
		selectHandel,
    addUser,
    ConfirmComponent,
    detailUser,
    ActionComponent,
    loadingComponent,
    AlertComponent
	},
  data(){
    return {
      action: null,
      disableAction: true,
      companyD : {},
      results : [],
      alert: false,
    }
  },
  watch:{
    userArr (){
      this.checkEnableExecute()
    }
  },
  mounted() {
    let param = this.$router.currentRoute.params
    if(param.method == 'addNew'){
      const { method, company } = param
      this.companyD = company
      this.dialogs.addNew = true

    }else if(param.method == 'detail'){
      const { method, user } = param
      this.onDetail(user)
    }
  },
  methods: {
    checkEnableExecute(){
      if(this.action && this.userArr.length > 0){
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
      arrBoard = JSON.parse(JSON.stringify(this.userArr))
      arrBoard.forEach(user => {
        if(user.id){
          arrId.push(user.id)
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
    changeUser(user){

    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    }
  },
	setup() {

    registerModule(StoreModule , 'users')
    const loadingComponent = ref(false)
    const updateSort = (sort) => {
      sortBy.value = sort.toString()
    }
    const updateSortDesc = (event) => {
      if(event){
        sortBy.value = '-'+sortBy.value
      }
      fetchAll(1)
    }
    const search = (event) => {
        fetchAll()
    }
    const dialogs = ref({ dialog : false, addNew : false , detail : false})
    const snackbar = ref({ isColorSnackbarVisible : false, message : '', color : 'error' })
    const id = ref(null)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
    }
    const clickRow = (item) => {
      onDetail(item)
    }
    const onDelete = () => {
      dialogs.value.dialog = false
      store.dispatch('users/deleteID', { id : id.value })
		  .then(response => {
        fetchAll(1)
			   store.commit("setSnackbar",{
            isColorSnackbarVisible: true,
            color : 'success',
            message: trans('message.delete_success')
        })
        checkCount()
      })
      .catch(error => {
      })
    }
    const user = ref(null)
    const departmentIDs = ref([])
    const onDetail = (item) => {
        store.commit('TOGGLE_LOADING' , true)
        user.value = null
        store.dispatch('users/fetchOne', {id : item.id })
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          let idDepartment = response.data.data.departments.map(item => item.id)
          user.value = response.data.data
          departmentIDs.value = idDepartment
          dialogs.value.detail = true
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })

    }
    const userArr = ref([])
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!ids || ids.length <= 0 ) return
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        let queryParams = {
          userIds : ids.value,
          organization_id: JSON.parse(localStorage.getItem('Organization')).id
        }
        store.dispatch('users/deleteSelected', queryParams)
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          fetchAll(1)
          userArr.value = []
          store.commit("setSnackbar",{
            isColorSnackbarVisible: true,
            color : 'success',
            message: trans('message.delete_success')
          })
          checkCount()
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          userArr.value = []
          store.commit("setSnackbar",{
            isColorSnackbarVisible: true,
            color : 'success',
            message: trans('message.cant_delete', {':item' : trans('app.user')})
          })
        })
        deleteMulti.value = false
      }
    }
    const exportCsv = (ids) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('users/export', { userIds : ids })
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
            link.download = "user" + fileName + ".csv"
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
    const refetchUser = computed(() => {
      return store.state.refetchUser
    })
    watch(refetchUser, (newVal, oldVal) => {
      if(newVal){
        fetchAll(1)
      }
    })
   	const {
		  changeItemPerPage, checkCount, listBoard,	listTable,tableColumns,loading,fetchAll,
      paginate,nameOrganization,prefecture,city,company,prefectureArr,isDisabled, sortBy,
      nameDepartment,
      roles,
		} = useStore()
		return {
      search,listBoard,headerProps,
      listAction,listHandel,fetchAll,tableColumns,loading,listTable,paginate,roles,
      nameOrganization,prefecture,city,company,dialogs,snackbar,onDelete,onDialog,onDetail,
      registerModule,id,user,itemPerPage,textNoData,loadingItem,clickRow,loadingComponent,departmentIDs,
      replacePrefecture,address,updateSort,userArr,exportCsv,deleteSelected,prefectureArr,isDisabled,updateSortDesc,
      changeItemPerPage,nameDepartment,
      ids, deleteMulti,checkCount,sortBy,
			icons: {
				mdiDotsVertical,
        mdiPlus,
        mdiFileDocumentOutline,
        mdiDeleteOutline,
			}
		}
	}
}
</script>
<style scoped>
p {
  margin-bottom: 0px;
}
.input__search{
  margin: .8rem;
}
@media screen and (min-width: 768px) {
  .input__search{
    width:17% !important;
  }

}
@media screen and (max-width: 768px) {
  .input__search{
    width:38% !important;
  }

}
</style>
