<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
        <v-card>
          <v-card-title class="headline">{{
            this.$trans("app.notify_setting")
          }}</v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow>
            <v-tab>
              {{ this.$trans("app.basic_information") }}
              <v-icon>{{ icons.mdiFileDocumentEditOutline }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans("app.detail") }}
              <v-icon>{{ icons.mdiFileDocumentOutline }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans("app.place") }}
              <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans("app.notify_destination") }}
              <v-icon>{{ icons.mdiAccountSupervisorOutline }}</v-icon>
            </v-tab>
          </v-tabs>
          <v-card-text>
            <v-tabs-items v-model="currentTab">
              <v-tab-item class="mx-8 pt-6" :transition="false">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="title"
                      hide-details="auto"
                      dense
                      :rules="[
                        validators.requiredField(title, labelTitle),
                        validators.maxLengthValidator(title, 255, labelTitle),
                      ]"
                      :error-messages="errorMessages.title"
                    >
                      <template v-slot:label>
                        {{ labelTitle }} <b class="required-field">*</b>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="department"
                      hide-details="auto"
                      item-text="name"
                      item-value="id"
                      :items="allBoard"
                      :rules="[
                        validators.requiredField(department, departmentLabel),
                      ]"
                      :no-data-text="textNoData"
                    >
                      <template v-slot:label>
                        {{ departmentLabel }} <b class="required-field">*</b>
                      </template>
                    </v-select>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
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
                          :rules="[
                            validators.requiredField(
                              startDateFormated,
                              textDate.startDate
                            ),
                          ]"
                        >
                          <template v-slot:label>
                            {{ textDate.startDate }}
                            <b class="required-field">*</b>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker
                        @input="menu = false"
                        locale="ja"
                        no-title
                        scrollable
                        :day-format="
                          (startDate) => new Date(startDate).getDate()
                        "
                        @change="($event) => (startDate = $event)"
                      >
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="12" md="6">
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
                          :rules="[
                            validators.requiredField(
                              endDateFormated,
                              textDate.endDate
                            ),
                            validators.endDateAfter(startDate, endDate),
                          ]"
                        >
                          <template v-slot:label>
                            {{ textDate.endDate }}
                            <b class="required-field">*</b>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker
                        @input="menu1 = false"
                        locale="ja"
                        no-title
                        scrollable
                        :day-format="(endDate) => new Date(endDate).getDate()"
                        @change="($event) => (endDate = $event)"
                      >
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="1" class="pr-0">
                    <p>{{ this.$trans("app.color") }}</p>
                  </v-col>
                  <v-col cols="12" md="5">
                    <v-color-picker
                      v-model="color"
                      class="ma-2"
                      hide-canvas
                    ></v-color-picker>
                  </v-col>
                  <v-col cols="12" md="6" class="pt-8">
                    <v-img
                      class="preview-image"
                      :lazy-src="image"
                      :src="image"
                    ></v-img>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-list
                      shaped
                      class="rounded"
                      style="border: 1px solid #ccc"
                    >
                      <v-list-item-group v-model="icon" class="item-group">
                        <template v-for="(item, i) in items">
                          <v-divider
                            v-if="!item"
                            :key="`divider-${i}`"
                          ></v-divider>
                          <v-list-item
                            class="un_border"
                            v-else
                            :key="`item-${i}`"
                            :value="item"
                            active-class="deep-purple--text text--accent-4"
                            style="padding: 0 0px !important"
                          >
                            <template v-slot:default="">
                              <v-list-item-content>
                                <v-list-item-title>
                                  <v-icon v-if="item">{{ item }}</v-icon>
                                </v-list-item-title>
                              </v-list-item-content>
                            </template>
                          </v-list-item>
                          <v-list-item
                            v-if="i == 7"
                            :key="''"
                            style="
                              padding: 0 0 !important;
                              color: #6200ea !important;
                            "
                          >
                            {{ "" }}
                          </v-list-item>
                        </template>
                      </v-list-item-group>
                    </v-list>
                  </v-col>
                  <v-col cols="12" md="6" class="pt-6">
                    <v-file-input
                      v-model="imgSelected"
                      accept="image/png, image/jpeg, image/bmp"
                      @change="onFileChange"
                      :label="this.$trans('app.file_select')"
                    ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="mx-8 pt-6" :transition="false">
                <v-row>
                  <v-col cols="12" md="12">
                    <v-textarea
                      v-model="detail"
                      hide-details="auto"
                      :label="this.$trans('app.detail')"
                      auto-grow
                      outlined
                      rows="10"
                      row-height="15"
                      :rules="[validators.maxLengthValidator(detail, 1023, this.$trans('app.detail'))]"
                      :error-messages="errorMessages.detail"
                    ></v-textarea>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="detail_url"
                      hide-details="auto"
                      :label="this.$trans('app.detail_url')"
                      :rules="[
                        validators.maxLengthValidator(detail_url, 1023, this.$trans('app.detail_url')),
                        validators.urlValidator(detail_url),
                      ]"
                      :error-messages="errorMessages.detail_url"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6" class="">
                    <v-text-field
                      v-model="fileHeader"
                      hide-details="auto"
                      :label="this.$trans('app.file_header')"
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="">
                    <v-file-input
                      v-model="file"
                      dense
                      hide-details="auto"
                      :label="this.$trans('app.file_select')"
                      @change="changeFile()"
                      :rules="[validators.maxFileSize(file)]"
                      :error-messages="errorMessages.file"
                    ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="mx-8 pt-6" :transition="false">
                <v-row>
                  <v-col cols="6" md="6">
                    <v-text-field
                      v-model="place"
                      hide-details="auto"
                      :label="this.$trans('app.place')"
                      :rules="[validators.maxLengthValidator(place, 255, this.$trans('app.place'))]"
                      :error-messages="errorMessages.place"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="place_url"
                      hide-details="auto"
                      :label="this.$trans('app.place_url')"
                      :rules="[
                        validators.maxLengthValidator(place_url, 1023, this.$trans('app.place_url')),
                        validators.urlValidator(place_url),
                      ]"
                      :error-messages="errorMessages.place_url"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <tab-notify-group
                ref="notifyGroupComponent"
                @users="changeUser"
                :userNotify="defaultVal"
              ></tab-notify-group>
            </v-tabs-items>
          </v-card-text>
          <v-divider class="mt-2"></v-divider>
          <v-card-actions>
            <v-row class="justify-end py-3">
              <v-btn color="default mx-2" @click="close">{{
                this.$trans("app.close")
              }}</v-btn>
              <v-btn color="success mx-2" type="submit">{{
                this.$trans("app.save")
              }}</v-btn>
            </v-row>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>
  </div>
</template>

<script>
import {
  requiredField,
  maxLengthValidator,
  lengthValidator,
  integerValidator,
  endDateAfter,
  maxFileSize,
  urlValidator,
} from "@core/utils/validation";
import {
  mdiFileDocumentEditOutline,
  mdiFileDocumentOutline,
  mdiMapMarkerOutline,
  mdiAccountSupervisorOutline,
} from "@mdi/js";
import store from "@/store";
import { ref } from "@vue/composition-api";
import { itemPerPage, formatDate, deparmentActive } from "@/common";
import moment from "moment";
import TabNotifyGroup from "@/common/components/tabNotifyGroup/TabNotifyGroup.vue";
import "@mdi/font/css/materialdesignicons.css";
import AlertComponent from "@/common/components/snackBar.vue";
import axios from "@axios";
export default {
  computed: {
    computedWidth: function () {
      return this.width;
    },
  },
  components: {
    TabNotifyGroup,
    AlertComponent,
  },
  props: {
    dialogs: {
      type: Object,
      require: true,
    },
    allBoard: { required: true },
  },

  name: "index",
  data() {
    return {
      snackbar: {
        isColorSnackbarVisible: false,
        message: "",
        color: "error",
      },
      labelTitle: this.$trans("app.title"),
      departmentLabel: this.$trans("app.in_charge_branch_committee"),
      currentTab: null,
      title: "",
      department: deparmentActive,
      errorMessages: {},
      menu: false,
      menu1: false,
      startDate: "",
      startDateFormated: "",
      endDateFormated: "",
      endDate: "",
      image: "",
      color: "",
      imgSelected: null,
      icon: "",
      detail: "",
      detail_url: "",
      place: "",
      place_url: "",
      fileHeader: "",
      file: null,
      userSelected: [],
    };
  },
  methods: {
    handleFormSubmit() {
      if (!this.$refs.form.validate()) {
        store.commit("setSnackbar", {
          isColorSnackbarVisible: true,
          color: "error",
          message: trans("message.input_error"),
        });
        return;
      }
      let arrUser = JSON.parse(JSON.stringify(this.userSelected));
      if (arrUser.length <= 0) {
        this.snackbar.isColorSnackbarVisible = true;
        this.snackbar.color = "error";
        this.snackbar.message =
           trans('message.user_required')
        return;
      }
      let arrId = [];
      arrUser.forEach((user) => {
        if (user.id) {
          arrId.push(user.id);
        }
      });
      let formData = new FormData();
      const queryParams = {
        organization_id: JSON.parse(localStorage.getItem("Organization")).id,
        board_id: this.department,
        title: this.title,
        start: moment(this.startDate).format("YYYY-MM-DD hh:mm:ss"),
        end: moment(this.endDate).format("YYYY-MM-DD hh:mm:ss"),
        detail: this.detail,
        detail_url: this.detail_url,
        theme_color: this.color,
        icon: this.icon,
        image: this.imgSelected,
        place: this.place,
        place_url: this.place_url,
        user_id: arrId,
        file: this.file,
        file_name: this.fileHeader,
      };
      for (const key in queryParams) {
        if (queryParams[key]) {
          if (key == "user_id") {
            for (let i = 0; i < arrId.length; i++) {
              formData.append(`user_id[${i}]`, arrId[i]);
            }
          } else {
            formData.append(key, queryParams[key]);
          }
        }
      }
      if (!store.state.loading) {
        store.commit("TOGGLE_LOADING", true);
        store.dispatch("notify/addNew", formData).then((response) => {
          store.commit("TOGGLE_LOADING", false);
          this.dialogs.addNew = false;
          store.commit("setSnackbar", {
            isColorSnackbarVisible: true,
            color: "success",
            message: trans("message.add_success"),
          });
          this.resetForm();
          this.$refs.notifyGroupComponent.clearData();
          this.currentTab = null;
          this.$emit("refetch-data");
          axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
          })
        });
      }
    },
    close() {
      this.startDate = null;
      this.endDate = null;
      this.dialogs.addNew = false;
      this.$refs.notifyGroupComponent.clearData();
      this.resetForm();
      this.imgSelected = null;
      this.currentTab = null;
      this.department = deparmentActive;
    },
    resetForm() {
      this.$refs.form.inputs.forEach((input) => {
        if (!input.$el.getAttribute("class").includes("v-select")) {
          input.reset();
        }
      });
      this.$refs.form.resetValidation()

    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image = "";
      }
    },
    changeFile() {
      if (this.file) {
        this.fileHeader = this.file.name;
      } else {
        this.fileHeader = "";
      }
    },
    changeUser(users) {
      this.userSelected = users;
    },
  },
  watch: {
    startDate(val) {
      this.startDateFormated = this.formatDate(this.startDate);
    },
    endDate(val) {
      this.endDateFormated = this.formatDate(this.endDate);
    },
  },
  setup() {
    const textDate = ref({
      startDate: trans("app.start_date"),
      endDate: trans("app.end_date"),
    });
    const items = [
      "mdi-airplane",
      "mdi-car-hatchback",
      "mdi-liquor",
      "mdi-alert-circle",
      "mdi-pencil",
      "mdi-emoticon-excited-outline",
      "mdi-emoticon-dead-outline",
      "",
    ];
    const defaultVal = ref([]);
    return {
      items,
      defaultVal,
      textDate,
      itemPerPage,
      formatDate,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        endDateAfter,
        maxFileSize,
        urlValidator,
      },
      icons: {
        mdiFileDocumentEditOutline,
        mdiFileDocumentOutline,
        mdiMapMarkerOutline,
        mdiAccountSupervisorOutline,
      },
    };
  },
};
</script>

<style scoped>
.item-group {
  display: flex;
  min-height: 20px !important;
}
.v-item-group{
  min-height: 450px;
}
.v-application--is-ltr .v-list.v-sheet--shaped .v-list-item,
.v-application--is-ltr .v-list.v-sheet--shaped .v-list-item::before,
.v-application--is-ltr
  .v-list.v-sheet--shaped
  .v-list-item
  > .v-ripple__container {
  border-bottom-right-radius: 0 !important;
  border-top-right-radius: 0 !important;
  border-right: 1px solid #ccc;
}
.v-list {
  padding: 0 !important;
}
.un_border:last-child {
  border-right: none !important;
}
.un_border {
  min-height: 20px;
  padding: 0 5px;
  text-align: center;
}
.custom-placeholder {
  padding: 1rem;
}
.v-text-field {
  padding-top: 0 !important;
}
.required-field {
  color: red !important;
}
.preview-image {
  height: 150px;
  border: 1px solid rgb(168, 166, 166);
}
</style>
