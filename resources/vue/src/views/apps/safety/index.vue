<template>
  <div>
    <v-card>
      <action-component
        v-if="admin"
        :listHandel="listHandel"
        :dialogs="dialogs"
        @changeAction="changeAction"
        :executionAction="executionAction"
        :disableAction="disableAction"
      ></action-component>
      <v-divider class="my-2 mx-3"></v-divider>
      <v-row class="ma-0">
        <v-col md="3" sm="6" cols="12">
          <v-text-field
            @change="searchSafeties"
            @keyup.enter="searchSafeties(title)"
            v-model="title"
            :label="this.$trans('app.title')"
            outlined
            hide-details
            dense
            class=""
          ></v-text-field>
        </v-col>
        <v-col md="3" sm="6" cols="12">
          <v-text-field
            @change="searchSafeties"
            @keyup.enter="searchSafeties(department)"
            v-model="department"
            :label="this.$trans('app.branch_committee')"
            outlined
            hide-details
            dense
          >
          </v-text-field>
        </v-col>
      </v-row>
      <!-- end search -->
      <v-row class="ma-0">
        <v-col cols="12" sm="12" md="12">
          <v-data-table
          :header-props="headerProps"
            v-model="selectedSafeties"
            :footer-props="{
              itemsPerPageOptions: itemPerPage,
              'items-per-page-text': this.$trans('app.itemPerPage')
            }"
            :items="listTable"
            :headers="admin ? tableAdmin : tableColumns"
            :show-select="selectTableAdmin()"
            class="elevation-1"
            :loading-text="this.$trans('app.loading_item')"
            :loading="loading"
            :page.sync="paginate.currentPage"
            :items-per-page.sync="paginate.itemsPerPage"
            :server-items-length="paginate.total"
            @update:items-per-page="listSafeties(paginate.currentPage, $event)"
            @update:page="listSafeties($event, paginate.itemsPerPage)"
            @update:sort-desc="getSort($event)"
            @update:sort-by="updateSort($event)"
            @click:row="onClickAdmin"
          >
            <template v-slot:no-data>
              {{ textNoData }}
            </template>
            <template #[`item.emergency`]="{ item }">
              <v-icon v-if="item.emergency === true" color="error">{{
                icons.mdiAlertOutline
              }}</v-icon>
            </template>
            <!-- <template #[`item.department`]="{ item }">
              {{ item.department.name }}
            </template> -->

            <template #[`item.response`]="{ item }">
              <span v-if="item.response.answer == 0" class="border-0"> </span>
              <span v-if="item.response.answer == 1" class="text-center answer success">
                {{ answerList.safe }}
              </span>
              <span v-if="item.response.answer == 2" class="text-center answer danger">
                {{ answerList.spr }}
              </span>
            </template>
            <template #[`item.answer_rate`]="{ item }">
              <span v-if="item.answer_rate < 29" class="answer__rate answer__rate_danger"
                >{{ item.answer_rate }}%</span
              >
              <span
                v-if="29 < item.answer_rate && item.answer_rate < 80"
                class="answer__rate answer__rate_alert"
                >{{ item.answer_rate }}%</span
              >
              <span v-if="item.answer_rate > 79" class="answer__rate answer__rate_safe"
                >{{ item.answer_rate }}%</span
              >
            </template>
            <template #[`item.repeat`]="{ item }">
              <v-chip v-if="item.emergency" color="red" outlined small>要支援</v-chip>
              <v-chip v-if="!item.emergency" color="green" outlined small>無事</v-chip>
            </template>
            <!-- actions -->
            <template #[`item.actions`]="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item link v-if="item.can_response">
                    <v-list-item-title @click="getAnswer(item)">
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiCellphoneMessage }}
                      </v-icon>
                      <span>{{ listAction.toAnswers }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item link v-if="admin">
                    <v-list-item-title @click="getDataChartReply(item)">
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiSignal }}
                      </v-icon>
                      <span>{{ listAction.resultAnswers }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item link v-if="admin">
                    <v-list-item-title @click="onDetail(item)">
                      <v-icon size="20" class="me-2">
                        {{ icons.mdiFileDocumentOutline }}
                      </v-icon>
                      <span>{{ listAction.detail }}</span>
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item link v-if="admin">
                    <v-list-item-title @click="onDialog(item)">
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
    <add-safety :listBoard="listBoard" :dialogs="dialogs" @refetch-data="listSafeties"></add-safety>
    <detail-safety :listBoard="listBoard" :inputVal="safety" :dialogs="dialogs" @refetch-data="listSafeties"></detail-safety>
    <confirm-component
      :dialogs.sync="dialogs"
      :onDelete="onDelete"
      :module="this.$trans('app.safe_confirm')"
    >
    </confirm-component>
    <answer-component :dialogs="dialogs" :safetyAnswer="safety" @refetch-data="listSafeties"></answer-component>
    <result-component :dialogs="dialogs" :inputVal="replySafety"></result-component>
    <alert-component :snackbar="snackbar"></alert-component>
    <loading-component :loading="loadingComponent"></loading-component>
  </div>
</template>

<script>
import {
  mdiDotsVertical,
  mdiSignal,
  mdiPlus,
  mdiFileDocumentOutline,
  mdiDeleteOutline,
  mdiAlertOutline,
  mdiCellphoneMessage
} from "@mdi/js";
import store from "@/store";
import StoreModule from "./StoreModule";
import useStore from "./useStore";
import selectHandel from "@/common/components/selectHandel.vue";
import addSafety from "@/views/apps/safety/add/Add.vue";
import detailSafety from "@/views/apps/safety/detail/Detail.vue";
import ConfirmComponent from "@/common/components/ConfirmDelete.vue";
import { registerModule, itemPerPage, textNoData,headerProps } from "@/common/";
import { ref } from "@vue/composition-api";
import answerComponent from "./answer/index.vue";
import resultComponent from "./resultAnswer/index.vue";
import ActionComponent from "@/common/components/ActionComponent.vue";
import loadingComponent from "@/common/components/loading.vue";
import AlertComponent from "@/common/components/snackBar.vue";
export default {
  name: "index",
  components: {
    selectHandel,
    addSafety,
    ConfirmComponent,
    detailSafety,
    answerComponent,
    resultComponent,
    ActionComponent,
    loadingComponent,
    AlertComponent
  },
  data() {
    return {
      action: null,
      disableAction: true
    };
  },
  watch: {
    selectedSafeties() {
      this.checkEnableExecute();
    }
  },
  created() {
    this.listSafeties();
  },
  methods: {
    checkEnableExecute() {
      if (this.action && this.selectedSafeties.length > 0) {
        this.disableAction = false;
      } else {
        this.disableAction = true;
      }
    },
    changeAction(action) {
      this.action = action;
      this.checkEnableExecute();
    },
    executionAction() {
      let arrSafety = [];
      let safeties = JSON.parse(JSON.stringify(this.selectedSafeties));
      //delete memo
      if (this.action == "delete") {
        safeties.forEach(safety => {
          if (safety.id) {
            let dataSafety = {
              safety_id: safety.id,
              notified: safety.notified
            };
            arrSafety.push(dataSafety);
          }
        });
        this.deleteSelected(arrSafety);
      }
    },
    /** Search */
    searchSafeties(event){
        /** Add column search */
        this.queryParams.title = this.title;
        this.queryParams.department = this.department;
        /** Delete column search if value column null */
        if (!this.title) delete this.queryParams.title;
        if (!this.department) delete this.queryParams.department;
        //** Check params if params != null */

        if (this.title !== undefined|| this.department !== undefined) {
          /** Action search */
          this.listSafeties();
        }
    }
  },

  setup() {
    registerModule(StoreModule, "safeties");
    registerModule(StoreModule, "safety-replies");

    const tableAdmin = ref([
      { text: trans("app.support_required"), value: "emergency", align: "center", sortable: false },
      { text: trans("app.title"), value: "title", sortable: false },
      { text: trans("app.branch_committee"), value: "department", sortable: false },
      { text: trans("app.notified_at"), value: "notified_at", sortable: true },
      { text: trans("app.response_rate"), align: "center", value: "answer_rate", sortable: false },
      { text: "", value: "actions", align: "right", sortable: false }
    ]);

    const tableColumns = ref([
      { text: trans("app.title"), value: "title", sortable: false },
      { text: trans("app.branch_committee"), value: "department", sortable: false },
      { text: trans("app.notified_at"), value: "notified_at", sortable: true },
      { text: trans("app.answer"), value: "response", sortable: false },
      { text: "", value: "actions", align: "right", sortable: false }
    ]);
    /** Change page */
    const getPageListSafeties = pages => {
      queryParams.page = pages;
      listSafeties(queryParams);
    };
    /** Setup table select of user */
    const selectTableAdmin = () => {
      return admin.value;
    };
    const snackbar = ref({
      isColorSnackbarVisible: false,
      message: "",
      color: "error"
    });
    /** Setup data selected safeties */
    const selectedSafeties = ref([]);
    /** Action delete selected safeties */
    const deleteSelected = item => {
      if (!store.state.loading) {
        store.commit("TOGGLE_LOADING", true);
        store.dispatch("safeties/deleteSelected", { data: item }).then(response => {
          dialogs.value.dialog = false;
          store.commit("TOGGLE_LOADING", false);
          store.commit("setSnackbar", {
            isColorSnackbarVisible: true,
            color: "success",
            message: trans("message.delete_success")
          });
          listSafeties();
        });
      }
    };
    const onClickAdmin = item => {
      if (admin.value) {
        onDetail(item);
      } else {
        getAnswer(item);
      }
    };
    /** Get details safety*/
    const safety = ref({});
    const onDetail = item => {
      if (!store.state.loading) {
        store.commit("TOGGLE_LOADING", true);
        dialogs.value.detail = true;
        store
          .dispatch("safeties/fetchOne", { id: item.id })
          .then(response => {
            store.commit("TOGGLE_LOADING", false);
            safety.value = response.data.data;
          })
          .catch(error => {
            store.commit("TOGGLE_LOADING", false);
          });
      }
    };
    /** Action delete safety*/
    const onDeleteSafety = ref({});
    const onDialog = item => {
      dialogs.value.dialog = true;
      onDeleteSafety.value = item;
    };
    const onDelete = () => {
      if (!store.state.loading) {
        store.commit("TOGGLE_LOADING", true);
        store.dispatch("safeties/deleteID", { id: onDeleteSafety.value.id }).then(response => {
          dialogs.value.dialog = false;
          store.commit("TOGGLE_LOADING", false);
          store.commit("setSnackbar", {
            isColorSnackbarVisible: true,
            color: "success",
            message: trans("message.delete_success")
          });
          listSafeties();
        });
      }
    };
    /** Sort reply safety */

    const getSort = type => {
      queryParams.sort = type[0] ? "DESC" : "ASC";
      type[0] == undefined ? delete queryParams.sort : queryParams;
    };
    const updateSort = col => {
      column.value = col;
    };

    /** Get data chart reply answer */
    const replySafety = ref({});
    const getDataChartReply = item => {
      store.dispatch("safeties/chartReply", { id: item.id }).then(response => {
        replySafety.value = response.data.data;
        dialogs.value.resultAnswer = true;
      });
    };
    /** Action answer */
    const getAnswer = item => {
      safety.value = item;

      dialogs.value.answer = true;
    };

    const {
      queryParams,
      onReply,
      listHandel,
      listTable,
      dialogs,
      readStatus,
      loading,
      loadingComponent,
      key,
      listSafeties,
      title,
      department,
      listBoard,
      listAction,
      paginate,
      answerList,
      admin,
      column
    } = useStore();
    return {
      headerProps,
      onClickAdmin,
      tableColumns,
      getPageListSafeties,
      column,
      getSort,
      admin,
      answerList,
      getAnswer,
      selectTableAdmin,
      tableAdmin,
      listBoard,
      replySafety,
      getDataChartReply,
      onDeleteSafety,
      snackbar,
      safety,
      onDelete,
      selectedSafeties,
      queryParams,
      onReply,
      itemPerPage,
      deleteSelected,
      listTable,
      loadingComponent,
      listSafeties,
      title,
      department,
      listHandel,
      listAction,
      dialogs,
      readStatus,
      textNoData,
      paginate,
      onDialog,
      onDetail,
      loading,
      key,
      updateSort,
      icons: {
        mdiDotsVertical,
        mdiPlus,
        mdiFileDocumentOutline,
        mdiDeleteOutline,
        mdiAlertOutline,
        mdiCellphoneMessage,
        mdiSignal
      }
    };
  }
};
</script>

<style>
.answer {
  padding: 2px 0.5rem;
  height: 50% !important;
  color: #fff;
  border-radius: 10px;
  display: block;
  width: 60px;
}
.danger {
  background: #fd0b13 !important;
}
.answer__rate {
  width: 65px !important;
  padding: 0 0.5rem;
  height: 50% !important;
  background: transparent !important;
  border-radius: 10px;
  border: 1px solid;
}
.answer__rate_danger {
  border-color: #fd0b13 !important;
  color: #fd0b13;
}
.answer__rate_alert {
  border-color: #fdc40b !important;
  color: #fdc40b;
}
.answer__rate_safe {
  border-color: #13d802 !important;
  color: #13d802;
}
.border-0 {
  border: 0 !important;
  background: transparent !important;
}
.text-start .border-0:hover {
  background: transparent !important;
}
.danger {
  background: #fda3a6;
  color: #fff;
}
</style>
