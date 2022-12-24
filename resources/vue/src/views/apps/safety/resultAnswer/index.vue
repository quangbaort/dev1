<template>
  <div>
    <v-dialog v-model="dialogs.resultAnswer" max-width="850" persistent scrollable>
      <v-card>
        <v-card-title class="headline lighten-2" primary-title>
          <span class="headline">{{ this.$trans("app.safety_confirm_answer_list") }}</span>
        </v-card-title>
        <v-tabs v-model="currentTab" icons-and-text grow>
          <v-tab>
            {{ this.$trans("app.response_status") }}
            <v-icon>{{ icons.mdiSignal }}</v-icon>
          </v-tab>
          <v-tab @change="getListReplies()">
            {{ this.$trans("app.detail") }}
            <v-icon>{{ icons.mdiFileDocumentOutline }}</v-icon>
          </v-tab>
        </v-tabs>
        <v-tabs-items v-model="currentTab" class="px-0 px-md-8">
          <v-tab-item class="pt-6 tab-overflow" :transition="false">
            <v-row>
              <v-col cols="12" md="6" sm="6">
                <v-menu
                  ref="menu"
                  v-model="menu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  width="270"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      hide-details="auto"
                      style="padding-top: 0"
                      v-model="startDateFormated"
                      :label="textDate.startDate"
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    hide-details="auto"
                    @input="menu = false"
                    locale="ja"
                    no-title
                    scrollable
                    :day-format="startDate => new Date(startDate).getDate()"
                    @change="$event => (startDate = $event)"
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6" sm="6">
                <v-menu
                  ref="menu1"
                  v-model="menu1"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  width="270"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      hide-details="auto"
                      style="padding-top: 0"
                      v-model="endDateFormated"
                      :label="textDate.endDate"
                      readreadonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    @input="menu1 = false"
                    locale="ja"
                    no-title
                    scrollable
                    :day-format="endDate => new Date(endDate).getDate()"
                    @change="$event => (endDate = $event)"
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6" sm="6">
                <div class="Chart">
                  <chart-data
                    class="box-chart"
                    ref="skills_chart"
                    :key="key"
                    :chart-data="chartData"
                    :options="options"
                  ></chart-data>
                </div>
              </v-col>
              <v-col cols="12" md="6" sm="6">
                <v-row align-center>
                  <v-col>
                    <h3>{{ this.$trans("app.response_rate") }} :</h3>
                  </v-col>
                  <v-col class="text-right">
                    <v-chip
                      v-if="answered < 30"
                      color="red"
                      outlined
                      style="width: 50%"
                      class="d-inline-block text-center"
                      >{{ answered }}%</v-chip
                    >
                    <v-chip
                      v-else-if="answered >= 20 && answered < 80"
                      color="warning"
                      outlined
                      style="width: 50%"
                      class="d-inline-block text-center"
                      >{{ answered }}%</v-chip
                    >
                    <v-chip
                      v-else-if="answered >= 80"
                      color="green"
                      outlined
                      style="width: 50%"
                      class="d-inline-block text-center"
                      >{{ answered }}%</v-chip
                    >
                  </v-col>
                </v-row>
                <v-divider class="my-2"></v-divider>
                <div class="pt-5 mt-5 align-center">
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : #ccc"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="3">
                      <div>{{ this.$trans("app.no_response") }}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ no_answer }}</div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : green"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="3">
                      <div>{{ this.$trans("app.safe") }}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ safe }}</div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : red"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="3">
                      <div>{{ this.$trans("app.support_required") }}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ unsafe }}</div>
                    </v-col>
                  </v-row>
                </div>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="pt-6 tab-overflow" :transition="false">
            <v-row>
              <v-col cols="12" md="3" sm="3" class="pl-0 pr-0 pr-sm-3">
                <v-text-field
                  hide-details="auto"
                  outlined
                  :label="this.$trans('app.name')"
                  dense
                  v-model="name"
                  @keyup.enter="checkParam()"
                  @change="checkParam()"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" sm="3" class="pl-0 pr-0 pl-sm-3 pr-sm-3">
                <v-text-field
                  hide-details="auto"
                  outlined
                  :label="this.$trans('app.company')"
                  dense
                  v-model="company"
                  @keyup.enter="checkParam()"
                  @change="checkParam()"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" sm="3" class="pl-0 pr-0 pl-sm-3 pr-sm-3">
                <v-menu
                  ref="menu2"
                  v-model="menu2"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  width="270"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      outlined
                      dense
                      style="padding-top: 0"
                      v-model="startDateFormated1"
                      :label="textDate.startDate"
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    @input="menu2 = false"
                    locale="ja"
                    no-title
                    scrollable
                    :day-format="startDate1 => new Date(startDate1).getDate()"
                    @change="$event => (startDate1 = $event)"
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="3" sm="3" class="pr-0 pl-0 pl-sm-3">
                <v-menu
                  ref="menu3"
                  v-model="menu3"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  width="270"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      outlined
                      dense
                      style="padding-top: 0"
                      v-model="endDateFormated1"
                      :label="textDate.endDate"
                      readreadonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    @input="menu3 = false"
                    locale="ja"
                    no-title
                    scrollable
                    :day-format="endDate1 => new Date(endDate1).getDate()"
                    @change="$event => (endDate1 = $event)"
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
            <v-row class="w-100">
              <v-data-table
                :header-props="headerProps"
                :items="listTable"
                :footer-props="{
                  itemsPerPageOptions: itemPerPage,
                  'items-per-page-text': this.$trans('app.itemPerPage')
                }"
                :headers="tableColumns"
                style="width:100%"
                class="elevation-1 w-100"
              >
                <template v-slot:no-data>
                  {{ textNoData }}
                </template>
                <template #[`item.answer`]="{ item }">
                  <span v-if="item.answer === 0" class="border-0"> </span>
                  <span v-if="item.answer === 1" class="text-center d-block m-auto answer success">
                    無事
                  </span>
                  <span
                    v-if="item.answer === 2"
                    outlined
                    small
                    class="text-center m-auto d-block answer danger"
                  >
                    要支援
                  </span>
                </template>
                <template #[`item.response`]="{ item }">
                  <span v-if="item.response === 0" class="d-block text-center"></span>
                  <span v-if="item.response === 1" class="d-block text-center">
                    対応中
                  </span>
                  <span v-if="item.response === 2" class="d-block text-center">
                    対応済み
                  </span>
                </template>
                <template #[`item.answered_at`]="{ item }">
                  {{ formatTimeToDate(item.answered_at) }}
                </template>
                <template #[`item.name`]="{ item }">
                  {{ item.user.name }}
                </template>
                <template #[`item.company`]="{ item }">
                  {{ item.user.company.name }}
                </template>
                <template #[`item.actions`]="{item}">
                  <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }" >
                      <div class="d-flex justify-content-end">
                        <v-icon v-if="item.comment" @click="getComment(item)">{{
                          icons.mdiCommentProcessingOutline
                        }}</v-icon>
                        <v-btn v-if="item.answer === 2" icon v-bind="attrs" v-on="on">
                          <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                        </v-btn>
                      </div>
                    </template>
                    <template >
                      <v-list>
                        <v-list-item link @click="postResReply(item, 1)">
                          <v-list-item-title>
                            <v-icon size="20" class="me-2">
                              {{ icons.mdiMedicalBag }}
                            </v-icon>
                            <span>{{ listActionSafety.during }}</span>
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item link @click="postResReply(item, 2)">
                          <v-list-item-title>
                            <v-icon size="20" class="me-2">
                              {{ icons.mdiCheckCircleOutline }}
                            </v-icon>
                            <span>{{ listActionSafety.acp }}</span>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </template>
                  </v-menu>
                </template>
              </v-data-table>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
        <v-divider class="my-2"></v-divider>
        <v-card-actions>
          <v-row class="justify-end py-3">
            <v-btn color="default" class="mx-2" @click="close()">{{
              this.$trans("app.close")
            }}</v-btn>
            <v-btn color="success" class="mx-2" @click="resendMail()">
              {{ resendMailSuccess }}
            </v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <commentComponent :dialogs="dialog" :inputComment="inputComment"></commentComponent>
  </div>
</template>
<script>
import {
  mdiOfficeBuildingOutline,
  mdiDotsVertical,
  mdiCheckCircleOutline,
  mdiMedicalBag,
  mdiSignal,
  mdiMapMarkerOutline,
  mdiFileDocumentOutline,
  mdiCommentProcessingOutline,
  mdiLinkVariant
} from "@mdi/js";
import chartData from "@/common/components/Chart.vue";
import useStore from "./userAnswerStore";
import StoreModule from "../StoreModule";
import store from "@/store";
import { ref, watch } from "@vue/composition-api";
import { itemPerPage, formatDate, registerModule, textNoData, formatTimeToDate, headerProps } from "@/common";
import commentComponent from "../comment/index.vue";
import moment from "moment";
import axios from "@axios";
export default {
  props: {
    dialogs: {
      type: Object,
      default: { addUser: false }
    },
    inputVal: {
      type: Object,
      default: {}
    }
  },
  components: {
    chartData,
    commentComponent
  },
  data() {
    return {
      dialog: {
        comment: false
      },
      name: undefined,
      company: undefined,
      inputComment: {},
      listTable: [],
      key: 0,
      safety_id: "",
      noAnswer: 0,
      safe: 0,
      support: 0,
      totalAnswers: 0,
      currentTab: null,
      menu: false,
      modal: false,
      menu1: false,
      menu2: false,
      menu3: false,
      startDate: null,
      startDateFormated: null,
      endDateFormated: null,
      endDate: "",
      startDate1: null,
      endDate1: null,
      startDateFormated1: null,
      endDateFormated1: null,
      resendMailSuccess: this.$trans("app.re_safety_to_unanswered"),
      unsafe: 0,
      safe: 0,
      no_answer: 0,
      answered: 0,
      queryParams: null,
      options: {
        responsive: true,
        maintainAspectRatio: true,
        animation: {
          animateRotate: false
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      },
      chartData: {
        labels: [trans("app.no_response"), trans("app.safe"), trans("app.support_required")],
        datasets: [
          {
            backgroundColor: ["#ccc", "green", "red"],
            data: [10, 10, 80]
          }
        ]
      }
    };
  },

  watch: {
    inputVal(value) {
      this.rate = this.inputVal;
      this.safetyId = this.inputVal.id;
      this.safe = this.inputVal.safe;
      this.no_answer = this.inputVal.no_answer;
      this.unsafe = this.inputVal.unsafe;
      this.chartData.datasets[0].data = [this.no_answer, this.safe, this.unsafe];
      this.key = Math.random() * 999;
      this.answered = value.answered;
    },
    startDate(val) {
      this.startDateFormated = this.formatDate(val);
      this.getChart();
    },
    endDate(val) {
      this.endDateFormated = this.formatDate(val);
      this.getChart();
    },
    startDate1(val) {
      this.startDateFormated1 = this.formatDate(val);
      this.checkParam();
    },
    endDate1(val) {
      this.endDateFormated1 = this.formatDate(val);
      this.checkParam();
    }
  },
  computed: {
    currentDataSet() {
      return this.chartData.datasets[0].data;
    },
    computedStartDateFormated() {
      return formatDate(this.startDate);
    },
    computedEndDateFormated() {
      return formatDate(this.endDate);
    },
    computedStartDateFormated1() {
      return formatDate(this.startDate1);
    },
    computedEndDateFormated1() {
      return formatDate(this.endDate1);
    }
  },
  methods: {
    /** Get chart data*/
    getChart() {
      const queryParams = {};
      this.startDate ? (queryParams.startDate = this.startDate) : delete queryParams.startDate;
      this.endDate ? (queryParams.endDate = this.endDate) : delete queryParams.endDate;
      store.commit("TOGGLE_LOADING", true);
      store
        .dispatch("safeties/chartReply", { id: this.safetyId, includes: "responses", queryParams })
        .then(response => {
          store.commit("TOGGLE_LOADING", false);
          this.key = Math.random() * 999;
          this.no_answer = response.data.data.no_answer;
          this.safe = response.data.data.safe;
          this.unsafe = response.data.data.unsafe;
          this.answered = response.data.data.answered;
          this.chartData.datasets[0].data = [this.no_answer, this.safe, this.unsafe];
        })
        .catch(error => {});
    },

    /** Get comment reply */
    getComment(item) {
      this.dialog.comment = true;
      this.inputComment = item;
    },
    /** Action admin responsive  */
    postResReply(item, res) {
      let safetyId = this.safetyId;
      let data = {
        user_id: item.user_id,
        notified_at: moment(item.notified_at).format("YYYY-MM-DD"),
        response: res
      };

      store.commit("TOGGLE_LOADING", true);
      axios.post("/safeties/" + safetyId + "/response-answer", data).then(res => {
        store.commit("TOGGLE_LOADING", false);
        this.getListReplies(this.queryParams);
      });
    },
    /** Get list replies safety */
    getListReplies(queryParams, limit = 10, page = 1) {
      store.commit("TOGGLE_LOADING", true);
      store
        .dispatch("safeties/chartReply", {
          id: this.inputVal.id,
          includes: "responses",
          queryParams
        })
        .then(response => {
          store.commit("TOGGLE_LOADING", false);
          const meta = response.data.meta;
          this.listTable = response.data.data.responses ?? [];
          // paginate.value = {
          //   total: meta.total,
          //   currentPage: parseInt(meta.current_page),
          //   itemsPerPage: parseInt(meta.per_page),
          //   pageStart: meta.from,
          //   pageStop: meta.last_page,
          //   pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          //   itemsLength: meta.last_page
          // };
        })
        .catch(error => {
          this.listTable = [];
        });
    },
    /** Resend Mail */
    resendMail() {
      store.commit("TOGGLE_LOADING", true);
      store
        .dispatch("safety-replies/resendMail", {
          id: this.safetyId
        })
        .then(response => {
          store.commit("TOGGLE_LOADING", false);
          store.commit("setSnackbar", {
            isColorSnackbarVisible: true,
            color: "success",
            message: trans("message.resend_mail_unanswered_success")
          });
        });
    },
    //** Check search title  */
    checkParam() {
      if (
        this.name === undefined &&
        this.company === undefined &&
        this.startDate1 === null &&
        this.endDate1 === null
      ) {
        return 1;
      };
      const queryParams = {};
      this.name ? (queryParams.name = this.name) : delete queryParams.name;
      this.company ? (queryParams.company = this.company) : delete queryParams.company;
      this.startDate1 ? (queryParams.startDate = this.startDate1) : delete queryParams.startDate1;
      this.endDate1 ? (queryParams.endDate = this.endDate1) : delete queryParams.endDate1;
      this.queryParams = queryParams;
      this.getListReplies(queryParams);
    },
    close() {
      this.dialogs.resultAnswer = false;
      this.currentTab = null;
      this.startDate = "";
      this.endDate = "";
      this.startDate1 = null;
      this.endDate1 = null;
      this.name = undefined;
      this.company = undefined;
      store.commit("TOGGLE_LOADING", false);
    }
  },

  setup(props) {
    registerModule(StoreModule, "safety-replies");
    const rate = ref(props.inputVal);

    const { tableColumns, listActionSafety, textDate, paginate, responseReply } = useStore();
    watch(props.inputVal, val => {});
    return {
      responseReply,
      paginate,
      registerModule,
      textDate,
      itemPerPage,
      formatDate,
      tableColumns,
      listActionSafety,
      textNoData,
      rate,
      formatTimeToDate,
      headerProps,
      icons: {
        mdiDotsVertical,
        mdiOfficeBuildingOutline,
        mdiCheckCircleOutline,
        mdiMedicalBag,
        mdiSignal,
        mdiMapMarkerOutline,
        mdiFileDocumentOutline,
        mdiCommentProcessingOutline,
        mdiLinkVariant
      }
    };
  }
};
</script>
<style scoped>
.cycle {
  width: 20px !important;
  height: 20px !important;
  border-radius: 50%;
}
.chart {
  height: 20px !important;
}
.justify-content-end {
  justify-content: flex-end !important;
}
.m-auto {
  margin: auto;
}
.tab-overflow {
  height: 460px;
  overflow: auto;
  padding: 0 15px 15px;
}
.v-input::v-deep .v-text-field__details {
  min-height: 0;
  height: 0;
}
.v-dialog {
  overflow: hidden;
}
.v-item-group {
  overflow-y: auto !important;
}
</style>
