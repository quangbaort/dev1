<template>
  <div>
    <v-dialog v-model="dialogs.resultAnswer" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
      <v-card>
          <v-card-title class="headline lighten-2" primary-title>
            <span class="headline">{{this.$trans('app.notify_confirm_status_list')}}</span>
          </v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow >
            <v-tab>{{this.$trans('app.confirm_status')}}
              <v-icon>{{ icons.mdiSignal }}</v-icon>
            </v-tab>
            <v-tab>{{this.$trans('app.detail')}}
              <v-icon>{{ icons.mdiFileDocumentOutline  }}</v-icon>
            </v-tab>
        </v-tabs>
        <v-card-text>
          <v-tabs-items v-model="currentTab" class="mx-8">
            <v-tab-item class="pt-6" :transition="false">
              <v-row >
                <v-col cols="12" md="6">
                  <v-menu ref="menu" v-model="menu" :close-on-content-click="false"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details style="padding-top: 0" v-model="startDateFormated" :label="textDate.startDate"  v-bind="attrs" v-on="on" ></v-text-field>
                    </template>
                    <v-date-picker hide-details="auto" @input="menu = false" locale="ja" no-title scrollable
                                   :day-format="startDate => new Date(startDate).getDate()" @change="$event => startDate = $event">
                    </v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="12" md="6">
                  <v-menu ref="menu1" v-model="menu1" :close-on-content-click="false"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details="auto" style="padding-top: 0"  v-model="endDateFormated" :label="textDate.endDate" readreadonly v-bind="attrs" v-on="on"
                                    :rules="[validator.startDateBefore(startDate, endDate)]"></v-text-field>
                    </template>
                    <v-date-picker hide-details="auto" @input="menu1 = false"  locale="ja" no-title scrollable
                                   :day-format="endDate => new Date(endDate).getDate()" @change="$event => endDate = $event">
                    </v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="Chart">
                    <chart-data ref="skills_chart" style="width: 90%" :key="key" :chart-data="chartData" :options="options"></chart-data>
                  </div>
                </v-col>
                <v-col cols="12" md="6">
                  <v-row align-center>
                    <v-col>
                      <h3>{{ this.$trans('app.already_read') }} :</h3>
                    </v-col>
                    <v-col class="text-right">
                      <v-chip color="warning" outlined style="width:50%" class="d-inline-block text-center">{{ parseInt(this.rateRead) }}%</v-chip>
                    </v-col>
                  </v-row>
                  <v-divider class="my-2"></v-divider>
                  <div class="pt-5 mt-5 align-center">
                    <v-row>
                      <v-col class="d-flex align-center" cols="4" md="2">
                        <div class="cycle" style="background : #00DA63"></div>
                      </v-col>
                      <v-col class="d-flex align-center" cols="4" md="3">
                        <div>{{ this.$trans('app.already_read') }}：</div>
                      </v-col>
                      <v-col class="d-flex align-center" cols="4" md="4">
                        <div>{{parseInt(rateRead)}}</div>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col class="d-flex align-center" cols="4" md="2">
                        <div class="cycle" style="background : #FF0000"></div>
                      </v-col>
                      <v-col class="d-flex align-center" cols="4" md="3">
                        <div>{{ this.$trans('app.unread') }}：</div>
                      </v-col>
                      <v-col class="d-flex align-center" cols="4" md="4">
                        <div>{{100 - parseInt(rateRead)}}</div>
                      </v-col>
                    </v-row>
                  </div>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="pt-6" :transition="false">
              <v-row>
                <v-col cols="12" md="3">
                  <v-text-field v-model="userName" @change="search" @keyup.enter="search(userName)" hide-details="auto" outlined :label="this.$trans('app.name')" dense></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field v-model="companyName" @change="search" @keyup.enter="search(companyName)" hide-details="auto" outlined :label="this.$trans('app.company')" dense></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-menu ref="menu2" v-model="menu2" :close-on-content-click="false"
                          transition="scale-transition" width="270">
                    <template  outlined dense v-slot:activator="{ on, attrs }">
                      <v-text-field hide-details="auto" style="padding-top: 0" outlined dense v-model="startDateFormated1" :label="textDate.startDate"  v-bind="attrs" v-on="on" ></v-text-field>
                    </template>
                    <v-date-picker dense hide-details="auto" @input="menu2 = false" locale="ja" no-title scrollable
                                   :day-format="startDate1 => new Date(startDate1).getDate()" @change="$event => startDate1 = $event">
                    </v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="12" md="3">
                  <v-menu ref="menu3" v-model="menu3" :close-on-content-click="false"
                          transition="scale-transition" width="270">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field dense hide-details="auto" outlined style="padding-top: 0"  v-model="endDateFormated1" :label="textDate.endDate" readreadonly v-bind="attrs" v-on="on"
                                    :rules="[validator.startDateBefore(startDate1, endDate1)]"></v-text-field>
                    </template>
                    <v-date-picker dense hide-details="auto" @input="menu3 = false"  locale="ja" no-title scrollable
                                   :day-format="endDate1 => new Date(endDate1).getDate()" @change="$event => endDate1 = $event">
                    </v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" md="12" class="pa-4">
                  <v-data-table :header-props="headerProps" :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }"
                                :items="listUserRep" :headers="tableColumnsNotify" class="elevation-1" :loading="loading">
                    <template v-slot:no-data>
                      {{ textNoData }}
                    </template>
                    <template #[`item.read_at`]="{item}">
                      <span>{{formatTimeToDate(item.read_at)}}</span>
                    </template>
                    <template #[`item.read`]="{item}">
                      <v-chip v-if="!item.read_at" color="error"  class="text-center v-chip-light-bg error--text" small >{{readStatus.unRead}}</v-chip>
                      <v-chip v-if="item.read_at" color="success"  class="text-center v-chip-light-bg success--text" small >{{readStatus.readed}}</v-chip>
                    </template>
                    <template #[`item.actions`]="{item}">
                      <v-menu bottom left>
                        <template v-slot:activator="{ on, attrs }">
                          <v-row>
                            <v-icon @click="dialogs.comment = true">{{ icons.mdiCommentProcessingOutline }}</v-icon>
                            <v-btn icon v-bind="attrs" v-on="on">
                              <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                            </v-btn>
                          </v-row>
                        </template>
                        <v-list>
                          <v-list-item link>
                            <v-list-item-title>
                              <v-icon size="20" class="me-2">
                                {{ icons.mdiBriefcaseCheck }}
                              </v-icon>
                              <span>{{ listActionSafety.during }}</span>
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item link>
                            <v-list-item-title>
                              <v-icon size="20" class="me-2">
                                {{ icons.mdiCheckCircleOutline  }}
                              </v-icon>
                              <span>{{listActionSafety.acp}}</span>
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </v-data-table>
                </v-col>
              </v-row>
            </v-tab-item>
          </v-tabs-items>
        </v-card-text>
        <v-divider class="my-2"></v-divider>
        <v-card-actions>
          <v-row class="justify-end py-3">
            <v-btn color="default" class="mx-2" @click="close">{{this.$trans('app.close')}}</v-btn>
            <v-btn color="success" class="mx-2"@click="remindUnRead">{{this.$trans('app.re_notify_to_unread')}}</v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-form>
  </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>
  </div>
</template>
<script>
import { startDateBefore } from '@core/utils/validation'
import { mdiOfficeBuildingOutline , mdiDotsVertical , mdiCheckCircleOutline , mdiBriefcaseCheck , mdiSignal , mdiMapMarkerOutline , mdiFileDocumentOutline ,mdiCommentProcessingOutline ,mdiLinkVariant  } from '@mdi/js'
import chartData from '@/common/components/Chart.vue'
import useStore from '../use_store'
import { itemPerPage , formatDate, textNoData, formatTimeToDate, headerProps } from "@/common"
import store from '@/store'
import AlertComponent from '@/common/components/snackBar.vue'

export default {
  props:{
    dialogs : { type : Object },
    listUserRepInput : { required : true },
    rateReadInput : { required : true },
    newsId : { required : true, type: String },
  },
  components: {
    chartData,
    AlertComponent
  },
  data() {
    return {
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      errorMessages: {},
      currentTab: null,
      menu: false,
      modal: false,
      menu1: false,
      menu2: false,
      menu3: false,
      menu4: false,
      key: 0,
      startDateFormated: '',
      endDateFormated: '',
      startDateFormated1: '',
      endDateFormated1: '',
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
        labels: [trans('app.already_read') , trans('app.unread')],
        datasets: [
          {
            backgroundColor: ['#00DA63' , '#FF0000'],
            data: [0, 100]
          },
        ]
      }
    }
  },
  methods : {
    close() {
      this.startDate= ''
      this.endDate= ''
      this.startDate1= ''
      this.endDate1= ''
      this.startDateFormated= ''
      this.endDateFormated= ''
      this.startDateFormated1= ''
      this.endDateFormated1= ''
      this.companyName = ''
      this.userName = ''
      this.dialogs.resultAnswer = false
      this.currentTab = null
    },
    remindUnRead() {
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('notify/sendMailRemind', this.newsId)
          .then(response => {
            store.commit('TOGGLE_LOADING' , false)
            this.dialogs.addNew = false
            store.commit('setSnackbar', {
              isColorSnackbarVisible : true,
              color : 'success',
              message : trans('message.send_mail_to_user_unread_notify')
            })
          })
      }
    },
    search(event){
        this.getUserReply(this.newsId)
    }
  },
  watch: {
    rateReadInput (newVal) {
      this.rateRead = newVal
    },
    rateRead (newVal) {
      this.key += 1
      this.chartData.datasets[0].data = [newVal, 100-newVal]
      this.key = Math.random()*999
    },
    listUserRepInput (newVal) {
      this.listUserRep = newVal
    },
    startDate (val) {
      this.startDateFormated = this.formatDate(this.startDate)
      this.getStatistic(this.newsId)
    },
    endDate (val) {
      this.endDateFormated = this.formatDate(this.endDate)
      this.getStatistic(this.newsId)
    },
    startDate1 (val) {
      this.startDateFormated1 = this.formatDate(this.startDate1)
      this.getUserReply(this.newsId)
    },
    endDate1 (val) {
      this.endDateFormated1 = this.formatDate(this.endDate1)
      this.getUserReply(this.newsId)
    },
  },
  setup() {
    const { tableColumnsNotify, listActionSafety, unreadAction , textDate, readStatus,rateRead,loading,
      getStatistic,getUserReply, startDate, endDate,startDate1, endDate1, userName, companyName, listUserRep } = useStore()
    return {
      tableColumnsNotify,itemPerPage,formatDate, formatTimeToDate,headerProps,
      listActionSafety, unreadAction,textDate, textNoData, readStatus, loading,
      getStatistic,getUserReply, startDate, endDate, startDate1, endDate1, rateRead, userName, companyName,listUserRep,
      icons : { mdiDotsVertical ,
        mdiOfficeBuildingOutline , mdiCheckCircleOutline , mdiBriefcaseCheck , mdiSignal, mdiMapMarkerOutline ,mdiFileDocumentOutline ,mdiCommentProcessingOutline ,mdiLinkVariant
      },
      validator : {
        startDateBefore
      }
    }
  }
}
</script>
<style scoped>
.v-item-group{
  min-height: 400px !important;
}
.cycle{
  width: 20px !important;
  height: 20px !important;
  border-radius:50%;
}
.chart {
  height: 20px !important;
}

</style>
