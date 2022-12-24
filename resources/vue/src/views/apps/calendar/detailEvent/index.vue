<template>
  <div>
  <v-dialog v-model="dialogs.detailEvent" max-width="800" persistent scrollable>
    <v-form ref="form" @submit.prevent="handleFormSubmit">
    <v-card>
      <v-card-title>
        <span class="headline">{{ this.$trans('app.event_setting') }}</span>
      </v-card-title>
      <v-tabs v-model="currentTab" icons-and-text grow>
        <v-tab>
          {{this.$trans('app.basic_information')}}
          <v-icon>{{ icons.mdiFileDocumentEditOutline  }}</v-icon>
        </v-tab>
        <v-tab>
          {{this.$trans('app.place')}}
          <v-icon>
            {{ icons.mdiMapMarkerOutline }}
          </v-icon>
        </v-tab>
        <v-tab>
          {{this.$trans('app.detail_information')}}
          <v-icon>
            {{ icons.mdiFileDocumentOutline  }}
          </v-icon>
        </v-tab>
        <v-tab>
          {{this.$trans('app.notify_destination')}}
          <v-icon>
            {{ icons.mdiAccountSupervisorOutline  }}
          </v-icon>
        </v-tab>
        <v-tab>
          {{this.$trans('app.file')}}
          <v-icon>
            {{ icons.mdiFolderOutline  }}
          </v-icon>
        </v-tab>
      </v-tabs>
      <v-card-text>
        <v-tabs-items v-model="currentTab" >
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="title" :label="this.$trans('app.title')"
                              :rules="[validators.requiredField(title, this.$trans('app.title')), validators.maxLengthValidator(title, 255, this.$trans('app.title')) ]"
                >
                  <template v-slot:label>
                    {{ labelTitle }} <span style="color : red">*</span>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-select hide-details="auto" @input="changeBoardSelect" v-model="boardID" :label="this.$trans('app.in_charge_branch_committee') + this.$trans('app.required_valid')"
                          item-text="name" item-value="id" :items="listBoard"
                          :rules="[validators.requiredSelect(boardID, this.$trans('app.in_charge_branch_committee'))]"
                          ::no-data-text="textNoData"
                >
                  <template v-slot:label>
                    {{ labelBoard }} <span style="color : red">*</span>
                  </template>
                </v-select>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="6" md="6">
                <v-switch hide-details="auto" v-model="status" :label="this.$trans('app.all_day')"></v-switch>
              </v-col>
              <v-col cols="6" md="6">
                <v-switch hide-details="auto" v-model="isGeneralMeeting" :label="this.$trans('app.general_meeting')"></v-switch>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6" v-if="status">
                <v-menu ref="menu5" v-model="menu5" :close-on-content-click="false" :close-on-click="closeOnClick"
                        transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field hide-details="auto" style="padding-top: 0"  v-model="startDateFormated" :label="textDate.startDate" readonly v-bind="attrs" v-on="on"
                                  :rules="[validators.requiredField(startDate, textDate.startDate)]"
                    >
                      <template v-slot:label>
                        {{ textDate.startDate }} <span style="color : red">*</span>
                      </template>
                    </v-text-field>
                  </template>
                  <v-date-picker @input="menu5= false" locale="ja" no-title scrollable
                                 :day-format="startDate => new Date(startDate).getDate()" @change="$event => startDate = $event">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6" v-else>
                <v-row>
                  <v-col md="6" cols="12">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :close-on-click="closeOnClick"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field  hide-details="auto" style="padding-top: 0" v-model="startDateFormated"
                                       :label="textDate.startDate"  v-bind="attrs" v-on="on"
                                       :rules="[validators.requiredField(startDate, textDate.startDate)]"
                        >
                          <template v-slot:label>
                            {{ textDate.startDate }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker @input="menu = false" locale="ja" no-title scrollable
                                     :day-format="startDate => new Date(startDate).getDate()" @change="$event => startDate = $event">
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-menu ref="menu1" v-model="menu1" :close-on-content-click="false" :close-on-click="closeOnClick"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field  hide-details="auto" style="padding-top: 0" v-model="timeStart" :label="textDate.startTime"  v-bind="attrs" v-on="on"
                                       :rules="[validators.requiredField(timeStart, textDate.startTime)]"
                        >
                          <template v-slot:label>
                            {{ textDate.startTime }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-time-picker @change="$refs.menu1.save(timeStart)" v-model="timeStart" scrollable></v-time-picker>
                    </v-menu>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12" md="6" v-if="status">
                <v-menu ref="menu6" v-model="menu6" :close-on-content-click="false" :close-on-click="closeOnClick"
                        transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field hide-details="auto" style="padding-top: 0"  v-model="endDateFormated"
                                  :label="textDate.endDate" readonly v-bind="attrs" v-on="on"
                                  :rules="[validators.requiredField(endDate, textDate.endDate), validators.endDateAfter(startDate, endDate)]"
                    >
                      <template v-slot:label>
                        {{ textDate.endDate }} <span style="color : red">*</span>
                      </template>
                    </v-text-field>
                  </template>
                  <v-date-picker @input="menu6= false" locale="ja" no-title scrollable
                                 :day-format="endDate => new Date(endDate).getDate()" @change="$event => endDate = $event">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6" v-else>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-menu ref="menu2" v-model="menu2" :close-on-click="closeOnClick" :close-on-content-click="false"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field hide-details="auto" v-model="endDateFormated"
                                      :label="textDate.endDate" readonly v-bind="attrs" v-on="on"
                                      :rules="[validators.requiredField(endDate, textDate.endDate), validators.endDateAfter(startDate, endDate)]"
                        >
                          <template v-slot:label>
                            {{ textDate.endDate }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker @input="menu2 = false"  locale="ja" no-title scrollable
                                     :day-format="endDate => new Date(endDate).getDate()" @change="$event => endDate = $event">
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-menu ref="menu3" v-model="menu3" :close-on-click="closeOnClick" :close-on-content-click="false"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field hide-details="auto" v-model="timeEnd" :label="textDate.endTime"
                                      readonly v-bind="attrs" v-on="on"
                                      :rules="[validators.requiredField(timeEnd, textDate.endTime)]"
                        >
                          <template v-slot:label>
                            {{ textDate.endTime }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-time-picker v-model="timeEnd" @change="$refs.menu3.save(timeEnd)" scrollable></v-time-picker>
                    </v-menu>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
            <v-row class="mt-2">
              <v-col cols="12" md="6">
                <v-menu ref="menu4" v-model="menu4" :close-on-click="closeOnClick" :close-on-content-click="false"
                        transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field  hide-details="auto"  v-model="dateResponseFormated"
                                   :label="textDate.responseDeadline" readonly
                                   v-bind="attrs" v-on="on"
                                   :rules="[validators.requiredField(dateResponse, textDate.responseDeadline), validators.startDateBefore(dateResponse, endDate, textDate.responseDeadline, textDate.endDate)]"
                    >
                      <template v-slot:label>
                        {{ textDate.responseDeadline }} <span style="color : red">*</span>
                      </template>
                    </v-text-field>
                  </template>
                  <v-date-picker @input="menu4 = false"  locale="ja" no-title scrollable
                                 :day-format="dateResponse => new Date(dateResponse).getDate()" @change="$event => dateResponse = $event">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" class="pr-0" md="1">
                <span>カラー</span>
              </v-col>
              <v-col cols="12" md="5" class="mb-2">
                <v-color-picker width="100%" v-model="themeColor" hide-canvas style="max-width:100% !important"></v-color-picker>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row class="">
              <v-col cols="12" md="6">
                <v-text-field hide-details="auto" v-model="place" :label="this.$trans('app.place')" :rules="[validators.maxLengthValidator(place, 255, this.$trans('app.place'))]"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-text-field hide-details="auto" label="URL" v-model="placeUrl" :rules="[validators.urlValidator(placeUrl,100)]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="12">
                <v-textarea hide-details="auto" :label="this.$trans('app.detail_content')"
                            auto-grow outlined v-model="detail"  :rules="[validators.maxLengthValidator(detail, 1023, this.$trans('app.detail_content'))]"
                ></v-textarea>
              </v-col>
              <v-col cols="12" md="12">
                <v-text-field hide-details="auto" label="URL"
                              v-model="detailUrl" :rules="[validators.urlValidator(detailUrl, 1023)]"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <tab-notify-group ref="notifyGroupComponent" :stateSelectUser="true"  :changeDepartment="changeDepartment" :userNotify="users" :departmentOfUser="departmentOfUser" @users="changeUser"></tab-notify-group>
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row>
              <v-col cols="6" md="6">
                <v-file-input class="mt-2" v-model="files" :value="listFile"  small-chips multiple @change="changeFile($event)" :label="this.$trans('app.file_select')" hide-details="auto" dense></v-file-input>
              </v-col>
              <!-- <v-col cols="6" md="6">
                <v-btn color="primary">{{this.$trans('app.add')}}</v-btn>
              </v-col> -->
            </v-row>
            <v-row>
              <v-col cols="12" md="12" class="pa-5">
                <v-data-table :headers="tableColumnFolderAdd" class="elevation-1" :items="listFile" :footer-props="{itemsPerPageOptions: itemPerPage, 'items-per-page-text':this.$trans('app.itemPerPage') }">
                  <template #[`item.actions`]="{item}">
                    <v-icon @click="removeFile(item)">{{icons.mdiTrashCan }}</v-icon>
                  </template>
                  <template v-slot:no-data>
                    {{ textNoData }}
                  </template>
                  <template #[`item.size`]="{item}">
                    {{ item.size ? bytesToSize(item.size) : item.file_size + ' KB' }}
                  </template>
                  <template #[`item.name`]="{item}">
                    <a target="_blank" :href="item.download_url">{{ item.title ? truncateFileName(item.title,35) : truncateFileName(item.name,35) }}</a>
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="12" >
                <v-switch hide-details="auto" v-model="repeat" class="mt-1" :label="this.$trans('app.repeat')"></v-switch>
              </v-col>
            </v-row>
            <v-row v-if="repeat">
              <v-col cols="12" md="12">
                <v-radio-group v-model="status3" @change="changeRadio($event)"
                               :rules="[validators.requiredField(status3, '状態')]"
                >
                  <v-row>
                    <v-col cols="12" md="12" class="">
                      <v-radio :label="this.$trans('app.every_day')" value="1"></v-radio>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-row class="align-center">
                        <v-col cols="6" md="3">
                          <v-radio :label="this.$trans('app.every_week')" value="2"></v-radio>
                        </v-col>
                        <v-col cols="6" md="9" v-if="!disabled">
                          <v-row>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="2"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.mon')" value="2">
                              </v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled" :label="this.$trans('app.tue')"
                                          v-model="week"
                                          input-value="3"
                                          :rules="validateCheckbox"
                                          value="3"></v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="4"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.wed')" value="4"></v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="5"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.thu')" value="5"></v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="6"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.fri')" value="6"></v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="7"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.sat')" value="7"></v-checkbox>
                            </v-col>
                            <v-col>
                              <v-checkbox :disabled="disabled"
                                          v-model="week"
                                          input-value="1"
                                          :rules="validateCheckbox"
                                          :label="this.$trans('app.sun')" value="1"></v-checkbox>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="3">
                      <v-radio :label="this.$trans('app.every_month')" value="3"></v-radio>
                    </v-col>
                    <v-col cols="12" md="3" v-if="status3 == '3'">
                      <v-text-field hide-details="auto" :disabled="disabledInput"
                                    v-model="day"
                                    :rules="[validators.requiredField(day, this.$trans('app.day')), validators.between(day, 1, 31, this.$trans('app.day'))]"
                                    :label="this.$trans('app.day')" outlined dense>
                      </v-text-field>
                    </v-col>
                  </v-row>
                </v-radio-group>
                <v-divider></v-divider>
                <v-row class="my-2">
                  <v-col cols="4" md="2">
                    期間
                  </v-col>
                  <v-col cols="4" md="4">
                    <v-menu ref="menu4" v-model="menuRepeat2" :close-on-content-click="false" :close-on-click="closeOnClick"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field  hide-details="auto" v-model="repeatStartFormated"
                                       :label="this.$trans('app.start_date')" readonly
                                       v-bind="attrs" v-on="on" outlined dense
                                       :rules="[validators.requiredField(repeatStart, this.$trans('app.start_date')), validators.startDateBefore(repeatStart, repeatEnd), validators.dateRepeat(status3, repeatStart, repeatEnd)]"
                        >
                          <template v-slot:label>
                            {{ this.$trans('app.start_date') }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker @input="menuRepeat2 = false"  locale="ja" no-title scrollable
                                     :day-format="repeatStart => new Date(repeatStart).getDate()" @change="$event => repeatStart = $event">
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="4" md="4">
                    <v-menu ref="menu4" v-model="menuRepeat1" :close-on-content-click="false" :close-on-click="closeOnClick"
                            transition="scale-transition" width="270">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field  hide-details="auto"  v-model="repeatEndFormated"
                                       :label="this.$trans('app.end_date')" readonly
                                       v-bind="attrs" v-on="on" outlined dense
                                       :rules="[validators.requiredField(repeatEnd, this.$trans('app.end_date')), validators.endDateAfter(repeatStart, repeatEnd)]"
                        >
                          <template v-slot:label>
                            {{ this.$trans('app.end_date') }} <span style="color : red">*</span>
                          </template>
                        </v-text-field>
                      </template>
                      <v-date-picker @input="menuRepeat1 = false"  locale="ja" no-title scrollable
                                     :day-format="repeatEnd => new Date(repeatEnd).getDate()" @change="$event => repeatEnd = $event">
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="my-2">
          <v-col md="3" v-if="statusRepeat">
            <v-switch hide-details="auto" v-model="resend" label="再通知する"></v-switch>
          </v-col>
          <v-col cols="12" :md="statusRepeat ? '9' : '12'" class="align-self-center justify-end">
            <v-row class="justify-end align-self-center py-3" style="align-self: center !important;">
              <v-btn color="primary" class="mx-2" @click="printer">印刷</v-btn>
              <v-btn color="default" class="mx-2" @click="close()">{{this.$trans('app.close')}}</v-btn>
              <v-btn color="success" type="submit" class="mx-2">{{this.$trans('app.save')}}</v-btn>
            </v-row>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
    </v-form>
  </v-dialog>
  </div>
</template>

<script>
import CalendarAddNewLeft from './CalendarAddNewLeft.vue'
import useStore from "../useStore";
import { useResponsiveLeftSidebar } from '@core/comp-functions/ui'
import { mdiFileDocumentEditOutline, mdiTrashCan, mdiMapMarkerOutline,mdiFileDocumentOutline,mdiAccountSupervisorOutline,mdiFolderOutline,mdiCalendarRefreshOutline   } from '@mdi/js'
import {  itemPerPage, formatDate,textNoData,bytesToSize, truncateFileName, maxSizeFile } from '@/common'
import { requiredField, maxLengthValidator, lengthValidator,urlValidator, dateRepeat,
  integerValidator, requiredSelect,between, endDateAfter, startDateBefore } from '@core/utils/validation'
import store from '@/store'
import TabNotifyGroup from "@/common/components/tabNotifyGroup/TabNotifyGroup.vue";
import moment from 'moment';

export default {
  computed : {
    computedStartDateFormated () {
      return formatDate(this.startDate)
    },
    computedRepeatStartFormated () {
      return formatDate(this.repeatStart)
    },
    computedRepeatEndDateFormated () {
      return formatDate(this.repeatEnd)
    },
    computedStartDateFormated1 () {
      return formatDate(this.startDate1)
    },
    computedEndDateFormated () {
      return formatDate(this.endDate)
    },
    computedEndDateFormated1 () {
      return formatDate(this.endDate1)
    },
    computedResponseStartFormated () {
      return formatDate(this.responseStart)
    },
    computedResponseEndFormated () {
      return formatDate(this.responseEnd)
    },
    computedDateResponseFormated () {
      return formatDate(this.dateResponse)
    },
    validateCheckbox () {
      return [ () =>  (this.week.length > 0 && this.status3 === '2') || "曜日"]
    }
  },
  components : {
    CalendarAddNewLeft,
    TabNotifyGroup,
  },
  name: "index",
  props : {
    dialogs : {
      type : Object,
      require : true
    },
    listBoard: {
      type: Array,
      default: () => []
    },
    event : {
      type: Object,
      default: () => ({})
    },
    statusRepeat : {
      type: Boolean,
      default: () => true
    },
  },
  data() {
    return {
      sumSize : 0,
      closeOnClick : false,
      menuRepeat1 : null,
      menuRepeat2 : null,
      repeatStartFormated : null,
      repeatEndFormated : null,
      labelTitle: this.$trans('app.title'),
      labelBoard : this.$trans('app.in_charge_branch_committee'),
      labelAllDay : this.$trans('app.all_day'),
      departmentOfUser : [],
      day : '',
      week : [],
      placeUrl : null,
      place : null,
      detail : null,
      detailUrl : null,
      files : null,
      title : null,
      boardID : null,
      themeColor : null,
      statusChangeFile : false,
      listFile : [],
      resend : false,
      currentTab : null,
      startDate : null,
      startDateFormated: null,
      startDate2 : null,
      startDateFormated2: null,
      endDateFormated: null,
      endDate : null,
      startDate1 : null,
      startDateFormated1: null,
      endDateFormated1: null,
      endDate1 : null,
      isGeneralMeeting : 0,
      modal: false,
      menu : false,
      menu1: false,
      menu2: false,
      menu3 : false,
      menu4 : false,
      menu5: false,
      menu6: false,
      menu7: false,
      menu8 : false,
      status : 0,
      repeat : 0,
      status3 : '2',
      disabled : false,
      disabledInput : true,
      timeStart : null,
      timeEnd : null,
      repeat_week : null,
      repeat_day : null,
      repeatStart : null,
      repeatEnd : null,
      responseStart : null,
      responseStartFormated : null,
      responseEnd : null,
      responseEndFormated : null,
      dateResponseFormated : null,
      dateResponse : null,
      users : [],
      url : null,
      boardIdArr : [],
      oldFile : null,
      newFile : [],
    }
  },
  watch: {
    event(){
      this.title = this.event.title
      this.startDate = this.event.start ? moment(this.event.start).format('YYYY-MM-DD'): null
      this.endDate = this.event.end ? moment(this.event.end).format('YYYY-MM-DD'): null
      this.timeStart = this.event.start ? moment(this.event.start).format('HH:mm'): null
      this.timeEnd = this.event.end ? moment(this.event.end).format('HH:mm'): null
      this.placeUrl = this.event.place_url
      this.place = this.event.place
      this.detail = this.event.detail
      this.detailUrl = this.event.detail_url
      this.boardID = this.event.department_id
      this.status = this.event.is_allday
      this.isGeneralMeeting = this.event.is_general_meeting
      this.departmentOfUser = this.event.departments && this.event.departments.length > 0 ? this.event.departments.map(item => item.id) : []
      this.dateResponse = this.event.period ?  moment(this.event.period).format('YYYY-MM-DD'): null
      this.users = this.event.users;
      this.boardIdArr =  this.event.departments &&  this.event.departments.length > 0 ? this.event.departments.map(item => item.id) : []
      this.oldFile = this.event.files
      this.listFile = this.event.files
      this.day = this.event.repeat_date.toString()
      this.week = this.event.repeat_week ? this.event.repeat_week.split(',') : []
      this.repeatStart = this.event.repeat_start ? moment(this.event.repeat_start).format('YYYY-MM-DD') : null
      this.repeatEnd =  this.event.repeat_end ?  moment(this.event.repeat_end).format('YYYY-MM-DD'): null
      this.repeat = this.event.repeat > 0  ? true : false,
      this.themeColor = this.event.theme_color
      this.status3 = this.event.repeat.toString()
      this.disabled = this.event.repeat.toString() !== '2' ? true : false
      this.disabledInput = this.event.repeat.toString() !== '3' ? true : false
      this.repeat = this.event.repeat > 0  ? true : false
    },
    repeatStart(val) {
      this.repeatStartFormated = this.formatDate(this.repeatStart)
    },
    repeatEnd(val) {
      this.repeatEndFormated = this.formatDate(this.repeatEnd)
    },
    startDate (val) {
      this.startDateFormated = this.formatDate(this.startDate)
    },
    startDate1 (val) {
      this.startDateFormated1 = this.formatDate(this.startDate1)
    },
    endDate (val) {
      this.endDateFormated = this.formatDate(this.endDate)
    },
    endDate1 (val) {
      this.endDateFormated1 = this.formatDate(this.endDate1)
    },
    responseStart (val) {
      this.responseStartFormated = this.formatDate(this.responseStart)
    },
    responseEnd (val) {
      this.responseEndFormated = this.formatDate(this.responseEnd)
    },
    dateResponse (val) {
      this.dateResponseFormated = this.formatDate(this.dateResponse)
    },
  },
  methods : {
    close(){
      this.dialogs.detailEvent = false
    },
    printer(){
      this.$emit('printer', this.event)
    },
    resetForm() {
      this.$refs.form.inputs.forEach(input => {
        if (!input.$el.getAttribute('class').includes('v-select')) {
          input.reset()
        }
      })
      // this.$refs.form.resetErrorBag()
    },
    changeFile(event){
      if(Array.isArray(event) && event.length > 0){
        event.forEach(file => {
          if(file.size > 20971520) {
            store.commit('setSnackbar', {
              isColorSnackbarVisible: true,
              color: 'error',
              message: trans('app.msg_max_size')
            })
            return
          }else {
            this.files = null
            this.statusChangeFile = true
            event.forEach(item => this.upsert(this.listFile, item))
            this.listFile.map(item => {
              if(item.constructor.name == 'File'){
                this.upsert(this.newFile, item)
              }
              else {
                this.listFile.filter(item => {
                  return item.constructor.name !== 'File'
                })
              }
            })
          }
        })
      }

    },
    changeUser(users) {
      this.users = users
    },
    changeDepartment(event) {
      this.boardIdArr = event
    },
    changeRadio(e){
      if(e === '1' || e == false){
        this.disabled = true
      }
      else if(e == '3'){
        this.disabled = true
        this.disabledInput = false
      }
      else if(e == '2'){
        this.disabled = false
        this.disabledInput = true
      }
    },
    changeBoardSelect(value){
      this.boardID = value
    },
    upsert(array, element){
      const i = array.findIndex(_element => _element.name === element.name);
      if (i > -1) array[i] = element; // (2)
      else array.push(element);
    },
    removeFile(item) {
      this.listFile = this.listFile.filter(function (value) {
        return value.name !== item.name
      })
      this.newFile = this.newFile.filter(function (value) {
        return value.name !== item.name
      })
      this.oldFile = this.oldFile.filter(function (value) {
        return value.id !== item.id
      })
    },
    handleFormSubmit(){
      this.oldFile = this.oldFile.filter(item => {
          return item.constructor.name !== 'File'
      })
      if(!this.$refs.form.validate() || this.boardIdArr.length <= 0){
        store.commit('setSnackbar', {
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      if(this.users.length <= 0){
        store.commit('setSnackbar', {
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.user_required')
        })
        return
      }
      let formData = new FormData();
      const start = this.timeStart !== null && !this.status ? this.startDate + ' ' + this.timeStart : this.startDate
      const end = this.timeEnd !== null && !this.status ? this.endDate + ' ' + this.timeEnd : this.endDate + ' ' + '23:59:59'
      const queryParams = {
        id : !this.statusRepeat ? null : this.event.id,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        board_id : this.boardID,
        place : this.place,
        department_id : this.boardID,
        departments : this.boardIdArr,
        users : this.users,
        title : this.title,
        start : moment(start).format('YYYY-MM-DD HH:mm:ss'),
        end : moment(end).format('YYYY-MM-DD HH:mm:ss'),
        period : moment(this.dateResponse).format('YYYY-MM-DD'),
        theme_color : this.themeColor.hasOwnProperty('hexa') ? this.themeColor.hexa : this.themeColor,
        place_url : this.placeUrl,
        detail_url : this.detailUrl,
        detail : this.detail,
        is_allday : this.status ? '1' : '0',
        is_general_meeting : this.isGeneralMeeting ? '1' : '0',
        old_files : this.oldFile.length > 0 ? this.oldFile : null,
        files : this.newFile.length > 0 ? this.newFile : null,
        resend : this.resend,
        repeat : this.repeat && this.status3 ? this.status3 : '0',
        repeat_week : this.repeat|| this.status3 == '2' && this.week.length > 0 ? this.week : null,
        repeat_start : this.repeat && this.repeatStart ? moment(this.repeatStart).format('YYYY-MM-DD') : null,
        repeat_end : this.repeat && this.repeatEnd ? moment(this.repeatEnd).format('YYYY-MM-DD') : null,
        repeat_date : this.repeat && this.day ? parseInt(this.day) : null,
      }
      for (const key in queryParams) {
        if(queryParams[key]){
          if(key == 'users'){
            for(let i = 0; i < this.users.map(item => (item.id)).length; i++){
              formData.append(`users[${i}]`, this.users.map(item => (item.id))[i])
            }
          }else if(key == 'departments'){
            for(let i = 0; i < this.boardIdArr.length; i++){
              formData.append(`departments[${i}]`, this.boardIdArr[i])
            }
          }
          else if(key == 'files'){
            for(let i = 0; i < this.newFile.length; i++){
              this.sumSize += this.newFile[i].size
              if(this.sumSize > maxSizeFile){
                store.commit('setSnackbar', {
                  isColorSnackbarVisible : true,
                  color : 'error',
                  message : '合計アップロードサイズは20MBを超えてはなりません'
                })
              }else {
                formData.append(`files[${i}]`, this.newFile[i])
              }
            }
          }else if(key == 'old_files'){
            for(let i = 0; i < this.oldFile.length; i++){
              formData.append(`old_file[${i}]`, this.oldFile[i].id)
            }
          }
          else {
            formData.append(key, queryParams[key])
          }
        }
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING', true)
        store.dispatch('calendar/addNewEvent', formData).then(res => {
          const { data } = res.data
          store.commit('TOGGLE_LOADING', false)
            this.dialogs.addNew = false
            this.$emit('refresh')
            store.commit('updateEvent', data)
            store.commit('setSnackbar', {
              isColorSnackbarVisible : true,
              color : 'success',
              message : trans('message.update_success')
            })
            if(!this.statusRepeat){
              data.departments = this.boardIdArr
              data.type = 'event'
              this.$emit('addEvent', data)
            }else{
              data.type = 'event'
              this.$emit('updateEvent', data)
            }
          this.close()
        })

      }
    },
  },
  setup() {
    const { tableColumn,tableColumnFolderAdd, textDate } = useStore()
    const { isLeftSidebarOpen, contentStyles } = useResponsiveLeftSidebar({ sidebarWidth: 250 })
    return {
      contentStyles,isLeftSidebarOpen,
      tableColumn,tableColumnFolderAdd,textDate,itemPerPage,
      formatDate,textNoData,bytesToSize,truncateFileName,
      maxSizeFile,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect,
        between,
        endDateAfter,
        startDateBefore,
        dateRepeat,
        urlValidator,
      },
      icons : {
        mdiFileDocumentEditOutline,
        mdiMapMarkerOutline,
        mdiFileDocumentOutline,
        mdiAccountSupervisorOutline,
        mdiFolderOutline,mdiCalendarRefreshOutline,mdiTrashCan
      }
    }
  }
}
</script>

<style scoped>
.custom-placeholder {
  padding : 1rem;
}
.v-item-group{
  min-height: 350px;
}
.v-text-field{
  padding-top: 0px;
  margin-top: 0px;
}

.v-text-field{
  padding-top: 0 !important;
}
.v-text-field__details{
  overflow: revert !important;
  white-space: nowrap !important;
}
</style>
