<template>
  <div>
    <v-card>
      <action-component
        v-if="role"
        :listHandel="listHandel"
        :dialogs="dialogs"
        @changeAction="changeAction"
        :executionAction="executionAction"
        :disableAction="disableAction"
        :onAdd="onAdd"
      ></action-component>
    <v-divider class="my-2 mx-3"></v-divider>

      <v-row class="ma-0">
        <v-col md="3" sm="12" cols="6">
          <v-text-field
            @change="search"
            @keyup.enter="search(titleSearch)"
            v-model="titleSearch"
            :label="this.$trans('app.title')"
            outlined
            hide-details
            dense
            class=""
          ></v-text-field>
        </v-col>
        <v-col md="3" sm="12" cols="6">
          <v-text-field
            @change="search"
            @keyup.enter="search(departmentSearch)"
            v-model="departmentSearch"
            :label="this.$trans('app.branch_committee')"
            outlined
            hide-details
            dense
          >
          </v-text-field>
        </v-col>
      </v-row>
      <v-row class="ma-0">
        <v-col cols="12" md="12">
          <v-data-table
            v-model="selectedNotify"
            :footer-props="{
              itemsPerPageOptions: itemPerPage,
              'items-per-page-text': this.$trans('app.itemPerPage')
            }"
            sort-by-text="並び替え"
            :header-props="headerProps"
            :items="listTable"
            :headers="tableColumn"
            show-select
            class="elevation-1"
            :loading-text="this.$trans('app.loading_item')"
            :loading="loading"
            :page.sync="paginate.currentPage"
            :items-per-page.sync="paginate.itemsPerPage"
            :server-items-length="paginate.total"
            @update:items-per-page="changeItemPerPage($event)"
            @update:page="fetchAll"
            @click:row="clickRow($event)"
            @update:sort-by="updateSort($event)"
            @update:sort-desc="updateSortValue($event)"
            :sort-desc="true"
          >
            <template #[`item.read`]="{item}">
              <v-chip
                v-if="!role && item.can_response"
                color="error"
                class="text-center v-chip-light-bg error--text"
                small
                >{{ readStatus.unRead }}</v-chip
              >
              <v-chip
                v-if="!role && !item.can_response"
                color="success"
                class="text-center v-chip-light-bg success--text"
                small
                >{{ readStatus.readed }}</v-chip
              >
              <v-chip
                outlined
                small
                class="text-center"
                v-if="role && item.rate < 30"
                color="error"
                >{{ parseInt(item.rate) }}%</v-chip
              >
              <v-chip
                outlined
                small
                class="text-center"
                v-if="role && item.rate >= 30 && item.rate < 80"
                color="warning"
                >{{ parseInt(item.rate) }}%</v-chip
              >
              <v-chip
                outlined
                small
                class="text-center"
                v-if="role && item.rate >= 80"
                color="success"
                >{{ parseInt(item.rate) }}%</v-chip
              >
            </template>
            <template v-slot:no-data>
              {{ textNoData }}
            </template>
            <template #[`item.start`]="{item}">
              <span>{{ formatTimeToDate(item.start) }}</span>
            </template>
            <template #[`item.actions`]="{item}">
              <v-menu bottom left v-if="role || item.can_response">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item v-if="item.can_response"  link @click="onReply(item)">
                    <v-list-item-title>
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiCommentCheckOutline }}
                      </v-icon>
                      <span>{{ listAction.replyNotify }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="role" link @click="onResult(item)">
                    <v-list-item-title>
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiSignal }}
                      </v-icon>
                      <span>{{ listAction.result }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="role" link @click="onDetail(item)">
                    <v-list-item-title>
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiFileDocumentOutline }}
                      </v-icon>
                      <span>{{ listAction.detail }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="role" link @click="onDialog(item)">
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
    <add-notify :allBoard="allBoard" :dialogs="dialogs" @refetch-data="fetchAll"></add-notify>
    <result-answer
      :dialogs="dialogs"
      :key="key"
      :rateReadInput="rateRead"
      :newsId="newsId"
      :listUserRepInput="listUserRep"
    ></result-answer>
    <reply-notify
      :dialogs="dialogs"
      :module="this.$trans('app.notify')"
      :inputVal="notify"
      @refetch-data="fetchAll"
    ></reply-notify>
    <confirm-delete
      :dialogs.sync="dialogs"
      :onDelete="onDelete"
      :module="this.$trans('app.notify')"
      :deleteMulti="deleteMulti"
      :deleteSelected="deleteSelected"
      @close="closeDelete"
    ></confirm-delete>
    <detail-notify
      :allBoard="allBoard"
      :inputVal="notify"
      :userInput="notify.users"
      :dialogs="dialogs"
      @refetch-data="fetchAll"
    ></detail-notify>
    <alert-component :snackbar="snackbar"></alert-component>
    <loading-component :loading="loadingComponent"></loading-component>
  </div>
</template>

<script>
import store from "@/store";
import useStore from "./use_store";
import selectHandel from "../../../common/components/selectHandel";
import StoreModule from "./StoreModule";
import {
  mdiPlus,
  mdiDeleteOutline,
  mdiFileDocumentOutline,
  mdiDotsVertical,
  mdiSignal,
  mdiCommentCheckOutline
} from "@mdi/js";
import { listAction, registerModule, itemPerPage, textNoData, formatTimeToDate,headerProps } from "@/common/";
import resultAnswer from "./resultAnswer/index";
import addNotify from "./add/index";
import detailNotify from "./detail/Detail.vue";
import confirmDelete from "@/common/components/ConfirmDelete.vue";
import ActionComponent from "@/common/components/ActionComponent.vue";
import replyNotify from "./replyNotify/index";
import { ref } from "@vue/composition-api";
import loadingComponent from "@/common/components/loading.vue";
import AlertComponent from '@/common/components/snackBar.vue';
export default {
  name: "index",
  components: {
    selectHandel,
    addNotify,
    detailNotify,
    resultAnswer,
    confirmDelete,
    ActionComponent,
    replyNotify,
    loadingComponent,
    AlertComponent
  },
  data() {
    return {
      action: null,
      disableAction: true
    };
  },
  mounted() {
    this.fetchAll();
  },
  watch:{
    selectedNotify (){
      this.checkEnableExecute()
    }
  },
  methods: {
    checkEnableExecute(){
      if(this.action && this.selectedNotify.length > 0){
        this.disableAction = false
      }
      else {
        this.disableAction = true
      }
    },
    changeAction(action) {
      this.action = action
      this.checkEnableExecute()
    },
    executionAction() {
      let arrNotify = [];
      let arrId = [];
      arrNotify = JSON.parse(JSON.stringify(this.selectedNotify));
      //delete memo
      if (this.action == "delete") {
        arrNotify.forEach(notify => {
          if (notify.id) {
            arrId.push(notify.id);
          }
        });
        this.ids = arrId
        this.deleteMulti = true
        this.dialogs.dialog = true
      }
    },
    clickRow(item) {
      if(this.role) {
        this.onDetail(item)
      }
      else {
        this.onReply(item)
      }
    },
    closeDelete() {
      this.ids = []
      this.deleteMulti = false
    }
  },
  setup() {
    registerModule(StoreModule, "notify");
    const id = ref(null);
    const snackbar = ref({ isColorSnackbarVisible: false, message: "", color: "error" });
    const updateSort = (event) => {
        sortBy.value = event.toString()
    };
    const updateSortValue = (event) => {
      if (event) {
        sortBy.value = '-' + sortBy.value
      }
      fetchAll(1);
    };
    const search = (event) => {
        fetchAll()
    }
    const onDialog = item => {
      dialogs.value.dialog = true;
      id.value = item.id;
    };
    const onDelete = () => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false;
        store
          .dispatch("notify/deleteID", { id: id.value })
          .then(response => {
            fetchAll();
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true;
            snackbar.value.color = "success";
            snackbar.value.message = response.data.message;
          })
          .catch(error => {
            store.commit('TOGGLE_LOADING' , false)
          });
      }
    };
    const onAdd = () => {
      getDepartments()
      dialogs.value.addNew = true
    }
    const onDetail = item => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        getDepartments()
        store.dispatch("notify/fetchOne", { id: item.id }).then(response => {
          dialogs.value.detailNotify = true;
          store.commit('TOGGLE_LOADING' , false)
          notify.value = response.data.data
        })
      .catch(error => {
        store.commit('TOGGLE_LOADING' , false)
      });
      }
    };
    const ids = ref([])
    const deleteMulti = ref(false)
    const deleteSelected = () => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        dialogs.value.dialog = false
        store
          .dispatch("notify/deleteSelected", { ids: ids.value })
          .then(response => {
            fetchAll();
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true;
            snackbar.value.color = "success";
            snackbar.value.message = response.data.message;
          })
          .catch(error => {
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true;
            snackbar.value.color = "error";
            snackbar.value.message = error.msg;
          });
        deleteMulti.value = false
      }
    };
    const {
      listHandel,
      tableColumn,
      listTable,
      dialogs,
      readStatus,
      selectedNotify,
      rateRead,
      newsId,
      loading,
      loadingComponent,
      role,
      key,
      fetchAll,
      titleSearch,
      departmentSearch,
      paginate,
      changeItemPerPage,
      listUserRep,
      fetchOne,
      getUserReply,
      onReply,
      notify,
      onResult,
      getDepartments, allBoard,
      sortBy
    } = useStore();
    return {
      headerProps,
      search,
      itemPerPage,
      deleteSelected,
      selectedNotify,
      loadingComponent,
      snackbar,
      fetchAll,
      changeItemPerPage,
      titleSearch,
      departmentSearch,
      listHandel,
      tableColumn,
      listTable,
      listAction,
      dialogs,
      onDelete,
      readStatus,
      textNoData,
      paginate,
      onDialog,
      onDetail,
      onAdd,
      loading,
      role,
      key,
      updateSort,updateSortValue,
      formatTimeToDate,
      listUserRep,
      fetchOne,
      getUserReply,
      onReply,
      notify,
      onResult,
      rateRead,
      newsId,
      ids, deleteMulti,
      sortBy,
      getDepartments, allBoard,
      icons: {
        mdiPlus,
        mdiDeleteOutline,
        mdiFileDocumentOutline,
        mdiDotsVertical,
        mdiSignal,
        mdiCommentCheckOutline
      }
    };
  }
};
</script>

<style scoped>
.v-chip {
  display: inline;
  width: 60px;
  padding: 1px 12px;
}
.v-chip .v-chip__content {
  width: 40px !important;
}
</style>
