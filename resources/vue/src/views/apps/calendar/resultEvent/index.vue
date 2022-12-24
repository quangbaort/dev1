<template>
<div>
  <v-dialog v-model="dialogs.resultEvent" max-width="800" persistent scrollable>
    <v-card>
      <v-card-title class="headline">行事出欠回答一覧</v-card-title>
      <v-tabs v-model="currentTab" icons-and-text grow>
        <v-tab> {{this.$trans('app.response_status')}}
          <v-icon>{{ icons.mdiSignal }}</v-icon>
        </v-tab>
        <v-tab> {{this.$trans('app.detail')}}
          <v-icon>{{ icons.mdiFileDocumentOutline  }}</v-icon>
        </v-tab>
      </v-tabs>
      <v-card-text>
        <v-tabs-items v-model="currentTab" class="px-8 mb-4">
          <v-tab-item class="pt-6" :transition="false">
            <v-row>
              <v-col cols="12" md="6">
                <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :close-on-click="closeOnClick"
                        transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field  hide-details="auto" outlined dense style="padding-top: 0" v-model="startDateFormated" :label="textDate.startDate"  v-bind="attrs" v-on="on" ></v-text-field>
                  </template>
                  <v-date-picker hide-details="auto" @input="menu = false" locale="ja" no-title scrollable
                                 :day-format="startDate => new Date(startDate).getDate()" @change="changeStartDate($event)" @keydown.enter="changeStartDate">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6">
                <v-menu ref="menu1" v-model="menu1" :close-on-content-click="false" :close-on-click="closeOnClick"
                        transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field  hide-details="auto" outlined dense style="padding-top: 0"  v-model="endDateFormated"
                                   :rules="[validator.startDateBefore(startDate, endDate)]"
                                   :label="textDate.endDate"  v-bind="attrs" v-on="on" ></v-text-field>
                  </template>
                  <v-date-picker hide-details="auto" @input="menu1 = false"  locale="ja" no-title scrollable
                                 :day-format="endDate => new Date(endDate).getDate()" @change="changeEndDate($event)" @keyup.enter="changeEndDate">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6">
                <div class="Chart">
                  <chart-data ref="skills_chart":key="key" style="width: 80%" :chart-data="chartData" :options="options"></chart-data>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <v-row align-center>
                  <v-col>
                    <h3>{{this.$trans('app.response_rate')}} :</h3>
                  </v-col>
                  <v-col class="text-right">
                    <v-chip color="warning" outlined style="width: 50%" class="d-inline-block text-center">{{ parseInt(result.totalAnswer) }}%</v-chip>
                  </v-col>
                </v-row>
                <v-divider class="my-2"></v-divider>
                <div class="pt-5 mt-5 align-center">
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : #ccc"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{this.$trans('app.no_response')}}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ result.unanswered }}</div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : green"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{this.$trans('app.attendance')}}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ result.attendance }}</div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : #ffb400"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{this.$trans('app.on_hold')}}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ result.pending }}</div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="d-flex align-center" cols="4" md="2">
                      <div class="cycle" style="background : red"></div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{this.$trans('app.absent')}}：</div>
                    </v-col>
                    <v-col class="d-flex align-center" cols="4" md="4">
                      <div>{{ result.absence }}</div>
                    </v-col>
                  </v-row>
                </div>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="pt-6" :transition="false">
            <v-form ref="form">
              <v-row>
                <v-col cols="12" md="3">
                  <v-text-field hide-details="auto" @change="search" @keyup.enter="search(name)" v-model="name" outlined :label="this.$trans('app.name')" dense></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field hide-details="auto" outlined @change="search" @keyup.enter="search(nameCompany)"  v-model="nameCompany" :label="this.$trans('app.company')" dense></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-menu ref="menu2" v-model="menu2" :close-on-content-click="false"  :close-on-click="closeOnClick"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details="auto" outlined dense style="padding-top: 0" v-model="startDateFormated1" :label="textDate.startDate"  v-bind="attrs" v-on="on" ></v-text-field>
                    </template>
                    <v-date-picker hide-details="auto"  @input="menu2 = false" locale="ja" no-title scrollable
                                   :day-format="startDate1 => new Date(startDate1).getDate()" @change="changeStartDate1($event)" @keyup.enter="changeStartDate1">
                    </v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="12" md="3">
                  <v-menu ref="menu3" v-model="menu3" :close-on-content-click="false"  :close-on-click="closeOnClick"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details="auto"  outlined dense style="padding-top: 0"
                                    v-model="endDateFormated1"
                                    :rules="[validator.startDateBefore(startDate1, endDate1)]"
                                    :label="textDate.endDate"  v-bind="attrs" v-on="on" ></v-text-field>
                    </template>
                    <v-date-picker hide-details="auto"  @input="menu3 = false"  locale="ja" no-title scrollable
                                   :day-format="endDate1 => new Date(endDate1).getDate()" @change="changeEndDate1($event)" @keyup.enter="changeEndDate1">
                    </v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>
            </v-form>
            <v-row class="w-100">
              <v-col cols="12" md="12" class="pb-4">
                <v-data-table :items-per-page="-1" :loading-text="this.$trans('app.loading_item')"
                :header-props="headerProps"
                              :loading="loading" @update:items-per-page="getEventReplies($event, paginate.currentPage)"
                              @update:page="getEventReplies(paginate.itemsPerPage, $event)"
                              :server-items-length="paginate.total"
                              :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                              :items="listUser" :headers="tableColumnResultEvent"
                              class="elevation-1">
                  <template #[`item.actions`]="{item}" >
                    <v-menu bottom left>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn icon v-bind="attrs" v-on="on">
                          <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item link class="my-2" v-for="listActionResult in listActionResults" :key="listActionResult.title">
                          <v-list-item-title >
                            <v-btn @click="replyEvent(item.id, listActionResult.value)" :color="listActionResult.color">{{listActionResult.title}}</v-btn>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                    <template #[`item.email`]="{item}" >
                      <div>{{formatDate(item.pivot.answered_at)}}</div>
                    </template>
                  <template v-slot:no-data>
                    {{ textNoData }}
                  </template>
                  <template #[`item.pivot`]="{item}">
                    <p v-if="item.pivot.answer == 0"></p>
                    <v-chip small color="green" v-if="item.pivot.answer == 1">{{ textData.attendance }}</v-chip>
                    <v-chip small color="yellow" v-if="item.pivot.answer == 3">{{ textData.on_hold }} </v-chip>
                    <v-chip small color="red" v-if="item.pivot.answer == 2">{{ textData.absent }} </v-chip>
                  </template>
                  <template #[`item.company`]="{item}">
                    {{item.company.name}}
                  </template>
                  <template #[`item.name`]="{item}">
                    {{ item.name }}
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="justify-end py-3">
          <v-btn color="default mx-2" @click="close">{{this.$trans('app.close')}}</v-btn>
          <v-btn color="success mx-2" @click="sendMailRemind">
            {{ this.$trans('app.re_event_to_unanswered') }}
          </v-btn>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</div>
</template>

<script>
import { mdiSignal , mdiFileDocumentOutline , mdiDotsVertical} from "@mdi/js";
import chartData from '@/common/components/Chart.vue'
import useStore from "../useStore";
import { itemPerPage, formatDate ,formatTimeToDate,textNoData,headerProps } from "@/common";
import { startDateBefore } from '@core/utils/validation'
import store from '@/store'
import moment from "moment"
export default {
  computed : {
    rate () {
      return this.result
    },
  },
  components : {
    chartData
  },
  data() {
    return {
      closeOnClick : false,
      eventId : null,
      statusSend : this.$trans('app.re_event_to_unanswered'),
      textData : {
        no_response:  this.$trans('app.no_response'),
        attendance :  this.$trans('app.attendance'),
        on_hold :     this.$trans('app.on_hold'),
        absent:       this.$trans('app.absent'),
      },
      currentTab : true,
      icons : {
        mdiFileDocumentOutline , mdiSignal , mdiDotsVertical
      },
      listUser : [],
      name : null,
      nameCompany : null,
      loading : false,
      listTable : [],
      paginate : {},
      key : 0,
      startDate : null,
      startDateFormated: null,
      endDateFormated: null,
      endDate : null,
      startDate1 : null,
      startDateFormated1: null,
      endDateFormated1: null,
      endDate1 : '',
      menu: false,
      modal: false,
      menu1: false,
      menu2: false,
      menu3 : false,
      menu4 : false,
      statusChange: false,
      options : {
        responsive : true,
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
        labels: [trans('app.no_response') , trans('app.attendance') , trans('app.on_hold'), trans('app.absent')],
        datasets: [
          {
            backgroundColor: ['#ccc', 'green' , '#ffb400','red'],
            data: [0,0,0,0]
          },
        ]
      },
      userResponse: []
    }
  },
  watch: {
    startDate(val) {
      this.startDateFormated = formatDate(this.startDate)
      // this.getChart()
    },
    endDate(val) {
      this.endDateFormated = formatTimeToDate(this.endDate)
      // this.getChart()
    },
    startDate1(val) {
      this.startDateFormated1 = formatTimeToDate(this.startDate1)
      // this.getEventReplies()
    },
    endDate1(val) {
      this.endDateFormated1 = formatTimeToDate(this.endDate1)
      // this.getEventReplies()
    },
    result(val) {
      if(!this.statusChange){
        this.listUser = val.users
      }
      this.key = Math.random()*999
      this.chartData.datasets[0].data = [parseInt(val.unanswered),parseInt(val.attendance),parseInt(val.pending),parseInt(val.absence)]
    },
    userResponse(val) {
      this.listUser = val.users
    },
  },
  methods : {
    changeStartDate(value) {
      // return date
      this.startDate = value
      this.statusChange = true
      this.getChart()
    },
    close(){
      this.startDate = null
      this.endDate = null
      this.name = null
      this.nameCompany = null
      this.dialogs.resultEvent = false
      this.currentTab = null
    },
    changeEndDate(value) {
      // return date
      this.endDate = value
      this.statusChange = true
      this.getChart()
      // return date = value
    },
    changeStartDate1(value){
      this.startDate1 = value
      this.statusChange = true
      this.getChart1()
    },
    changeEndDate1(value){
      this.endDate1 = value
      this.statusChange = true
      this.getChart1()
    },
    getChart(){
      if(!store.state.loading){
          store.commit('TOGGLE_LOADING' , true)
            store.dispatch('calendar/chartEvent' , {
            id : this.event.id,
            start : this.startDate ? moment(this.startDate).format("YYYY-MM-DD HH:mm:ss") : '',
            end : this.endDate ? moment(this.endDate).add(23,'hours').add(59,'minutes').add(59,'seconds').format("YYYY-MM-DD HH:mm:ss") :'',
          }).then(response => {
            try{
              this.result = response.data.data
              console.log(this.result);
            }catch(error) {}
            store.commit('TOGGLE_LOADING' , false)
          }).catch(error => {
            store.commit('TOGGLE_LOADING' , false)
            store.commit('setSnackbar' , {
              isColorSnackbarVisible : true,
              color : 'error',
              message : error.response.data.errors
            })
          })
      }
    },
     getChart1(){
      if(!store.state.loading){
          store.commit('TOGGLE_LOADING' , true)
            store.dispatch('calendar/chartEvent' , {
            id : this.event.id,
            start : this.startDate1 ? moment(this.startDate1).format("YYYY-MM-DD HH:mm:ss") : '',
            end : this.endDate1 ? moment(this.endDate1).add(23,'hours').add(59,'minutes').add(59,'seconds').format("YYYY-MM-DD HH:mm:ss") :'',
            name : this.name ?? '',
            company : this.nameCompany ?? '',
          }).then(response => {
            try{
              this.userResponse = response.data.data.users
            }catch(error) {}
            store.commit('TOGGLE_LOADING' , false)
          }).catch(error => {
            store.commit('TOGGLE_LOADING' , false)
            store.commit('setSnackbar' , {
              isColorSnackbarVisible : true,
              color : 'error',
              message : error.response.data.errors
            })
          })
      }
    },
    getEventReplies(limit = null, page = null, name = null, nameCompany = null, ){
      if(!this.loading && this.startDate1 <= this.endDate1){
        this.loading = true
        store.dispatch('calendar/getEventReplies' , {
          event_id : this.event.id,
          startDate :this.startDate1 ? moment(this.startDate1).format("YYYY-MM-DD") : '',
          endDate :this.endDate1 ? moment(this.endDate1).add(23, 'hours').add(59, 'minutes').add(59, 'seconds').format("YYYY-MM-DD HH:mm:ss") : '',
          name : name ?? '',
          company : nameCompany ?? '',
          limit : limit,
          page : page,
        }).then(result => {
          const { data , meta } = result.data
          this.paginate = {
            total: meta.total,
            currentPage : parseInt(meta.current_page),
            itemsPerPage : parseInt(meta.per_page),
            pageStart : meta.from,
            pageStop : meta.last_page,
            pageCount : parseInt(Math.ceil(meta.total / meta.per_page)),
            itemsLength :meta.last_page
          }
          this.listTable = data
          this.loading = false
        }).catch(error => {
          this.loading = false
          store.commit('setSnackbar' , {
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        })
      }
    },
    replyEvent(userID,value){
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/replyEventAdmin' , {
          id : this.result.id,
          answer : value,
          user_id : userID,
        }).then(response => {
          store.commit('TOGGLE_LOADING' , false)
          this.getChart()
          store.commit('setSnackbar' , {
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.update_success')
          })
        })
      }
    },
    search(event){
        this.getChart()
    },
    sendMailRemind(){
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('calendar/sendMailRemind' , {
          id : this.event.id
        }).then(response => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' , {
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('app.send_re_event_to_unanswered')
          })
          this.statusSend = trans('app.send_re_event_to_unanswered')
        })
      }
    }
  },
  props : {
    dialogs : {
      type : Object,
      require : true
    },
    result : {
      type : Object
    },
    event : {
      type : Object
    }
  },
  setup(){
    const { tableColumnResultEvent, tableList ,fetchResultEvent, listActionResults , textDate } = useStore()
    return {
      itemPerPage,formatDate,textNoData,headerProps,
      tableColumnResultEvent, tableList,fetchResultEvent, listActionResults ,textDate,
      validator : {
        startDateBefore
      }
    }
  }
}
</script>

<style scoped>
.v-item-group{
  min-height: 350px;
}
.cycle{
  width: 20px !important;
  height: 20px !important;
  border-radius:50%;
}
.chart {
  height: 20px !important;
}

.v-text-field{
  padding-top: 0 !important;
}
.v-chip .v-chip__content{
  font-size: 14px !important;
}
</style>
