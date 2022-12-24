<template>
  <v-dialog v-model="dialogs.printer" max-width="800" persistent scrollable>
    <v-card class="printer">
      <v-card-text>
<div id="printf">
        <div class="printer_box">
          <h2 style="margin: 5px;">{{organizationActive.name}}</h2>
          <h3 style="background: #84c7ff; border-top: 1px solid; border-left:1px solid; border-right:1px solid; padding: 5px; color:#000 !important;">行事詳細</h3>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">行事名</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.title }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">担当組織</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.department_name }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">開催日時</span>
            </div>
            <div class="printer_item_content">
              <span>{{ start + ' ~ ' + end }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">開催場所</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.place }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">登録日</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.created_at }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">登録者</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.created_by }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">更新者</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.updated_by }}</span>
            </div>
          </div>
          <div class="printer_item">
            <div class="printer_item_title">
              <span class="font-weight-bold">内容</span>
            </div>
            <div class="printer_item_content">
              <span>{{ event.detail }}</span>
            </div>
          </div>
        </div>
        <div class="" style="margin: 10px 0">
          <table style="width: 100%;">
            <thead>
              <tr>
                <th colspan="6">
                  <h3 style="color:#000 !important;">参加状況一覧</h3>
                </th>
              </tr>
              <tr>
                <th>
                  会社
                </th>
                <th>
                  名前
                </th>
                <th>
                  出席
                </th>
                <th>
                  欠席
                </th>
                <th>
                  保留
                </th>
                <th>
                  回答日時
                </th>
              </tr>
            </thead>
            <tbody>

              <tr v-for="user in result.users">
                <td>
                  {{ user.company.name }}
                </td>
                <td>
                {{ user.name }}
                </td>
                <td>
                  <span v-if="user.pivot.answer == 1">〇</span>
                </td>
                <td>
                  <span v-if="user.pivot.answer == 2">〇</span>
                </td>
                <td>
                  <span v-if="user.pivot.answer == 3">〇</span>
                </td>
                <td>
                  {{ user.pivot.answered_at }}
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  各合計人数
                </td>
                <td>
                  {{ attendance }}
                </td>
                <td>{{ absence }}</td>
                <td>{{ pending }}</td>
                <td></td>
              </tr>
              <tr>
                <td colspan="2">
                  合計人数
                </td>
                <td colspan="3">
                  {{ total }}
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      </v-card-text>

      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="">
          <v-col cols="12" md="12" class="align-self-center justify-end">
            <v-row class="justify-end align-self-center py-3" style="align-self: center !important;">
              <v-btn color="default" class="mx-2" @click="dialogs.printer = false">{{this.$trans('app.close')}}</v-btn>
              <v-btn color="success" type="button" v-print="printObj"class="mx-2">印刷</v-btn>
            </v-row>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import moment from 'moment'
import Vue from 'vue'
import "./Printer.css"
import Print from 'vue-print-nb'
import { organizationActive } from '@/common'
import store from '@/store'
Vue.use(Print);

directives: {
  print
}
export default {
  props:{
    dialogs: {
      type: Object,
      default: () => ({
        Printer: false
      })
    },
    event:{
      type: Object,
      default: () => ({})
    },
    result:{
      type: Object,
      default: () => ({})
    },
  },
  data() {
    return {
      start : null,
      end : null,
      printObj: {
        id: "printf",
        previewTitle: '',
        popTitle: '',
        printTitle: '',
        beforeOpenCallback (vue) {
          store.commit('TOGGLE_LOADING', true)
        },
        openCallback (vue) {
          store.commit('TOGGLE_LOADING', false)
        },
      },
      attendance: null,
      absence: null,
      pending: null,
      total: null,
    }
  },
  watch: {
    event(value){
      if(value){
        if(value.is_allday){
          this.start = moment(value.start).locale('ja').format('YYYY年MM月DD日 (ddd)');
          this.end = moment(value.end).locale('ja').format('YYYY年MM月DD日 (ddd)')
        }else {
          this.start = moment(value.start).locale('ja').format('YYYY年MM月DD日 (ddd) HH:mm');
          this.end = moment(value.end).locale('ja').format('YYYY年MM月DD日 (ddd) HH:mm')
        }
        value.created_at = moment(value.created_at).locale('ja').format('YYYY/MM/DD');
        value.user.map(user =>
          user.pivot.answered_at = moment(user.pivot.answered_at).locale('ja').format('YYYY/MM/DD HH:mm')
        )
        this.event = value;
      }
    },
    result(value){
      if(value){
        this.attendance = value.users.filter(event => event.pivot.answer == 1).length;
        this.absence = value.users.filter(event => event.pivot.answer == 2).length;
        this.pending = value.users.filter(event => event.pivot.answer == 3).length;
        this.total = value.users.length;
        this.result = value;
      }
    }
  },
  methods: {
    printer(){
      this.$htmlToPaper('printf');

    }
  },
  setup(){
    return {
      organizationActive
    }
  }
}
</script>

<style scoped>
  * {
    /* color-adjust: exact!important; */
    -webkit-print-color-adjust: exact!important;
     /* print-color-adjust: exact!important; */
  }

.v-card.printer{
    border-radius: none !important;
  }
  table, th, td {
   border-style:solid;
  border-color: #000;
  border-collapse: collapse;
  border-width: 1px;
}
  table{
    border-collapse: collapse;
    width: 100%;
  }
  table th:first-child{
    border-left: none;
    padding: 0;
  }
  table th {
    text-align: center;
    padding: 5px 0;
    color: #000;
  }
  table tr td {
    padding: 5px 0;
    text-align: center;
    color: #000;
  }
  .printer #printf .printer_box h3{
    border-left: 1px solid #000;
    border-top: 1px solid #000;
    border-right: 1px solid #000;
    text-align: left;
    color: #000 !important;
    padding : 5px;
    background: #84c7ff;

  }
  table thead th h3 {
    background: #84c7ff;
    padding : 5px;
    color: #000 !important;
    text-align: left;
  }

  .printer_item{
    display: flex;
    color: #000;
    /* font-weight: bold; */
  }
  .printer_item:last-child{
    /* border-bottom: 1px solid #000; */
  }
  .printer_item_title{
    /* border-right: 1px solid #000; */
    text-align: center;
    background: #D9D9D9;
    border-bottom: none;
  }
  .printer_item_content{
    /* border-right: 1px solid #000; */
    border: 1px solid #000;
    border-left: none;
  }

  .printer_item_title,.printer_item_content{
    padding: 10px;
    width: 100%;
    border-bottom: none;
  }
  .printer_item_title{
    width: 30%;
    border: 1px solid #000;
    border-bottom: none;

  }
  .printer_item:last-child .printer_item_title, .printer_item:last-child .printer_item_content{
    border-bottom: 1px solid #000;
  }
  th{
    background: #D9D9D9;
  }
  th:first-child{
    width: 24%;
  }

</style>
