<template>
  <div>
    <v-dialog v-model="dialogs.detailNotify" max-width="800" persistent scrollable>
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
                      v-model="inputVal.title"
                      hide-details="auto"
                      dense
                      :disabled="readOnly"
                      :rules="[
                        validators.requiredField(inputVal.title, labelTitle),
                        validators.maxLengthValidator(inputVal.title, 255, labelTitle),
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
                      v-model="inputVal.department_id"
                      hide-details="auto"
                      item-text="name"
                      item-value="id"
                      :disabled="readOnly"
                      :items="allBoard"
                      :rules="[
                        validators.requiredField(
                          inputVal.department_id,
                          departmentLabel
                        ),
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
                      ref="menuStart"
                      v-model="menuStart"
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
                          :disabled="readOnly"

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
                        @input="menuStart = false"
                        locale="ja"
                        no-title
                        scrollable
                        :disabled="readOnly"
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
                      ref="menuEnd"
                      v-model="menuEnd"
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
                          :disabled="readOnly"
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
                        @input="menuEnd = false"
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
                  <v-col cols="12" md="1">
                    <p>{{ this.$trans("app.color") }}</p>
                  </v-col>
                  <v-col cols="12" md="5">
                    <v-color-picker
                      v-model="inputVal.theme_color"
                      class="ma-2"
                      hide-canvas
                    ></v-color-picker>
                  </v-col>
                  <v-col cols="12" md="6">
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
                      <v-list-item-group
                        v-model="inputVal.icon"
                        class="item-group"
                        :disabled="readOnly"

                      >
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
                            :disabled="readOnly"

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
                            &nbsp;
                          </v-list-item>
                        </template>
                      </v-list-item-group>
                    </v-list>
                  </v-col>
                  <v-col cols="12" md="6" class="pt-6">
                    <v-file-input
                      v-model="imageSelected"
                      accept="image/png, image/jpeg, image/bmp"
                      @change="onFileChange"
                      :disabled="readOnly"
                      :label="this.$trans('app.file_select')"
                    ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="mx-8 pt-6" :transition="false">
                <v-row>
                  <v-col cols="12" md="12">
                    <v-textarea
                      v-model="inputVal.detail"
                      hide-details="auto"
                      :label="this.$trans('app.detail')"
                      :disabled="readOnly"
                      auto-grow
                      outlined
                      rows="10"
                      row-height="15"
                      :rules="[
                        validators.maxLengthValidator(inputVal.detail, 1023, this.$trans('app.detail')),
                      ]"
                      :error-messages="errorMessages.detail"
                    ></v-textarea>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="inputVal.detail_url"
                      hide-details="auto"
                      :label="this.$trans('app.detail_url')"
                      :disabled="readOnly"
                      :rules="[
                        validators.maxLengthValidator(
                          inputVal.detail_url,
                          1023,
                          this.$trans('app.detail_url')
                        ),
                        validators.urlValidator(inputVal.detail_url),
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
                      :disabled="readOnly"
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" class="">
                    <v-file-input
                      v-model="file"
                      hide-details="auto"
                      :label="this.$trans('app.file_select')"
                      @change="changeFile()"
                      :disabled="readOnly"
                      :rules="[validators.maxFileSize(file)]"
                      :error-messages="errorMessages.file"
                      dense
                    ></v-file-input>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="mx-8 pt-6" :transition="false">
                <v-row>
                  <v-col cols="6" md="6">
                    <v-text-field
                      v-model="inputVal.place"
                      hide-details="auto"
                      :label="this.$trans('app.place')"
                      :disabled="readOnly"
                      :rules="[
                        validators.maxLengthValidator(inputVal.place, 255, this.$trans('app.place')),
                      ]"
                      :error-messages="errorMessages.place"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="inputVal.place_url"
                      hide-details="auto"
                      :label="this.$trans('app.place_url')"
                      :disabled="readOnly"
                      :rules="[
                        validators.maxLengthValidator(inputVal.place_url, 1023, this.$trans('app.place_url')),
                        validators.urlValidator(inputVal.place_url),
                      ]"
                      :error-messages="errorMessages.place_url"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <tab-notify-group
                @users="changeUser"
                :userNotify="inputVal.users"
                :readonly="readOnly"
              ></tab-notify-group>
            </v-tabs-items>
          </v-card-text>
          <v-divider class="mt-2"></v-divider>
          <v-card-actions>
            <v-row class="py-3">
              <v-col class="pl-10">
                <v-switch
                  hide-details="auto"
                  v-model="resend"
                  :label="this.$trans('app.re_notify')"
                  :disabled="readOnly"
                ></v-switch>
              </v-col>
              <div class="justify-end">
                <v-btn color="default mx-2" @click="close">{{
                  this.$trans("app.close")
                }}</v-btn>
                <v-btn color="success mx-2" type="submit" :disabled="readOnly">{{
                  this.$trans("app.save")
                }}</v-btn>
              </div>
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
import { itemPerPage, formatDate, formatDateInput, textNoData } from "@/common";
import moment from "moment";
import TabNotifyGroup from "@/common/components/tabNotifyGroup/TabNotifyGroup.vue";
import "@mdi/font/css/materialdesignicons.css";
import AlertComponent from "@/common/components/snackBar.vue";
import { ref } from "@vue/composition-api";
import { computed } from "@vue/composition-api";
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
      default: { detail: false },
    },
    allBoard: { required: true },
    inputVal: { required: true },
    userInput: { required: true },
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
      department: "",
      errorMessages: {},
      menuStart: false,
      menuEnd: false,
      startDate: "",
      startDateFormated: "",
      endDateFormated: "",
      endDate: "",
      image: "",
      oldImage: null,
      color: "",
      imageSelected: null,
      icon: "",
      detail: "",
      detail_url: "",
      place: "",
      place_url: "",
      fileHeader: "",
      file: null,
      oldFile: null,
      userSelected: [],
      resend: 0,
    };
  },
  methods: {
    handleFormSubmit() {
      if (!this.$refs.form.validate()) {
        this.snackbar.isColorSnackbarVisible = true;
        this.snackbar.color = "error";
        this.snackbar.message = trans("message.input_error");
        return;
      }
      let arrUser = JSON.parse(JSON.stringify(this.userSelected));
      if (arrUser.length <= 0) {
        this.snackbar.isColorSnackbarVisible = true;
        this.snackbar.color = "error";
        this.snackbar.message =
          trans("message.user_required");
        return;
      }
      let arrId = [];
      arrUser.forEach((user) => {
        if (user.id) {
          arrId.push(user.id);
        }
      });
      let formData = new FormData();
      if (this.oldImage) {
        this.imageSelected = null;
      }
      if (this.oldFile) {
        this.file = null;
      }
      const queryParams = {
        id: this.inputVal.id,
        organization_id: JSON.parse(localStorage.getItem("Organization")).id,
        board_id: this.inputVal.department_id,
        title: this.inputVal.title,
        start: moment(this.startDate).format("YYYY-MM-DD hh:mm:ss"),
        end: moment(this.endDate).format("YYYY-MM-DD hh:mm:ss"),
        detail: this.inputVal.detail,
        detail_url: this.inputVal.detail_url,
        theme_color: this.inputVal.theme_color,
        icon: this.inputVal.icon,
        image: this.imageSelected,
        old_image: this.oldImage,
        place: this.inputVal.place,
        place_url: this.inputVal.place_url,
        user_id: arrId,
        file: this.file,
        old_file: this.oldFile,
        file_name: this.fileHeader,
        resend: this.resend ? 1 : 0,
      };
      for (const key in queryParams) {
        if (queryParams[key] || key == "resend") {
          if (key == "user_id") {
            for (let i = 0; i < arrId.length; i++) {
              formData.append(`user_id[${i}]`, arrId[i]);
            }
          } else {
            formData.append(key, queryParams[key]);
          }
        } else {
          if (
            key != "old_image" &&
            key != "image" &&
            key != "old_file" &&
            key != "file"
          ) {
            formData.append(key, "");
          }
        }
      }
      if (!store.state.loading) {
        store.commit("TOGGLE_LOADING", true);
        store
          .dispatch("notify/addNew", formData)
          .then((response) => {
            store.commit("TOGGLE_LOADING", false);
            this.dialogs.detailNotify = false;
            this.snackbar.isColorSnackbarVisible = true;
            this.snackbar.color = "success";
            this.snackbar.message = trans("message.update_success");
            this.$emit("refetch-data");
            this.imageSelected = null;
            this.image = "";
            this.file = null;
            this.fileHeader = "";
            this.currentTab = null;
            axios.get('/total-unanswered-notify').then(response => {
              const { count } = response.data
              localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
            })
          })
          .catch((error) => {
            store.commit("TOGGLE_LOADING", false);
            this.snackbar.isColorSnackbarVisible = true;
            this.snackbar.color = "error";
            this.snackbar.message = trans("message.error");
          });
        this.fileChange = false;
      }
    },
    close() {
      this.dialogs.detailNotify = false;
      this.imageSelected = null;
      this.image = "";
      this.file = null;
      this.fileHeader = "";
      this.currentTab = null;
    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image = "";
      }
      this.oldImage = null;
    },
    changeFile() {
      if (this.file) {
        this.fileHeader = this.file.name;
      } else {
        this.fileHeader = "";
      }
      this.oldFile = null;
    },
    changeUser(users) {
      this.userSelected = users;
    },
    blobToFile(theBlob, fileName) {
      theBlob.lastModifiedDate = new Date();
      theBlob.name = fileName;
      return theBlob;
    },
  },
  watch: {
    inputVal() {
      this.startDate = formatDateInput(this.inputVal.start);
      this.endDate = formatDateInput(this.inputVal.end);
      if (this.inputVal.image_url) {
        this.image = this.inputVal.image_url;
        let myBlob = new Blob();
        this.imageSelected = this.blobToFile(myBlob, this.inputVal.name_image);
        this.oldImage = this.inputVal.name_image;
      }
      if (this.inputVal.file) {
        this.fileHeader = this.inputVal.file.name;
        this.file = this.inputVal.file;
        this.oldFile = this.inputVal.file.title;
        this.userSelected = this.inputVal.users;
      }
    },
    userInput() {
      this.userSelected = this.userInput;
    },
    startDate() {
      this.startDateFormated = formatDate(this.startDate);
    },
    endDate() {
      this.endDateFormated = formatDate(this.endDate);
    },
  },
  setup() {
    const textDate = ref({
      startDate: trans("app.start_date"),
      endDate: trans("app.end_date"),
    });
    const readOnly = computed(() => {
      if(store.state.user.role){
        return store.state.user.role == 1 ? false : true
      }else if(store.state.user.is_super_admin){
        return false
      }
    })
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
    const model = [];
    return {
      items,
      model,
      textDate,
      itemPerPage,
      formatDate,
      readOnly,
      textNoData,
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
