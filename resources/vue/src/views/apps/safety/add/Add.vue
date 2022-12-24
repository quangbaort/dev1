<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
        <v-card class="modal-safety">
          <v-card-title>
            <span class="headline">{{ this.$trans("app.safety_setting") }}</span>
          </v-card-title>
            <v-tabs v-model="currentTab" icons-and-text grow>
              <v-tab>
                {{ this.$trans("app.basic_information") }}
                <v-icon>{{ icons.mdiFileDocumentEditOutline }}</v-icon>
              </v-tab>
              <v-tab>
                {{ this.$trans("app.detail_information") }}
                <v-icon>{{ icons.mdiFileDocumentOutline }}</v-icon>
              </v-tab>

              <v-tab>
                {{ this.$trans("app.notify_destination") }}
                <v-icon>{{ icons.mdiAccountSupervisorOutline }}</v-icon>
              </v-tab>
              <v-tab>
                {{ this.$trans("app.repeat") }}
                <v-icon>{{ icons.mdiCalendarRefreshOutline }}</v-icon>
              </v-tab>
            </v-tabs>
            <v-card-text>
              <v-tabs-items v-model="currentTab">
                <v-tab-item class="px-8 pt-6" :transition="false">
                  <v-row>
                    <v-col cols="12" md="6" sm="6">
                      <v-text-field
                        class="input-title"
                        v-model="title"
                        hide-details="auto"
                        dense
                        :rules="[
                        validators.requiredField(title, labelTitle),
                        validators.maxLengthValidator(title, 255, labelTitle)
                      ]"
                        :error-messages="errorMessages.title"
                      >
                        <template v-slot:label>
                          {{ labelTitle }}
                          <b class="required-field">*</b>
                        </template>
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" sm="6">
                      <v-select
                        v-model="department"
                        hide-details="auto"
                        item-text="name"
                        item-value="id"
                        :items="listBoard"
                        dense
                        :rules="[validators.requiredField(department, departmentLabel)]"
                        :no-data-text="textNoData"
                      >
                        <template v-slot:label>
                          {{ departmentLabel }} <b class="required-field">*</b>
                        </template>
                      </v-select>
                    </v-col>
                  </v-row>
                </v-tab-item>
                <v-tab-item class="px-8 pt-6" :transition="false">
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-textarea
                        class="detail-textarea"
                        hide-details="auto"
                        :label="this.$trans('app.detail')"
                        auto-grow
                        outlined
                        rows="5"
                        dense
                        v-model="detail"
                        :rules="[validators.maxLengthValidator(detail, 1023, this.$trans('app.detail'))]"
                        :error-messages="errorMessages.detail"
                      ></v-textarea>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-text-field
                        v-model="detail_url"
                        hide-details="auto"
                        dense
                        :rules="[validators.maxLengthValidator(detail_url, 1023, 'URL'), validators.urlValidator(detail_url)]"
                      >
                        <template v-slot:label>URL</template>
                      </v-text-field>
                    </v-col>
                  </v-row>
                </v-tab-item>
                <tab-notify-group
                  ref="notifyGroupComponent"
                  @users="changeUser"
                  :userNotify="defaultVal"
                  :departmentOfUser="defaultVal"
                ></tab-notify-group>
                <v-tab-item class="mx-8" :transition="false">
                  <v-row class="pt-6">
                    <v-col cols="12" md="12" class="">
                      <v-switch
                        hide-details="auto"
                        v-model="repeatOpen"
                        :label="this.$trans('app.repeat')"
                      ></v-switch>
                    </v-col>
                  </v-row>
                  <v-row v-if="repeatOpen">
                    <v-col cols="12" md="12">
                      <v-radio-group class="radio-group" v-model="repeat" @change="changeRadio($event)">
                        <v-row>
                          <v-col cols="12" md="12" class="">
                            <v-radio :label="this.$trans('app.every_day')" value="1"></v-radio>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col cols="12" md="12">
                            <v-row class="align-center">
                              <v-col cols="6" md="2">
                                <v-radio :label="this.$trans('app.every_week')" value="2"></v-radio>
                              </v-col>
                              <v-col cols="6" md="9" v-if="repeat == 2">
                                <v-row>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="1"
                                      :label="this.$trans('app.mon')"
                                      value="1"
                                    >
                                    </v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      :label="this.$trans('app.tue')"
                                      v-model="repeat_week"
                                      input-value="2"
                                      value="2"
                                    ></v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="3"
                                      :label="this.$trans('app.wed')"
                                      value="3"
                                    ></v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="4"
                                      :label="this.$trans('app.thu')"
                                      value="4"
                                    ></v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="5"
                                      :label="this.$trans('app.fri')"
                                      value="5"
                                    ></v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="6"
                                      :label="this.$trans('app.sat')"
                                      value="6"
                                    ></v-checkbox>
                                  </v-col>
                                  <v-col>
                                    <v-checkbox
                                      v-model="repeat_week"
                                      input-value="0"
                                      :label="this.$trans('app.sun')"
                                      value="0"
                                    ></v-checkbox>
                                  </v-col>
                                </v-row>
                              </v-col>
                            </v-row>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col cols="12" md="2">
                            <v-radio :label="this.$trans('app.every_month')" value="3"></v-radio>
                          </v-col>
                          <v-col cols="12" md="2" v-if="repeat == 3">
                            <v-select
                              v-model="repeat_date"
                              :disabled="disabledInput"
                              :label="this.$trans('app.day')"
                              :items="dayInMonthDefault()"
                              :rules="[validators.checkDay(repeat_date)]"
                              outlined
                              :no-data-text="textNoData"
                              dense
                            >
                            </v-select>
                          </v-col>
                        </v-row>
                      </v-radio-group>
                    </v-col>
                  </v-row>
                  <!-- <v-divider></v-divider> -->
                  <v-row class="my-2" v-if="repeatOpen">
                    <v-col cols="3" md="2" class="align-self-center">
                      <p>{{ this.$trans("app.period") }}</p>
                    </v-col>
                    <v-col cols="12" md="5">
                      <v-menu
                        ref="menu"
                        v-model="menu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        width="270"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            style="padding-top: 0"
                            v-model="startDateFormated"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            :rules="[validators.checkStartDate(startDate, repeatStartField)]"
                            hide-details="auto"
                            outlined dense
                          >
                            <template v-slot:label>
                              {{ repeatStartField }}
                            </template>
                          </v-text-field>
                        </template>
                        <v-date-picker
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
                    <v-col cols="12" md="5">
                      <v-menu
                        ref="menu1"
                        v-model="menu1"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        width="270"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            style="padding-top: 0"
                            v-model="endDateFormated"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            :rules="[validators.checkEndDate(startDate, endDate, repeat)]"
                            hide-details="auto"
                            outlined dense
                          >
                            <template v-slot:label>
                              {{ repeatEndField }}
                            </template>
                          </v-text-field>
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
                </v-tab-item>
              </v-tabs-items>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-row class="justify-end py-3">
                <v-btn color="default" class="mx-2" @click="close">{{this.$trans("app.close") }}</v-btn>
                <v-btn color="success" type="submit" class="mx-2">{{this.$trans("app.save") }}</v-btn>
              </v-row>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>
    <loading-component :loading="loadingComponent"></loading-component>
  </div>
</template>

<script>
import store from "@/store";
import {
  maxLengthValidator,
  lengthValidator,
  integerValidator,
  requiredField,
  checkStartDate,
  checkEndDate,
  checkDay,
  urlValidator
} from "@core/utils/validation";
import AlertComponent from "@/common/components/snackBar.vue";
import { ref } from "@vue/composition-api";
import useStore from "../useStore";
import useAddStore from "./useAddStore";
import pagination from "@/common/components/Pagination.vue";
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import { itemPerPage, formatDate, deparmentActive } from "@/common";
import TabNotifyGroup from "@/common/components/tabNotifyGroup/TabNotifyGroup.vue";
import moment from "moment";
import loadingComponent from "@/common/components/loading.vue";
import axios from "@axios";
import {
  mdiPhone,
  mdiHeartOutline,
  mdiAccountBoxOutline,
  mdiAccountCircle,
  mdiOfficeBuildingOutline,
  mdiAccountSupervisorOutline,
  mdiLockOutline,
  mdiPlus,
  mdiMapMarkerOutline,
  mdiPhoneInTalkOutline,
  mdiNotebookEditOutline,
  mdiFileDocumentEditOutline,
  mdiFileDocumentOutline,
  mdiCalendarSyncOutline,
  mdiCalendarRefreshOutline
} from "@mdi/js";

export default {
  computed: {
    computedWidth: function() {
      return this.width;
    }
  },
  components: {
    AlertComponent,
    pagination,
    PerfectScrollbar,
    TabNotifyGroup,
    loadingComponent
  },
  props: {
    dialogs: {
      type: Object,
      default: { addUser: false }
    },
    listBoard : {
      type : Array,
      default: []
    }
  },
  watch: {
    startDate(val) {
      this.startDateFormated = this.formatDate(this.startDate);
    },
    endDate(val) {
      this.endDateFormated = this.formatDate(this.endDate);
    }
  },
  data() {
    return {
      snackbar: {
        isColorSnackbarVisible: false,
        message: "",
        color: "error"
      },
      title: "",
      detail: "",
      detail_url: "",
      department: deparmentActive,
      repeatOpen: false,
      repeat: "1",
      repeat_week: [],
      repeat_date: "",
      repeat_start: "",
      repeat_end: "",
      repeatField: this.$trans("app.repeat"),
      repeatWeekField: this.$trans("app.repeat_week"),
      repeatDateField: this.$trans("app.repeat_date"),
      repeatStartField: this.$trans("app.start_date"),
      repeatEndField: this.$trans("app.end_date"),
      labelTitle: this.$trans("app.title"),
      departmentLabel: this.$trans("app.in_charge_branch_committee"),
      labelDetailUrl: this.$trans("app.detail"),
      switch2: false,
      width: 260,
      modal: false,
      currentTab: null,
      storageCapacity: "",
      errorMessages: {},
      userSelected: [],
      menu: false,
      menu1: false,
      startDate: "",
      startDateFormated: "",
      endDateFormated: "",
      endDate: "",
      disabledInput: true,
      dateResponseFormated: "",
      repeatWeek: false,
      repeatDate: false,
      disabled: false,
    };
  },
  methods: {
    /** Setup alert */
    alertMessage(type, message) {
      this.snackbar.isColorSnackbarVisible = true;
      this.snackbar.color = type;
      this.snackbar.message = trans("message." + message);
      return;
    },
    /** Setup repeat radio */
    changeRadio(e) {
      if (e === "1" || e == false) {
        this.disabled = true;
      } else if (e == "3") {
        this.disabled = true;
        this.disabledInput = false;
      } else if (e == "2") {
        this.disabled = false;
        this.disabledInput = true;
      }
    },


    /** Action add safety */
    handleFormSubmit() {
      if (!this.$refs.form.validate()) {
        return;
      }
      let arrUser = JSON.parse(JSON.stringify(this.userSelected));
      if (arrUser.length <= 0) {
        return this.alertMessage("error", "user_required");
      }
      let users = [];
      arrUser.forEach(user => {
        users.push(user.id);
      });

      // /** Check repeat week*/
      if (this.repeat == 2 && this.repeat_week == "") {
        return this.alertMessage("error", "please_select_date_week");
      }

      /**  Setup Params*/
      const queryParams = {
        title: this.title,
        department_id : this.department,
        organization_id: JSON.parse(localStorage.getItem("Organization")).id,
        detail: this.detail,
        detail_url: this.detail_url,
        repeat: this.repeatOpen ? this.repeat : 0,
        users: users
      };

      /** if repeat true then queryParams is repeat*/
      if (this.repeatOpen && this.repeat == 1) {
        queryParams.repeat_start = moment(this.startDate).format("YYYY/MM/DD");
        queryParams.repeat_end = moment(this.endDate).format("YYYY/MM/DD");
      }
      if (this.repeatOpen && this.repeat == 2) {
        queryParams.repeat_week = this.repeat_week.toString();
        queryParams.repeat_start = moment(this.startDate).format("YYYY/MM/DD");
        queryParams.repeat_end = moment(this.endDate).format("YYYY/MM/DD");
      }
      if (this.repeatOpen && this.repeat == 3) {
        queryParams.repeat_date = this.repeat_date;
        queryParams.repeat_start = moment(this.startDate).format("YYYY/MM/DD");
        queryParams.repeat_end = moment(this.endDate).format("YYYY/MM/DD");
      }
      store.commit("TOGGLE_LOADING", true);
      store
        .dispatch("safeties/addNew", queryParams)
        .then(response => {
          this.dialogs.addNew = false;
          this.alertMessage("success", "add_safety_success");
          // this.listSafeties();
          this.$emit('refetch-data')
          this.resetForm()
          this.$refs.notifyGroupComponent.clearData()
          this.close()
          axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
          })
        })
        .catch(error => {
          this.alertMessage("error", "add_safety_error");
        });
    },
    close() {
      this.dialogs.addNew = false;
      this.$refs.notifyGroupComponent.clearData()
      this.startDate = null;
      this.endDate = null;
      this.resetForm()
      this.currentTab = null
      this.department = deparmentActive
    },
    changeUser(users) {
      this.userSelected = users;
    },
    resetForm() {
      this.$refs.form.inputs.forEach(input => {
        if (!input.$el.getAttribute('class').includes('v-select')) {
          input.reset()
        }
      })
      this.$refs.form.resetValidation()
    },
  },

  setup() {
    const dateField = { text: trans("app.date") };
    const {
      paginate,
      loadingComponent,
      fetchAll,
      loading,
      textDate,
      getAllDepartments,
      listSafeties
    } = useStore();
    const itemSelection = ref([
      { title: "一般", value: 1 },
      { title: "管理者", value: 2 }
    ]);
    const perfectScrollbarSettings = {
      maxScrollbarLength: 70,
      wheelPropagation: false,
      wheelSpeed: 0.7
    };

    const defaultVal = ref([]);

    const dayInMonthDefault = () => {
      let day = [];
      for (let i = 1; i <= 31; i++) {
        day.push(i);
      }
      return day;
    }

    return {
      dayInMonthDefault,
      loadingComponent,
      getAllDepartments,
      listSafeties,
      defaultVal,
      dateField,
      fetchAll,
      itemPerPage,
      formatDate,
      paginate,
      loading,
      perfectScrollbarSettings,
      textDate,
      // validation rules
      validators: {
        itemSelection,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredField,
        checkStartDate,
        checkEndDate,
        checkDay,
        urlValidator
      },
      icons: {
        mdiCalendarRefreshOutline,
        mdiPhone,
        mdiHeartOutline,
        mdiAccountBoxOutline,
        mdiAccountCircle,
        mdiOfficeBuildingOutline,
        mdiAccountSupervisorOutline,
        mdiLockOutline,
        mdiPlus,
        mdiMapMarkerOutline,
        mdiPhoneInTalkOutline,
        mdiNotebookEditOutline,
        mdiFileDocumentEditOutline,
        mdiFileDocumentOutline,
        mdiCalendarSyncOutline
      }
    };
  }
};
</script>
<style scoped>

.justify-right {
  justify-content: right !important;
}
.custom-placeholder {
  padding: 1rem;
}
.v-item-group{
  min-height: 400px !important;
  overflow-y: auto !important;
}
.v-text-field {
  padding-top: 0 !important;
}
.required-field{
  color: #eb0000;
}
.v-treeview-node__level{
  width: revert !important;
}
.modal-safety{
  min-height:300px;
}
.input-title input {
  padding-bottom: 10px;
}
.radio-group{
  border-bottom: 1px solid #ccc;
}
.detail-textarea::v-deep textarea{
  height:280px !important;
  overflow: auto;
}

</style>
