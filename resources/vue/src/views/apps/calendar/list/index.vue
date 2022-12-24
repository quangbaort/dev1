<template>
  <div style="height: 100%; width: 100%;" id='list__event'>
    <perfect-scrollbar
      ref="scrollbar"
      :options="perfectScrollbarSettings"
      id="event-list"
    >
    <table style="width: 100%; border: 1px solid #ccc;" >
      <tbody ref="table" v-if="dateArr.length > 0">
        <tr v-for="(date, i) in dateArr" :key="i" :id="date.fullDate == now ? 'now' : ''" >
          <td style="width: 22%;">
            <div :class="date.fullDate == now ? 'row ml--5' : 'row ml-1'"  style="padding-right:20px">
                <div class="col-md-4 col-12">
                  <span :id="date.fullDate == now ? 'dateNow' : ''" >{{ date.date }}</span>
                </div>
              <span class="col-md-8 col-12 text-right align-self-center">{{  date.monthJP }}</span> </p>
            </div>
          </td>
          <td  class="" >
            <div class="row list__item" v-for="(event, i) in date.events" :key="i">
              <div class="col-5 align-self-center">
                <span :style="{width: '10px', height: '10px', display: 'inline-block', borderRadius: '100%', backgroundColor: event.theme_color}"></span>
                <span> {{ time(date.fullDate1, event) }} </span>
              </div>
              <div class="col-6 align-self-center">
                {{ event.title }}
              </div>
              <div  class="text-right col-1">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                      <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item link @click="clickAction('can_answer', event)" v-if="event.can_answer">
                      <v-list-item-title >
                        <v-icon >
                          {{ icons.mdiCellphoneMessage }}
                        </v-icon>
                        <span>{{ linkAction.answer }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('download', event)">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiCalendarPlus }}
                        </v-icon>
                        <span>{{ linkAction.isc }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('result', event)" v-if="admin">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiSignal }}
                        </v-icon>
                        <span>{{ linkAction.result }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('detail', event)" v-if="admin">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiFileDocumentOutline }}
                        </v-icon>
                        <span>{{ linkAction.detail }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('duplicate', event)" v-if="admin">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiContentDuplicate }}
                        </v-icon>
                        <span> {{ linkAction.duplicate }}</span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('printer', event)" v-if="admin">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiPrinter }}
                        </v-icon>
                        <span> 印刷 </span>
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="clickAction('delete', event)" v-if="admin">
                      <v-list-item-title>
                        <v-icon >
                          {{ icons.mdiDeleteOutline }}
                        </v-icon>
                        <span>{{ linkAction.delete }}</span>
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </div>

          </td>

        </tr>
      </tbody>
      <p v-else class="text-center py-3"> {{ this.$trans('app.no_event') }}</p>
    </table>
    </perfect-scrollbar>
  </div>
</template>

<script>
import moment from "moment";
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import store from '@/store'
import { ref, watch, computed } from '@vue/composition-api'
import { mdiDotsVertical,mdiCellphoneMessage,mdiCalendarPlus,mdiSignal,mdiFileDocumentOutline,mdiContentDuplicate,mdiDeleteOutline,mdiPrinter } from '@mdi/js';
export default {
  props: {
    admin: {
      type: Boolean,
      default: false
    },
    start: {
      type: String,
      default: () => moment().format('YYYY-MM-DD')
    },
    end: {
      type: String,
      default: () => moment().add(1, 'month').format('YYYY-MM-DD')
    },
  },
  components: {
    PerfectScrollbar,
  },
  data(){
    return {
      scrollbar: null,
     perfectScrollbarSettings : {
        maxScrollbarLength: 60,
        wheelPropagation: false,
        wheelSpeed: 0.7,
       suppressScrollX: true,
     },
      isNow : '',
      dataEvent: {},
      now : new Date().toLocaleTimeString('ja-JP', {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      }).split(' ')[0],
      dateArr : [],
      linkAction : {
        answer: trans('app.event_answer_reference'),
        isc: trans('app.register_in_my_calendar'),
        result: trans('app.result_response'),
        detail: trans('app.detail'),
        duplicate: trans('app.duplicate'),
        delete: trans('app.delete')
      },
      dateBetween: this.getDatesInRange(new Date(this.start), new Date(this.end))
    }
  },
  created() {
    this.scrollbar = this.$refs.scrollbar
    this.getEvent(this.events)
    setTimeout(() => {
      this.$nextTick(() => {
        this.$refs.scrollbar.update()
        this.scrollToElement()
      });
    }, 500);
  },
  methods: {
    scrollToElement(){
      const container = this.scrollbar;
      let findNow = container.$slots.default[0].children[0].elm.children
      for(let i = 0; i < findNow.length; i++){
        if(findNow[i].getAttribute('id') == 'now'){
          let height = findNow[i].offsetTop
          return this.$refs.scrollbar.$el.scrollTop = height;
        }
      }
    },
    getEvent(event){
      if(event && event.length > 0){
        this.dataEvent =  event.map(item => {
          item.fullDate = moment(item.start).format('YYYY/MM/DD');
          item.fullDate1 = moment(item.start).format('YYYY-MM-DD');
          item.date = moment(item.start).format("YYYY-MM-DD").split("-")[2];
          item.monthJP = moment(item.start).locale('ja').format('MM月、dd');
          item.month = moment(item.start).format("YYYY-MM-DD").split("-")[1];
          item.year = moment(item.start).format("YYYY-MM-DD").split("-")[0];
          item.timeStart = moment(item.start).format("HH:mm");
          item.timeEnd = moment(item.end).format("HH:mm");
          item.between = this.getDatesInRange(new Date(item.start), new Date(item.end))
          return item;
        })
        let mySet = this.dataEvent.map(event => {
          return  event.between.map(item => {
            return {
              date: item.getDate().toString(),
              month: moment(item).locale('ja').format('MM'),
              monthJP: moment(item).locale('ja').format('MM月、dd'),
              year: item.getFullYear().toString(),
              fullDate: moment(item).format('YYYY/MM/DD'),
              fullDate1 : moment(item).format('YYYY-MM-DD'),
              events : this.dataEvent.filter(event => {
                let date = event.between.map(item => {
                  return moment(item).locale('ja').format('YYYY/MM/DD')
                })
                return date.includes(moment(item).locale('ja').format('YYYY/MM/DD'))
              })
            }
          })
        });
        mySet = [].concat.apply([], mySet)
        this.dateArr = this.getUnique(mySet, 'fullDate').sort((a, b) => {
            return new Date(a.fullDate) - new Date(b.fullDate)
        })
      } else {
        this.dateArr = []
      }
    },
    time(date, event){
      if(event.is_allday){
        return '終日'
      }
      if(date == event.start.split(' ')[0]){
        return event.timeStart + ' - ' + '23:59'
      }else if(date == event.end.split(' ')[0]){
        return '00:00' + ' - ' + event.timeEnd
      }else{
        return '00:00' + ' - ' + '23:59'
      }
    },
    getDatesInRange(startDate, endDate) {
      const date = new Date(startDate.getTime());

      const dates = [];

      while (date <= endDate) {
        dates.push(new Date(date));
        date.setDate(date.getDate() + 1);
      }

      return dates;
    },
    getUnique(arr, comp) {

      const unique = arr
        .map(e => e[comp])

        // store the keys of the unique objects
        .map((e, i, final) => final.indexOf(e) === i && i)

        // eliminate the dead keys & store unique objects
        .filter(e => arr[e]).map(e => arr[e]);

      return unique;
    },
    clickAction(action, event){
      var result = new Promise((resolve) => this.$emit(action, event, resolve))

      result.then((value) => console.log(value))

      return result
    }
  },
  watch:{
    events(event){
      this.getEvent(event);
    }
  },

  mounted(){
    this.scrollbar = this.$refs.scrollbar;
    setTimeout(() => {
      this.$nextTick(() => {
        this.$refs.scrollbar.update()
        this.scrollToElement()
      });
    }, 10);
  },
  setup(props, context) {
    const events = computed(() => store.state.eventList);
    return {
      events,
      icons: {
        mdiDotsVertical,
        mdiCellphoneMessage,
        mdiCalendarPlus,
        mdiSignal,
        mdiFileDocumentOutline,
        mdiContentDuplicate,
        mdiDeleteOutline,
        mdiPrinter
      }
    }
  }
}
</script>
<style scoped>
table {
  border-collapse: collapse;
}
.ps {
  overflow-x: hidden !important;
  width: 100%;
  height: 100%;
}
tr {
  border-bottom: 1pt solid #ccc;
  display: table-row;
}
td {
  border-right: 1px solid #ccc;
}
td:nth-child(2) {
  border-right: none;
}
.v-menu__content {
  border-radius: 15px;
}
#event-list .ps--active-x > .ps__rail-x{
  display: none !important;
  opacity: 0 !important;
}
.v-list-item:hover{
  background: #f1f1f1 !important;
}
#dateNow {
    align-items: center;
    align-self: center;
    background: #30529e;
    border-radius: 50%;
    color: #fff;
    display: inline-block!important;
    height: 35px;
    padding: 8px 0;
    text-align: center;
    width: 35px;
    font-weight: 600;

}
.ml--5{
  margin-left: -6px;
}
.list__item{
  margin: revert !important;
}
.list__item:hover{
  background: #eee !important;

}

</style>
