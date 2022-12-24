<template>
  <v-tab-item class="pt-6 row" :transition="false">
    <v-col cols="12" sm="3" md="3">
      <div class=" pt-8" style="cursor:context-menu" v-if="isEvent">参照可能支部・委員会</div>
      <div class=" pb-0 pt-8" style="cursor:context-menu" v-if="!isEvent">{{ textBranch }}</div>

      <perfect-scrollbar :settings="perfectScrollbarSettings" class="ps-email-left-sidebar">
        <v-treeview
          v-model="boardId"
          @input="changeDepartment"
          :selectable="!role"
          :items="treeBoard"
          style="width:260px"
          selection-type="independent"
        ></v-treeview>
      </perfect-scrollbar>
    </v-col>
    <v-col cols="12" sm="9" md="9" class="pa-5">
      <div class="d-flex align-center ">
        <div class="pl-0 col-md-4 col-sm-7 col-6">
          <v-select
          v-model="notifyGroup"
          clearable
          hide-details="auto"
          :label="this.$trans('app.notify_destination')"
          :items="listNotifyGroup"
          item-text="name"
          item-value="id"
          :disabled="role"
          :no-data-text="textNoData"
        >
        </v-select>
        </div>
        <div class="col-md-3 col-sm-5 col-6">
          <v-btn color="primary" class="col-3" @click="getListUser()">{{this.$trans("app.notify_des_setting")}}</v-btn>
        </div>
      </div>
      <div class="mt-3 overflow-table">
        <v-data-table
          :footer-props="{
            itemsPerPageOptions: itemPerPage,
            'items-per-page-text': this.$trans('app.itemPerPage')
          }"
          :headers="tableHeader"
          :show-select="!role"
          :loading="loading"
          class="elevation-1"
          :items="userFiltered"
          :loading-text="this.$trans('app.loading_item')"
          v-model="userSelected"
          :value="userSelected"
          :items-per-page="-1"
          @input="changeUser"
          :page.sync="paginate.currentPage"
          :server-items-length="paginate.total"
          @update:items-per-page="changeItemPerPage"
          @update:page="getUser($event, true)"
        >
          <template v-slot:no-data>
            {{ textNoData }}
          </template>
          <template #[`item.company`]="{item}">
            <span v-if="item.company">{{ item.company.name }}</span>
          </template>
        </v-data-table>
      </div>
    </v-col>
  </v-tab-item>
</template>

<script>
import { mdiPlus } from "@mdi/js";
import useStore from "./notifyGroup";
import StoreModule from "./StoreModule";
import { itemPerPage, registerModule, textNoData } from "@/common";
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import { computed } from "@vue/composition-api";
import { organizationActive } from '@/common'
import store from "@/store";
export default {
  components: {
    PerfectScrollbar
  },
  props: {
    userNotify: { type: Array, default: () => [] },
    changeDepartment: { type: Function },
    stateSelectUser: { type: Boolean, default: null },
    textBranch: { type: String, default: organizationActive.name },
    departmentOfUser : {type : Array },
    readonly : {type: Boolean, default: false},
    isEvent : {type: Boolean, default: false},
  },
  data() {
    return {
      action: "",
      notifyGroup: "",
      userFiltered: [],
      statusMerge : 0,
    };
  },
  mounted() {
    this.userSelected = this.userNotify
    this.mergeArray()
    this.boardId = this.departmentOfUser && this.departmentOfUser.length > 0 ? this.departmentOfUser : []
  },
  watch: {
    departmentOfUser(val) {
      this.userFiltered = this.listUser
    },
    userNotify: function(newVal) {
      this.userSelected = newVal
      this.listUser = newVal
    },
    listUser: function(newVal) {
        this.mergeArray()
    },
    listUserMailingList: function(newVal) {
      let ids = new Set(this.listUser.map(item => item.id))
      let listUserMailingList = newVal.filter(item => !ids.has(item.id))
      this.listUser = [...this.listUser, ...listUserMailingList]
      this.userSelected = [...this.userSelected, ...listUserMailingList]
      if(this.stateSelectUser) {
        if (this.statusGetUser){
          this.statusGetUser = false;
          let boardID = newVal.map(item => item.departments ? item.departments.map(d => d.id) : []).flat()
          this.boardId = [...this.boardId, ...Array.from(new Set(boardID))]
          this.statusGetUser = false;
        }
      }
    },
  },
  methods: {
    changeUser(item) {
      this.$emit("users", item);
      this.$emit("board", this.boardId);
    },
    getListUser() {
      this.notifyGroupId = this.notifyGroup;
      if(this.notifyGroupId) {
        this.getUserMailingList()
      }
      this.statusGetUser = true;
    },
    clearData() {
      this.userSelected = []
      this.boardId = []
      this.notifyGroup = ''
      this.listUser = []
    },
    mergeArray() {
      let ids = new Set(this.userSelected.map(d => d.id))
      this.userFiltered = [ ...this.userSelected, ...this.listUser.filter(d => !ids.has(d.id))]
      this.paginate.total = this.userFiltered.length
    }
  },
  setup() {
    registerModule(StoreModule, "tabNotifyGroup");
    // Perfect Scrollbar
    const role = computed(() => {
      if(store.state.user.role){
        return store.state.user.role == 1 ? false : true
      }else if(store.state.user.is_super_admin){
        return false
      }
    })
    console.log(role.value);
    const perfectScrollbarSettings = {
      maxScrollbarLength: 70,
      wheelPropagation: false,
      wheelSpeed: 0.7
    };
    const {
      getUser,
      treeBoard,
      listUser,
      changeItemPerPage,
      tableHeader,
      boardId,
      paginate,
      listNotifyGroup,
      notifyGroupId,
      loading,
      userSelected,
      statusGetUser,
      boardIds,
      listUserMailingList,
      getUserMailingList
    } = useStore();
    return {
      role,
      listUserMailingList,
      statusGetUser,
      boardIds,
      getUser,
      treeBoard,
      listUser,
      changeItemPerPage,
      getUserMailingList,
      tableHeader,
      listNotifyGroup,
      notifyGroupId,
      itemPerPage,
      textNoData,
      boardId,
      paginate,
      perfectScrollbarSettings,
      userSelected,
      loading,
      icons: {
        mdiPlus
      }
    };
  }
};
</script>
<style scoped>
.custom-placeholder {
  padding: 1rem;
}
.ps-email-left-sidebar {
  height: calc(100% - 10%) !important;
}
.v-text-field {
  padding-top: 0 !important;
}
/*.overflow-table{*/
/*  height: 300px;*/
/*  overflow: auto;*/
/*}*/
.pt-35{
  padding-top: 35px;
}
</style>
