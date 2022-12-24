<template>
  <div>
  <v-dialog v-model="dialogs.addNew" max-width="800" persistent scrollable>
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
                  <v-select hide-details="auto" @input="changeBoardSelect" v-model="boardId" :label="this.$trans('app.in_charge_branch_committee') + this.$trans('app.required_valid')"
                            item-text="name" item-value="id" :items="listBoard"
                            :rules="[validators.requiredSelect(boardId , this.$trans('app.in_charge_branch_committee'))]"
                            :no-data-text="textNoData"
                  >
                    <template v-slot:label>
                      {{ this.$trans('app.in_charge_branch_committee') }} <span style="color : red">*</span>
                    </template>
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
                  <v-menu ref="menu5" v-model="menu5" scrim="transparent" :close-on-content-click="false" :close-on-click="closeOnClick"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details="auto" style="padding-top: 0"  v-model="startDateFormated" :label="textDate.startDate" readonly v-bind="attrs" v-on="on"
                                    :rules="[validators.requiredField(startDate , textDate.startDate)]"
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
                                         :rules="[validators.requiredField(startDate , textDate.startDate)]"
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
                          <v-text-field  hide-details="auto" style="padding-top: 0" v-model="timeStart" label="開始時刻"  v-bind="attrs" v-on="on"
                                         :rules="[validators.requiredField(timeStart , '開始時刻')]"
                          >
                            <template v-slot:label>
                              開始時刻 <span style="color : red">*</span>
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
                                    :rules="[validators.requiredField(endDate, textDate.endDate) , validators.endDateAfter(startDate, endDate)]"
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
                      <v-menu ref="menu2" v-model="menu2" :close-on-content-click="false" :close-on-click="closeOnClick"
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
                      <v-menu ref="menu3" v-model="menu3" :close-on-content-click="false" :close-on-click="closeOnClick"
                              transition="scale-transition" width="270">
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field hide-details="auto" v-model="timeEnd" label="終了時刻"
                                        readonly v-bind="attrs" v-on="on"
                                        :rules="[validators.requiredField(timeEnd , '終了時刻')]"
                          >
                            <template v-slot:label>
                              終了時刻 <span style="color : red">*</span>
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
                  <v-menu ref="menu4" v-model="menu4" :close-on-content-click="false" :close-on-click="closeOnClick"
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
                  <v-text-field hide-details="auto" label="URL" v-model="placeUrl" :rules="[validators.urlValidator(placeUrl,1023)]"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
              <v-row>
                <v-col cols="12" md="12">
                  <v-textarea hide-details="auto" :label="this.$trans('app.detail_content')"
                              auto-grow outlined rows="10" row-height="15" v-model="detail" :rules="[validators.maxLengthValidator(detail, 1023, this.$trans('app.detail_content'))]"
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="12">
                  <v-text-field hide-details="auto" label="URL"
                                v-model="detailUrl" :rules="[validators.urlValidator(detailUrl,100)]"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-tab-item>
            <tab-notify-group ref="notifyGroupComponent" :stateSelectUser="true" :isEvent="true" :userEvent="userEvent" :changeDepartment="changeDepartment" :departmentOfUser="departmentOfUser" @users="changeUser"></tab-notify-group>
            <v-tab-item :eager="true" class="px-8 pt-6" :transition="false">
              <v-row>
                <v-col cols="6" md="6">
                  <v-file-input class="mt-2" v-model="files" :value="listFile"  small-chips multiple @change="changeFile($event)"
                                :label="this.$trans('app.file_select')" hide-details="auto" dense :error-messages="errorMessages.file"
                  ></v-file-input>
                </v-col>
                <!-- <v-col cols="6" md="6">
                  <v-btn color="primary">{{this.$trans('app.add')}}</v-btn>
                </v-col> -->
              </v-row>
              <v-row>
                <v-col cols="12" md="12" class="pa-5">
                  <v-data-table :headers="tableColumnFolderAdd" class="elevation-1" :items="listFile" :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }">
                    <template #[`item.actions`]="{item}">
                      <v-icon @click="removeFile(item)">{{icons.mdiTrashCan }}</v-icon>
                    </template>
                    <template #[`item.name`]="{item}">
                      {{truncateFileName(item.name, 35)}}
                    </template>

                    <template #[`item.size`]="{item}">
                      {{ bytesToSize(item.size) }}
                    </template>
                    <template v-slot:no-data>
                      {{ textNoData }}
                    </template>
                  </v-data-table>
                </v-col>
              </v-row>
            </v-tab-item>
          </v-tabs-items>
        </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="">
          <v-col cols="12" md="12" class="align-self-center justify-end">
            <v-row class="justify-end align-self-center py-3" style="align-self: center !important;">
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
import { mdiFileDocumentEditOutline , mdiTrashCan , mdiMapMarkerOutline ,mdiFileDocumentOutline ,mdiAccountSupervisorOutline ,mdiFolderOutline ,mdiCalendarRefreshOutline   } from '@mdi/js'
import {  itemPerPage , formatDate ,textNoData, bytesToSize,truncateFileName, maxSizeFile, deparmentActive } from '@/common'
import { requiredField, maxLengthValidator, urlValidator,
  lengthValidator, integerValidator, requiredSelect, between,
  maxListFileSize, dateRepeat, startDateBefore, endDateAfter } from '@core/utils/validation'
import store from '@/store'
import TabNotifyGroup from "@/common/components/tabNotifyGroup/TabNotifyGroup.vue";
import axios from '@axios'
import moment from 'moment'

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
  },
  data() {
    return {
      closeOnClick: false,
      errorMessages : {},
      menuRepeat1 : null,
      menuRepeat2 : null,
      repeatStartFormated : null,
      repeatEndFormated : null,
      labelTitle: this.$trans('app.title'),
      labelBoard : this.$trans('app.in_charge_branch_committee'),
      labelAllDay : this.$trans('app.all_day'),
      departmentOfUser : [],
      day : null,
      week : [],
      placeUrl : null,
      place : null,
      detail : null,
      detailUrl : null,
      files : null,
      title : null,
      boardId :  deparmentActive,
      themeColor : null,
      listFile : [],
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
      isGeneralMeeting : false,
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
      userEvent : [],
      sumSize : 0,
    }
  },
  watch: {
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
    changeDate(value , date) {
      return date = value
    },
    close() {
      this.dialogs.addNew = false
      this.currentTab = null
      this.departmentOfUser = []
      this.userEvent = []
      this.listFile = []
      this.startDate = null
      this.endDate = null
      this.startDate1 = null
      this.endDate1 = null
      this.dateResponse = null
      this.users = []
      this.boardIdArr = []
      this.resetForm()
      this.$refs.notifyGroupComponent.clearData()
      this.boardId = deparmentActive
    },
    resetForm() {
      this.$refs.form.inputs.forEach(input => {
        if (!input.$el.getAttribute('class').includes('v-select')) {
          input.reset()
        }
      })
      this.$refs.form.resetValidation()
    },
    changeFile(event){
      if(Array.isArray(event) && event.length > 0) {
        event.forEach(file => {
          if (file.size > 20971520) {
            store.commit('setSnackbar', {
              isColorSnackbarVisible: true,
              color: 'error',
              message: trans('app.msg_max_size')
            })
            return
          }else {
            event.forEach(item => {
              this.upsert(this.listFile, item)
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
    },
    handleFormSubmit(){
      if(!this.$refs.form.validate() ){
        store.commit('setSnackbar', {
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      if(this.users.length <= 0 || this.boardIdArr.length <= 0){
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
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        board_id : this.boardId,
        place : this.place,
        users : this.users,
        title : this.title,
        start : moment(start).format('YYYY-MM-DD HH:mm:ss'),
        end : moment(end).format('YYYY-MM-DD HH:mm:ss'),
        date_response : this.dateResponse,
        theme_color : this.themeColor ? this.themeColor.hexa : '',
        place_url : this.placeUrl,
        detail_url : this.detailUrl,
        detail : this.detail,
        is_allday : this.status ? 1 : 0,
        is_general_meeting : this.isGeneralMeeting ? 1 : 0,
        period :  this.dateResponse ? moment(this.dateResponse).format('YYYY-MM-DD') : null,
        files : this.listFile,
        department_id : this.boardId,
        departments : this.boardIdArr,
        repeat_week : this.status3 == '2' && this.week.length > 0 ? this.week : null,
        repeat_start : this.repeatStart ? moment(this.repeatStart).format('YYYY-MM-DD') : null,
        repeat_end : this.repeatEnd ? moment(this.repeatEnd).format('YYYY-MM-DD') : null,
        repeat_date : this.day,
        repeat : 0
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
            for(let i = 0; i < this.listFile.length; i++){
              this.sumSize += this.listFile[i].size
              if(this.sumSize > maxSizeFile){
                store.commit('setSnackbar', {
                  isColorSnackbarVisible : true,
                  color : 'error',
                  message : this.$trans('message.over_upload_file_size')
                })
              }else {
                formData.append(`files[${i}]`, this.listFile[i])
              }
            }
          }
          else {
            formData.append(key, queryParams[key])
          }
        }
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING', true)
        store.dispatch('calendar/addNewEvent', formData).then(response => {
          const { data } = response.data
          store.commit('TOGGLE_LOADING', false)
            this.$refs.form.reset()
            this.dialogs.addNew = false
            this.$emit('refresh')
            store.commit('setSnackbar', {
              isColorSnackbarVisible : true,
              color : 'success',
              message : trans('message.update_success')
            })
          axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
          })
          data.departments = this.boardIdArr
          this.$emit('addEvent', data)
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
      deparmentActive,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        requiredSelect,
        between,
        maxListFileSize,
        startDateBefore,
        endDateAfter,
        dateRepeat,
        urlValidator,
      },
      icons : {
        mdiFileDocumentEditOutline,
        mdiMapMarkerOutline,
        mdiFileDocumentOutline,
        mdiAccountSupervisorOutline,
        mdiFolderOutline ,mdiCalendarRefreshOutline,mdiTrashCan
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
  white-space: nowrap;
}
</style>
