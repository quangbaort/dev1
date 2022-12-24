<template>
  <div>
    <v-card>
      <action-component :listHandel="listHandel" :dialogs="dialogs" @changeAction="changeAction"
                        :executionAction="executionAction":disableAction="disableAction"></action-component>
      <v-divider class="my-2 mx-3"></v-divider>
      <v-row class="ma-0">
        <v-col md="3" sm="6" cols="6">
          <v-text-field @change="search" @keyup.enter="search(nameCompany)" v-model="nameCompany" :label="this.$trans('app.company_name')" outlined dense
          hide-details>
          </v-text-field>
        </v-col>
        <v-col md="3" sm="6" cols="6">
          <v-select @change="search" @keyup.enter="search(prefecture)" hide-details="auto" v-model="prefecture" item-value="Pref_code" item-text="Pref_name" dense
                    :label="this.$trans('app.prefectures')" :items="address" outlined clearable>
          </v-select>
        </v-col>
        <v-col md="3" sm="16" cols="6">
          <v-text-field @change="search" @keyup.enter="search(city)" v-model="city" :label="this.$trans('app.municipalities')" outlined hide-details dense>
          </v-text-field>
        </v-col>
        <v-col md="3" sm="6" cols="6">
          <v-text-field @change="search" @keyup.enter="search(tel)" v-model="tel" :label="this.$trans('app.tel')" outlined hide-details dense>
          </v-text-field>
        </v-col>
        <v-spacer></v-spacer>
      </v-row>
      <v-row class="ma-0">
        <v-col md="12" sm="12">
          <v-data-table v-model="selectedCompany" :header-props="headerProps" :headers="tableColumns" :items="listTable" :loading="loading" :page.sync="paginate.currentPage"
          :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage') }" show-select class="elevation-1"
          :items-per-page="paginate.itemsPerPage" @update:sort-by="updateSort($event)" @click:row="clickRow" :loading-text="this.$trans('app.loading_item')"
           @update:items-per-page="changeItemPerPage" @update:page="fetchAll" :server-items-length="paginate.total"
           @update:sort-desc="updateSortDesc($event)" :sort-desc="true">
            <template v-slot:no-data>
              {{ textNoData }}
            </template>
            <template #[`item.prefecture`]="{item}">
              {{ replacePrefecture(item.prefecture , address) }}
            </template>
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
    <add-company :dialogs="dialogs" @refetch-data="fetchAll"></add-company>
    <detail-company :company="company" @refetch-data="fetchAll" :dialogs="dialogs"></detail-company>
    <confirm-component :dialogs.sync="dialogs" :onDelete="onDelete" :deleteMulti="deleteMulti" :deleteSelected="deleteSelected" @close="closeDelete" :module="this.$trans('app.company')"></confirm-component>
  </div>
</template>
<script>
import { mdiDotsVertical, mdiPlus, mdiFileDocumentOutline, mdiDeleteOutline } from '@mdi/js'
import store from '@/store'
import StoreModule from './StoreModule'
import useStore from './useStore'
import selectHandel from '@/common/components/selectHandel.vue'
import addCompany from '@/views/apps/companies/add/Add.vue'
import detailCompany from '@/views/apps/companies/detail/Detail.vue'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import { listHandel , listAction , registerModule , itemPerPage , textNoData, replacePrefecture, headerProps } from '@/common/'
import { ref } from '@vue/composition-api'
import ActionComponent from '@/common/components/ActionComponent.vue'
import { address } from '@/common/address'
export default {
  components : {
    selectHandel,
    addCompany,
    ConfirmComponent,
    detailCompany,
    ActionComponent,
  },
  data() {
    return {
      action: null,
      disableAction: true,
      companyD : {},
      company : {},
    }
  },
  watch:{
    selectedCompany (){
      this.checkEnableExecute()
    }
  },
  created() {
    this.fetchAll()
    if(this.$router.currentRoute.params.method == 'addNew'){
      const { company } = this.$router.currentRoute.params
    }
  },
  methods : {
    checkEnableExecute(){
      if(this.action && this.selectedCompany.length > 0){
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
      arrCompany = JSON.parse(JSON.stringify(this.selectedCompany))
      arrCompany.forEach((company) => {
        if (company.id) {
          arrId.push(company.id)
        }
      });
      //delete company
      if (this.action == "delete") {
        this.ids = arrId
        this.deleteMulti = true
        this.dialogs.dialog = true
      }
      //export company
      else if (this.action == "export") {
        this.exportCsv(arrId)
      }
    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    },
    onDetail (item){
      store.commit('TOGGLE_LOADING' , true)
      store.dispatch('company/fetchOne', {id : item.id})
        .then(response => {
          this.company = response.data.data
          this.dialogs.detail = true
          store.commit('TOGGLE_LOADING' , false)
        })
    },
    clickRow (id) {
      this.onDetail(id)
    }
  },
  setup() {
    registerModule(StoreModule , 'company')
    const dialogs = ref({ dialog : false, addNew : false , detail : false})
    const id = ref(null)
    const onDialog = (item) => {
      dialogs.value.dialog = true
      id.value = item.id
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
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('company/deleteID', { id : id.value })
		      .then(response => {
            store.commit('TOGGLE_LOADING' , false)
            fetchAll()
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
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store.dispatch('company/deleteSelected', { ids : ids.value })
          .then(response => {
            store.commit('TOGGLE_LOADING' , false)
            fetchAll()
            store.commit('setSnackbar' ,{
              isColorSnackbarVisible : true,
              color : 'success',
              message : response.data.message
            })
            selectedCompany.value = []
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
        store
        .dispatch("company/exportCsv", { ids: ids })
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
          link.download = "Companies" + fileName + ".csv"
          link.click()
        })
        .catch((error) => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        });
      }
    };
    const search = (event)=> {
        fetchAll()
    }
    const {
			listTable,tableColumns,loading,fetchAll,paginate,nameCompany,prefecture,city,tel,changeItemPerPage,selectedCompany, sortBy
		} = useStore()
    return {
      listAction,listHandel,fetchAll,tableColumns,loading,listTable,paginate,
      nameCompany,prefecture,city,tel,dialogs,onDelete,onDialog,
      changeItemPerPage,registerModule,id,itemPerPage,textNoData,deleteSelected, selectedCompany,
      exportCsv, address,replacePrefecture,updateSort, updateSortDesc,
      ids, deleteMulti, sortBy,search,headerProps,
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
<style lang="scss">
</style>
