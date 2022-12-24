<template>
  <div>
    <v-card class="max-h-content-container app-calendar position-relative overflow-hidden text-sm">
      <v-navigation-drawer v-model="isLeftSidebarOpen" width="250" mobile-breakpoint="sm" absolute touchless :right="$vuetify.rtl"
                           :temporary="$vuetify.breakpoint.xsOnly" style="z-index: 2">
        <div class="h-full">
          <calendar-left-sidebar-content :role="role" :changeDepartment="changeDepartment" :boardIdArr="boardIdArr" :listBoard="listBoard"  @add-event="dialogs.addNew = true"></calendar-left-sidebar-content>
        </div>
      </v-navigation-drawer>
      <div :style="contentStyles" class="h-full">
        <div class="calendar-header mx-6 mb-5 d-flex align-center flex-wrap">
          <v-btn icon class="d-inline-block d-md-none me-1" @click="isLeftSidebarOpen = true">
            <v-icon size="30">
              {{ icons.mdiMenu }}
            </v-icon>
          </v-btn>
          <v-btn icon class="me-1" @click="prev()">
            <v-icon size="30">
              {{ icons.mdiChevronLeft }}
            </v-icon>
          </v-btn>
          <v-btn icon class="me-3" @click="next()">
            <v-icon size="30">
              {{ icons.mdiChevronRight }}
            </v-icon>
          </v-btn>
          <p class="font-weight-semibold text-xl text--primary mb-0 me-6">
            {{ calendarHeader }}
          </p>
          <v-spacer></v-spacer>
          <v-btn-toggle @change="changeView" color="primary" clas="mt-3 mt-sm-0">
            <v-btn v-for="calendarViewOption in calendarViewOptions" :key="calendarViewOption.value" elevation="0" :value="calendarViewOption.value">
              {{ calendarViewOption.text }}
            </v-btn>
            <v-btn value="list" elevation="0" @click="showEventList">
              一覧
            </v-btn>
          </v-btn-toggle>
        </div>
        <v-sheet height="565">
          <v-calendar v-if="!isList" :category-show-all="true" event-overlap-mode="column" :event-overlap-threshold="30" :show-month-on-first="false" locale="ja-jp" ref="refCalendar" class="h-full my-0" v-model="calendarValue" :type="activeCalendarView" :events="eventStore"
                      :event-margin-bottom="5" @click:event="clickEvent" event-text-color="#fff" @click:time="calendarHandleDayClick"  :event-overlap-mode="mode"
                      @click:more="calendarHandleMoreClick" :event-color="getEventColor" :weekdays="weekday" @change="changeCalendar">
            <template #day="{part , day ,date}">
              <div v-if="holidays.includes(date)" class="holiday holiday__wrapper"></div>
            </template>
            <template v-slot:day-label-header="{past , day , date ,weekday}">
              <div  v-if="holidays.includes(date)" class="holiday holiday__wrapper">
                <div class="d-flex mt-4">{{day}}</div>
              </div>
              <div v-ripple  v-else-if="weekday == 6" class="friday">
                <div class="d-flex justify-center " style="color : blue">{{day}}</div>
              </div>
              <div v-ripple  v-else-if="weekday == 0">
                <div class="d-flex justify-center " style="color : red">{{day}}</div>
              </div>
              <div  v-else class="holiday">
                <div class="d-flex justify-center ">{{day}}</div>
              </div>
            </template>
            <template v-slot:day-month="{past , day}">
              {{past}}
            </template>
            <template v-slot:day-label="{day , date}">
              <div class="ml-2" style="color : red" v-if="holidays.includes(date)">{{ day }} {{ itemHoliday.find(item => item.date == date).name }}</div>
              <div class="ml-2" v-else>{{ day }}</div>
            </template>
            <template v-slot:day-body="{day}">
              <div class="ml-2">{{ day }} {{ textDate }}</div>
            </template>
            <template v-slot:interval="{day , hour , date}">
              <div v-if="holidays.includes(date)" class="holiday holiday__wrapper" style="position: inherit"></div>
            </template>
            <template #event="{ event , day}">
              <div style="width: 100%; height: 100%" @contextmenu.prevent="mouseRight(event)"  v-contextmenu:contextmenu v-if="event.type == 'event'">
                <v-icon v-if="event.icon !== '7'" color="#fff" size="15">{{ event.icon }}</v-icon>
                <span>{{ event.title }}</span>
              </div>
              <div style="width: 100%; height: 100%" v-else>
                <v-icon v-if="event.icon !== '7'"  color="#fff" size="15">{{ event.icon }}</v-icon>
                <span>{{ event.title }}</span>
              </div>
            </template>
          </v-calendar>
          <list-event v-if="isList"
            :start="start" :end="end"
                      :admin="role"
            @can_answer="actionMouseRight('can_answer', $event)"
            @download="actionMouseRight('download', $event)"
            @result="actionMouseRight('result', $event)"
            @detail="actionMouseRight('detail', $event)"
            @delete="actionMouseRight('delete', $event)"
            @duplicate="actionMouseRight('duplicate', $event)"
            @printer="actionMouseRight('printer', $event)"
          >

          </list-event>
        </v-sheet>

      </div>
      <v-contextmenu ref="contextmenu" :style="styleContextMenu">
        <v-contextmenu-item v-if="disabled" @click="actionMouseRight('can_answer')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiCellphoneMessage }}</v-icon>
          {{ this.$trans('app.event_answer_reference') }}
        </v-contextmenu-item>
        <v-contextmenu-item @click="actionMouseRight('download')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiCalendarPlus  }}</v-icon>
          {{ this.$trans('app.register_in_my_calendar') }}
        </v-contextmenu-item>
        <v-contextmenu-item v-if="role" @click="actionMouseRight('result')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiSignal }}</v-icon>
          {{ this.$trans('app.result_response') }}
        </v-contextmenu-item>
        <v-contextmenu-item v-if="role" @click="actionMouseRight('detail')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiFileDocumentOutline }}</v-icon>
          {{ this.$trans('app.detail') }}
        </v-contextmenu-item>
        <v-contextmenu-item v-if="role"  @click="actionMouseRight('duplicate')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiContentDuplicate  }}</v-icon>
          {{ this.$trans('app.duplicate') }}
        </v-contextmenu-item>
         <v-contextmenu-item v-if="role" @click="actionMouseRight('printer')" :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiPrinter }}</v-icon>
            印刷
        </v-contextmenu-item>
        <v-contextmenu-item v-if="role" @click="actionMouseRight('delete')"  :style="styleContextMenuItem">
          <v-icon>{{ icons.mdiDeleteOutline }}</v-icon>
          {{ this.$trans('app.delete') }}
        </v-contextmenu-item>
      </v-contextmenu>
    </v-card>
    <add-component @addEvent="addEvent" :dialogs="dialogs" :listBoard="listDepart"></add-component>
    <detail-event @addEvent="addEvent" @updateEvent="updateEvent" @printer="printer" :statusRepeat="statusRepeat" @refetch-data="addEvent" :event="eventRep" :dialogs="dialogs" :listBoard="listDepart"></detail-event>
    <reply-event :event="eventRep" :dialogs="dialogs"></reply-event>
    <result-event v-if="role" :result="resultEvent" :event="eventRep" :dialogs="dialogs"></result-event>
    <confirm-component :dialogs.sync="dialogs" :onDelete="deleteEvent" :module="this.$trans('app.event')"></confirm-component>
    <detail-memo :inputVal="memo"  :dialogs="dialogs"></detail-memo>
    <printer-component :dialogs="dialogs" :event="eventRep" :result="resultEvent"></printer-component>
    <detail-notify
      :allBoard="listDepart"
      :inputVal="notifyDetail"
      :userInput="notifyDetail.users"
      :dialogs="dialogs"
    ></detail-notify>
    <alert-component :snackbar="snackbar"></alert-component>
  </div>

</template>

<script>
// eslint-disable-next-line object-curly-newline
import loadingComponent from '@/common/components/loading.vue'
import { ref, onUnmounted, computed, watch,watchEffect } from '@vue/composition-api'
import { mdiChevronLeft, mdiChevronRight, mdiMenu } from '@mdi/js'
import { useResponsiveLeftSidebar } from '@core/comp-functions/ui'
import store from '@/store'
import { hexToRgb, getVuetify } from '@core/utils'
import calendarStoreModule from './calendarStoreModule'
import useStore from  './useStore'
import StoreModule from './StoreModule'
import { directive, Contextmenu, ContextmenuItem } from "v-contextmenu";
import "@mdi/font/css/materialdesignicons.css";
// Local Components
import CalendarLeftSidebarContent from './CalendarLeftSidebarContent.vue'
import CalendarEventHandlerDrawerContent from './CalendarEventHandlerDrawerContent.vue'
import addComponent from './add/index'
import ReplyEvent from  './replyEvent/index'
import ResultEvent from  './resultEvent/index'
import DetailEvent from './detailEvent/index'
import ConfirmComponent from '@/common/components/ConfirmDelete.vue'
import useAppConfig from "../../../@core/@app-config/useAppConfig";
import { registerModule  } from '@/common'
import detailMemo from '@/views/apps/memo/detail/Detail.vue';
import detailNotify from '@/views/apps/notify/detail/Detail.vue';
import {mdiAirplane , mdiCellphoneMessage, mdiPrinter, mdiCalendarPlus ,mdiSignal , mdiFileDocumentOutline , mdiContentDuplicate ,mdiDeleteOutline } from '@mdi/js'
import moment from "moment";
import StoreModuleMemo from '@/views/apps/memo/StoreModule';
import StoreModuleNotify from '@/views/apps/notify/StoreModule';
import AlertComponent from '@/common/components/snackBar.vue'
import { makeIcsFile } from "./iscConfig";
import PrinterComponent from '@/views/apps/calendar/printer/Printer.vue';
import ListEvent from './list'
export default {
  directives: {
    contextmenu: directive,
  },
  methods : {
    next(){
      try{
        this.$refs.refCalendar.next()
      }catch(e){
        let year = Number(this.calendarHeader)
        this.calendarHeader = year + 1
        let start = this.calendarHeader + '-01-01' + ' 00:00:00'
        let end = this.calendarHeader + '-12-31' + ' 23:59:59'
        this.showEventList(this.boardIdArr,start,end)
      }
    },
    prev(){
      try{
        this.$refs.refCalendar.prev()
      }catch(e){
        let year = Number(this.calendarHeader)
        this.calendarHeader = year - 1
        let start = this.calendarHeader + '-01-01' + ' 00:00:00'
        let end = this.calendarHeader + '-12-31' + ' 23:59:59'
        this.showEventList(this.boardIdArr,start,end)
      }
    },
    changeView(event){
      if(event == 'list') {
        this.isList = true
        this.calendarHeader = this.calendarActive.start.year
        return
      }
      this.activeCalendarView = event
      console.log(event);
      this.isList = false
    },
    changeCalendar(event) {

      let start = ''
      let end = ''
      if(this.activeCalendarView === 'month'){
        start = this.$refs.refCalendar.getStartOfWeek(event.start).date + ' 00:00:00'
        end = this.$refs.refCalendar.getEndOfWeek(event.end).date + ' 23:59:59'
        // end = moment(this.$refs.refCalendar.getEndOfWeek(event.end)).add(23, 'hours').add(59, 'minutes').add(59, 'seconds').format('YYYY-MM-DD HH:mm:ss')
      } else {
        start = moment(event.start.date).format('YYYY-MM-DD HH:mm:ss')
        end = moment(event.end.date).add(23, 'hours').add(59, 'minutes').add(59, 'seconds').format('YYYY-MM-DD HH:mm:ss')
      }
      this.calendarActive = event;
      const month = event.start.month
      const year = event.start.year
      this.calendarHeader = year + "/" + month
      this.changeColor(event)
      this.dateActive.start = start
      this.dateActive.end = end
      this.start = this.calendarHeader.split('/')[0] + '-01-01 00:00:00'
      this.end = this.calendarHeader.split('/')[0] + '-12-31 23:59:59'
      this.getHoliday(start, end)
    },
    getEventColor (event) {
      return event.theme_color
    },
    rnd (a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
    },
    mouseRight(event){
      try{
        this.event = event
        this.disabled = event.can_answer
      }catch(e){
        this.eventRep = null
      }
    },
    fixEventRep(){
      this.eventRep = this.event
    },
    actionMouseRight(type = 'can_answer', event = null, callback = null){
      if (event === null) {
        event = this.event
      }
      if(type == 'can_answer'){
        if(event.can_answer){
          if(!store.state.loading){
            store.commit('TOGGLE_LOADING' , true)
            store.dispatch('calendar/detailEvent', { id : event.id })
              .then(response => {
                this.eventRep = response.data.data
                this.dialogs.replyEvent = true
                store.commit('TOGGLE_LOADING' , false)
              })
          }
        }
      }else if(type == 'detail'){
        this.onDetail(event)
      }else if(type == 'result'){
        this.getResult(event)
      }else if(type == 'download'){
        this.downloadFile(event)
      }else if(type == 'duplicate'){
        this.duplicateEvent(event)
      }else if(type == 'delete'){
        this.eventRep = event
        console.log(this.eventRep);
        this.dialogs.dialog = true
      }else if(type == 'printer'){
        this.eventRep = event
        this.printer(event)
      }

    },
  },
  components: {
    CalendarLeftSidebarContent,
    CalendarEventHandlerDrawerContent,
    [Contextmenu.name]: Contextmenu,
    [ContextmenuItem.name]: ContextmenuItem,
    addComponent,loadingComponent,
    ReplyEvent,ResultEvent,ConfirmComponent,
    detailMemo,
    DetailEvent,
    AlertComponent,
    ListEvent,
    detailNotify,
    PrinterComponent
  },
  data() {
    return {
      event : [],
      mode: 'column',
      selectedItem: 1,
      disabled : false,
      disabledResult : false,
      now : null,
      items: [
        { text: 'Real-Time', icon: 'mdi-clock' },
        { text: 'Audience', icon: 'mdi-account' },
        { text: 'Conversions', icon: 'mdi-flag' },
      ],
      weekday: [1, 2, 3, 4, 5, 6, 0],
      textDate : trans('app.date'),
    }
  },

  setup() {
    store.commit('setModuleName', 'calendar')
    registerModule(StoreModule , 'calendar')
    registerModule(StoreModuleMemo , 'memos')
    registerModule(StoreModuleNotify , 'notify')
    const isAction = ref('')
    const eventList = computed(() => {
      return store.state.eventList;
    });

    const statusChangeCalendar = ref(0)
    const listBoardEvent = ref([])
    const calendarHeader = ref('')
    const isList = ref(false)
    const start = ref(null)
    const end = ref(null)
    const dateActive = ref({
      start : new Date(),
      end : new Date()
    })
    const { isDark } = useAppConfig()
    const styleContextMenu = ref({})
    const styleContextMenuItem = ref({})
    if(isDark.value){
      styleContextMenu.value = {
        background : '#28243d',
        color : '#fff'
      }
      styleContextMenuItem.value = {
        color : '#fff'
      }
    }
    const showEventList = (boardIds = [], startTime = null, endTime = null) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/getEvent', {
          all : true,
          board_id : boardIds.length > 0 ? boardIds : boardIdArr.value,
          start : startTime == null ? moment(new Date(start.value)).format('YYYY-MM-DD HH:mm:ss') : startTime,
          end : endTime == null ? moment(new Date(end.value)).format('YYYY-MM-DD HH:mm:ss') : endTime
        })
          .then(response => {
            isList.value = true
            const { data } = response.data
            data.map(item => {
              item.type = 'event'
              return item
            })
            store.commit('setEventList', data)
            store.commit('TOGGLE_LOADING', false)
          })
      }
    }
    const resultEvent = ref({})
    const addEvent = (event) => {
      event.type = 'event'
      let departments = event.departments.map(department => department.id)
      // boardIdArr.value.map(item => {
        // push event to list
          eventStore.value.push(event)
        // return;
      // })
      // getHoliday(dateActive.value.start, dateActive.value.end)
    }
    const getResult = (event) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/chartEvent' , {
          id : event.id,
        }).then(response => {
          resultEvent.value = response.data.data
          eventRep.value = event
          dialogs.value.resultEvent = true
          store.commit('TOGGLE_LOADING' , false)
        }).catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    }
    const duplicateEvent = (event) => {
      statusRepeat.value = false
      onDetail(event, true)
    }
    const snackbar = ref({
      isColorSnackbarVisible : false,
      message : '',
      color : 'error'
    })
    const eventRep = ref({})
    const deleteEvent = event => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/deleteEvent', { id : eventRep.value.id })
          .then(response => {
            snackbar.value.isColorSnackbarVisible = true
            snackbar.value.color = 'success'
            snackbar.value.message = trans('message.update_success')
            dialogs.value.dialog = false
            const updatedEventIndex = eventStore.value.findIndex(event => event.id === eventRep.value.id)
            eventStore.value.splice(updatedEventIndex, 1)
            store.commit('removeEvent', eventRep.value.id)
            store.commit('TOGGLE_LOADING', false)
          })
          .catch(error => {
            store.commit('TOGGLE_LOADING' , false)
          })
      }
    }
    const printer = (event) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/detailEvent', { id : event.id })
          .then(response => {
            const { data } = response.data
            eventRep.value = data
            store.dispatch('calendar/chartEvent' , { id : event.id }).then(response => {
                const { data } = response.data
                data.users.forEach(user => {
                  user.pivot.answered_at = user.pivot.answered_at != null ? moment(user.pivot.answered_at).locale('ja').format('YYYY/MM/DD HH:mm') : ''
                })
                resultEvent.value = data
                dialogs.value.printer = true
                store.commit('TOGGLE_LOADING' , false)
              })
          })
      }
    }

    const openDialog = (event) => {
      if(event.event.type == 'memo'){
        dialogs.value.replyEvent = true
      }
    }
    const clickEvent = (event) => {
      if(event.event.type == 'memo'){
        detailMemo(event.event)
      }
      if(event.event.type == 'event'){
        if(event.event.can_answer){
          if(role.value){
            statusRepeat.value = true
            onDetail(event.event)
          }
          else {
            if(!store.state.loading){
              store.commit('TOGGLE_LOADING' , true)
              store.dispatch('calendar/detailEvent', { id : event.event.id })
                .then(response => {
                  eventRep.value = response.data.data
                  dialogs.value.replyEvent = true
                  store.commit('TOGGLE_LOADING' , false)
                })
            }
          }
        } else {
          if(role.value){
            onDetail(event.event)
          }
        }
      }
      if(event.event.type == 'notify'){
        if(role.value){
          detailNotify(event.event)
        }else {
          return
        }

      }
    }
    const statusRepeat = ref(true)
    const onDetail = (event, state = null) => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/detailEvent', { id : event.id })
          .then(response => {
            if(state){
              response.data.data.id = null
              statusRepeat.value = false
            }
            eventRep.value = response.data.data
            dialogs.value.detailEvent = true
            store.commit('TOGGLE_LOADING' , false)
          })
      }
    }
    const memo = ref({})
    const detailMemo = (item) => {
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/detailMemo', {id : item.id})
          .then(response => {
            memo.value = response.data.data
            dialogs.value.detailMemo = true
            store.commit('TOGGLE_LOADING' , false)
          })
          .catch(error =>  {
            store.commit('TOGGLE_LOADING' , false)
            snackbar.value.isColorSnackbarVisible = true
            snackbar.value.color = 'error'
            snackbar.value.message =  error.response.data.errors
          })
      }
    }
    const notifyDetail = ref({})
     const detailNotify = item => {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch("notify/fetchOne", { id: item.id }).then(response => {
          dialogs.value.detailNotify = true;
          store.commit('TOGGLE_LOADING' , false)
          notifyDetail.value = response.data.data
        })
      }
    };
    const convertDate = (date) => {
      var event = new Date(date).toISOString();
      event = event.split("T")[0];
      event = event.split("-");
      event = event.join("");
      return event;
    }
    const icsFile = ref(null)
    const downloadFile = () => {
      let event = {
        start : moment(eventRep.value.start).format('YYYY-M-D-H-m').split("-").map(item => item = parseInt(item)),
        end : moment(eventRep.value.end).format('YYYY-M-D-H-m').split("-").map(item => item = parseInt(item)),
        summary : eventRep.value.detail ?? '',
        description : eventRep.value.detail ?? '',
        method : 'PUBLISH',
        url : eventRep.value.place_url,
        title : eventRep.value.title,
        location : eventRep.value.place,
      }
      let ics = require("ics")
      ics.createEvent(event, (error, value) => {
        if(error){

        }
        else {
          icsFile.value = value
        }
      })
      let link = document.createElement("a")
      link.href = makeIcsFile(icsFile.value)
      let date = new Date()
      let fileName =
        String(date.getFullYear()) +
        String(date.getMonth() + 1) +
        String(date.getDate()) +
        String(date.getHours()) +
        String(date.getMinutes()) +
        String(date.getSeconds())
      link.download = "calendar" + fileName + ".ics"
      link.click()
    }

    const {
      dialogs,getNotify,
      listBoard, getHoliday,itemHoliday,
      eventStore, holidays, loading,role,arrEvent,
      readOnly , memos,notify,listEvent,boardIdArr,listDepart
    } = useStore()

    // ————————————————————————————————————
    //* ——— Left Sidebar
    // ————————————————————————————————————
    const { isLeftSidebarOpen, contentStyles } = useResponsiveLeftSidebar({ sidebarWidth: 250 })
    const calendarActive = ref({
      start : { date : new Date() , time : '00:00' },
      end : { date : new Date() , time : '23:59' }
    })


    const changeDepartment = (event) => {
      statusChangeCalendar.value++
      listBoardEvent.value = event
    }
    const count = ref(0);
    const changeColor =  (event) => {
      if(activeCalendarView.value == 'day'){
        if(event.start.weekday == 6){
          document.documentElement.style.setProperty('--color' , '#30529e')
        }
        else if(event.start.weekday == 0) {
          document.documentElement.style.setProperty('--color' , 'red')
        }
        else {
          document.documentElement.style.setProperty('--color' , '#000')
        }
      }
    }
    // ————————————————————————————————————
    //* ——— Templare Ref
    // ————————————————————————————————————


    // ————————————————————————————————————
    //* ——— Calendar View/Type
    // ————————————————————————————————————

    const activeCalendarView = ref('month')
    // const calendarViewOptions = ['month', 'week', 'day']
    const calendarViewOptions = [
      {value : 'month', text: trans('app.month')},
      {value : 'week', text: trans('app.week')},
      {value : 'day', text: trans('app.day')},
    ]
    // ————————————————————————————————————
    //* ——— Calendar Value & Events
    // ————————————————————————————————————

    const calendarValue = ref()
    const calendarEvents = ref([])
    const blankEvent = {
      id: '',
      name: '',
      start: '',
      end: '',
      timed: false,
      extendedProps: {
        calendar: '',
        guests: [],
        location: '',
        description: '',
      },
    }
    const event = ref(JSON.parse(JSON.stringify(blankEvent)))
    const clearEventData = () => {
      event.value = JSON.parse(JSON.stringify(blankEvent))
    }

    const random = (start, end, startHour, endHour) => {
      let date = new Date(+start + Math.random() * (end - start));
      let hour = startHour + Math.random() * (endHour - startHour) | 0;
      date.setHours(hour);
      return date;
    }
    const getEventTextColor = e => e.eventTextColor

    //
    //* ——— Calendar Event Handler ——————————————————
    //

    // TODO: Remove this once issue is resolved
    // Please check: https://github.com/vuetifyjs/vuetify/issues/13900
    const eventClickBugWorkaround = ref(false)

    const calenderHandleEventClick = eventObj => {
      // Grab only event object
      event.value = eventObj.event

      // eslint-disable-next-line no-use-before-define
      isEventHandlerDrawerActive.value = true

      // Bug workaround
      eventClickBugWorkaround.value = true
    }

    const calendarHandleDayClick = dayObj => {
      // Bug workaround
      if (eventClickBugWorkaround.value) {
        eventClickBugWorkaround.value = false

        return
      }

      event.value.start = dayObj.date

      // eslint-disable-next-line no-use-before-define
      isEventHandlerDrawerActive.value = true
    }

    const calendarHandleMoreClick = ({ date }) => {
      // changeBackgroundHoliday()
      calendarValue.value = date

      activeCalendarView.value = 'day'

      // Bug workaround
      eventClickBugWorkaround.value = true
    }
    const updateEvent = (event) => {
      console.log(event);
      eventStore.value = eventStore.value.map(item => {
        if (item.id === event.id) {
          return event
        }
        return item
      })
    }





    const selectedCalendars = ''



    const isEventHandlerDrawerActive = ref(false)
    onUnmounted(() => {
      isEventHandlerDrawerActive.value = true
    })
    watch(listBoardEvent , (newV, oldV) => {
      if(statusChangeCalendar.value > 1) {
        if (newV.length <= 0) {
          store.commit('setSnackbar', {
            isColorSnackbarVisible: true,
            color: 'error',
            message: '少なくとも1つの部門を選択してください'
          })
          return
        } else{
          if( ( activeCalendarView.value == 'month' || activeCalendarView.value == 'week' || activeCalendarView.value == 'day') && !isList.value) {
            getHoliday(dateActive.value.start, dateActive.value.end, newV)
          }else {
            boardIdArr.value = newV
           showEventList(newV, start.value, end.value)
          }
        }
      }
    })

    return {
      printer,
      eventList,
      start,
      end,
      updateEvent,
      detailNotify,notifyDetail,
      boardIdArr,onDetail,role,duplicateEvent,statusChangeCalendar,
      convertDate,makeIcsFile,downloadFile,statusRepeat,
      eventRep,listBoardEvent,snackbar,
      showEventList,
      changeDepartment,deleteEvent,
      getResult,resultEvent,
      addEvent,
      getNotify,
      readOnly,
      memo,
      memos,listEvent,notify,
      detailMemo,
      loading,
      openDialog,
      holidays,
      getHoliday,
      clickEvent,
      calendarHeader,
      count,
      moment,
      random,
      styleContextMenuItem,
      isDark,
      styleContextMenu,
      dialogs,
      eventStore,
      // Left Sidebar
      isLeftSidebarOpen,
      contentStyles,
      // Template Refs
      getEventTextColor,
      changeColor,
      arrEvent,
      // Calendar View/Type
      activeCalendarView,
      calendarViewOptions,
      isList,
      // Calendar Value & Events
      listDepart,
      calendarValue,
      calendarEvents,
      event,
      listBoard,
      clearEventData,
      calenderHandleEventClick,
      calendarHandleDayClick,
      calendarHandleMoreClick,
      itemHoliday,
      // Event handler sidebar
      isEventHandlerDrawerActive,
      dateActive,

      // Icons
      icons: {
        mdiAirplane,
        mdiCellphoneMessage,
        mdiChevronLeft,
        mdiChevronRight,
        mdiMenu,
        mdiCalendarPlus,
        mdiSignal,
        mdiFileDocumentOutline,
        mdiContentDuplicate,
        mdiDeleteOutline,
        mdiPrinter
      },
    }
  },
}
</script>

<style lang="scss">

.v-contextmenu .v-contextmenu-item{
  margin: .5rem;
  text-align: left !important;
}
.v-contextmenu .v-contextmenu-item:hover {
  background-color: #f1f1f1;
  color: #000 !important;
}
.v-calendar-weekly .v-calendar-weekly__head-weekday {
  color: revert !important;
}
.v-contextmenu{
  border-radius: 10px !important;
}
.app-calendar{
  height: 100% !important;
}
.calendar-header{
  margin-top: 0 !important;
}

.app-calendar .v-calendar{
  height: 100% !important;
}

@import '~@core/preset/preset/apps/calendar.scss';
$color : #000;
.v-calendar-daily__head {
  .v-calendar-daily_head-weekday {
    font-size: 14px;
    font-weight: 600;

    padding-top: 7px;
    padding-bottom: 7px;

    border-right: none !important;
    text-transform: none;

    @at-root {
      @include theme(app-calendar) using($material) {
        .v-calendar-daily__head {
          .v-calendar-daily_head-weekday, .v-past, .v-calendar-daily_head-weekday.v-outside {
            color: var(--color, $color) !important;
          }
        }
      }
    }
  }
}


.holiday.holiday__wrapper {
  width: 100%;
  height: 100%;
  position: absolute;
  background: #ff171752;
  bottom: 0;
  align-items: center;
  justify-content: center;
}
.holiday.holiday__wrapper div {
  display: flex !important;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
.holiday {
  justify-content:center;
  color : $color;
}
.v-application--is-ltr .app-calendar .v-calendar-weekly__week .v-calendar-weekly__day-label .v-btn{
  margin-left: 24px !important;
}
.v-calendar-daily_head-day {
  padding : 10px 0;
}


.v-calendar-weekly__head-weekday:nth-child(6) ,.v-calendar-daily__head .v-calendar-daily_head-day:nth-child(7) .v-calendar-daily_head-weekday,.v-calendar-weekly__day:nth-child(6) .v-calendar-weekly__day-label div,
.v-calendar-daily_head-day:nth-child(7) .v-calendar-daily_head-day-label div div
{
  color : #30529e !important;
}
.v-calendar-weekly__head-weekday:nth-child(7) ,.v-calendar-daily__head .v-calendar-daily_head-day:nth-child(8) .v-calendar-daily_head-weekday, .v-calendar-weekly__day:nth-child(7) .v-calendar-weekly__day-label div,
.v-calendar-daily_head-day:nth-child(8) .v-calendar-daily_head-day-label div div,.v-calendar-daily_head-day.v-past .v-calendar-weekly__head-weekday
{
  color : red !important;
}
.v-calendar-daily__interval{
  text-align: center!important;
  padding-right: 0 !important;
}
.v-calendar .v-event-timed {
  transform: translateX(5px) !important;
}
.app-calendar .v-event > div{
  font-weight: revert !important;
}
.v-calendar.v-calendar-events .v-calendar-weekly__head-weekday{
  margin-right: 0 !important;
}
.v-calendar-daily_head-weekday:nth-child(6){
  color : yellow !important;
}
</style>
<style scoped>
.v-chip .v-chip__content{
  font-size: 12px;
}
.app-calendar .v-calendar-daily__head .v-calendar-daily_head-weekday{
  display: none !important;
}
.v-calendar-daily_head-weekday{
  color : yellow !important;
}
.v-text-field__details{
  overflow: revert !important;
  white-space: nowrap !important;
}
</style>
