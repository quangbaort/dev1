import store from '@/store'
import { ref, watch, computed } from '@vue/composition-api'
import { lang, parseDate , permissions } from '@/common'
import useConfig from '@/common/useConfig'
const { loading } = useConfig()
import moment from 'moment'
const trans = lang
const date = new Date()
const nextDay = new Date(new Date().getTime() + 5)
const stateLoading = ref(loading)
const userData = computed(() => store.state.user)
const role = computed(() => {
  if(store.state.user.role){
    return store.state.user.role == 1 ? true : false
  }else if(store.state.user.is_super_admin){
    return true
  }
  return false
})
const readOnly = computed(() => {
  if(store.state.user.role){
    return store.state.user.role == 1 ? true : false
  }else if(store.state.user.is_super_admin){
    return true
  }
  return false
})

const treeToList = require('tree-to-list');
// prettier-ignore
const nextMonth = date.getMonth() === 11 ? new Date(date.getFullYear() + 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() + 1, 1)

// prettier-ignore
const prevMonth = date.getMonth() === 11 ? new Date(date.getFullYear() - 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() - 1, 1)
const organization = ref(localStorage.getItem('Organization') ? JSON.parse(localStorage.getItem('Organization')) : null)

export default function useStore() {
  const eventStore = ref([])
  const holidays = ref([])
  const notify = ref([])
  const dialogs = ref({
    addNew : false,
    replyEvent : false,
    resultEvent : false,
    dialog : false,
    detailEvent : false,
    detailMemo : false,
    detailNotify : false,
    printer : false,
  })
  const tableList = ref([])
  const listEvent = ref([])
  const tableColumn = ref([
    {text : trans('app.name') , value : 'a' , sortable : true},
    {text : trans('app.company') , value : 'a' , sortable : true},
  ])
  const listBoard = ref([])
  const tableColumnFolderAdd = ref([
    { text : trans('app.file_name') , value : 'name' , sortable : true, },
    { text : trans('app.file_size'), value : 'size' , sortable : true,},
    {
      text: '',
      value: 'actions',
      align: 'center',
      sortable: false,
    }

  ])
  const tableColumnReplyEvent = ref([
    { text : '' , value : 'icon' , sortable : true},
    { text : trans('app.file_name') , value : 'a' , sortable : true},
    { text : trans('app.file_size') , value : 'a' , sortable : true},
  ])
  const tableColumnResultEvent = ref([
    { text : trans('app.response_date_time') , value : 'email' , sortable : true},
    { text : trans('app.response_attendance_absent') , value : 'pivot' , sortable : true},
    { text : trans('app.name') , value : 'name' , sortable : true},
    { text : trans('app.company') , value : 'company.name' , sortable : true},
    {
      text: '',
      value: 'actions',
      align: 'center',
      sortable: false,
    }
  ])
  const listTable = ref([
    { a : 'test 1' }
  ])
  const boardIdArr = ref([])
  const listActionResults = ref([
    { title : trans('app.attendance') , color : 'success' , value : 1},
    { title : trans('app.on_hold') , color : 'warning', value : 3},
    { title : trans('app.absent') , color : 'error', value : 2}

  ])
  // get api department

  const upsert = (array, element) =>{
    const i = array.findIndex(_element => _element.id === element.id);
    if (i > -1) array[i] = element; // (2)
    else array.push(element);
  }
  const itemHoliday = ref({})

  const listDepart = ref([])
  const memos = ref([])
  const state = ref(true)
  const getHoliday =  (start = null, end = null, departments = null) => {
    // if(!store.state.loading){
      store.commit('TOGGLE_LOADING', true)
      store
      .dispatch('calendar/getHoliday' , {
        all : true,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        start : moment(start).format('YYYY-MM-DD HH:mm'),
        end : moment(end).format('YYYY-MM-DD HH:mm'),
      })
      .then(response => {
        // role.value = store.state.role != 5 || userData.value.is_super_admin ? true : false
        // readOnly.value = store.state.role == 5 ? true : false
        const { data }  =  response.data
        const regex = /[\u3000-\u303F]|[\u3040-\u309F]|[\u30A0-\u30FF]|[\uFF00-\uFFEF]|[\u4E00-\u9FAF]|[\u2605-\u2606]|[\u2190-\u2195]|\u203B/g;

        data.forEach(item => {
          if (regex.test(item.name)) {
            item.name = item.name.length > 4 ? item.name.slice(0, 4) + '...' : item.name
          } else {
            item.name = item.name.length > 8 ? item.name.slice(0, 8) + '...' : item.name
          }
        })
        itemHoliday.value = data
        holidays.value = data.map(item => {
          return item.date
        })
        store.dispatch('calendar/getMemo' , {
          organization_id : JSON.parse(localStorage.getItem('Organization')).id,
          all : true,
          start : moment(start).format('YYYY-MM-DD HH:mm:ss'),
          end : moment(end).format('YYYY-MM-DD HH:mm:ss'),
        })
        .then(response => {
            const { data }  =  response.data
            data.forEach(event => {
              event.type = 'memo'
              event.start = moment(event.start).format('YYYY-MM-DD HH:mm:ss')
              event.end = moment(event.end).format('YYYY-MM-DD HH:mm:ss')
            })
            memos.value = data
            if(listBoard.value.length <= 0 && listDepart.value <= 0){
              store.dispatch('calendar/getTree', {
                organization_id : JSON.parse(localStorage.getItem('Organization')).id,
                response : 'tree',
                all : true,
              }).then(response => {
                const { data }  =  response
                listBoard.value =  data
                boardIdArr.value = treeToList.default(data, 'children').map(item => item.id)
                listDepart.value = treeToList.default(data, 'children')
                if(boardIdArr.value.length > 0){
                  getNotify(boardIdArr.value, start,end)
                }
              })
            }else {
              if(departments){
                getNotify(departments,start,end)
              }else {
                getNotify(boardIdArr.value,start,end)
              }
            }
          })
      })
  }
  // get notify api
  const getNotify = ( boardId = [], start = null, end = null) => {
    if(organization && organization.value.news_enabled || role.value){
      store.dispatch('notify/fetchAll' , {
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        board_id : boardId,
        all : true,
        start : moment(start).format('YYYY-MM-DD HH:mm:ss'),
        end : moment(end).format('YYYY-MM-DD HH:mm:ss'),
      }).
      then(response => {
        const { data }  =  response.data
        data.forEach(event => {
          event.type = 'notify'
          event.start = moment(event.start).format('YYYY-MM-DD HH:mm:ss')
          event.end = moment(event.end).format('YYYY-MM-DD HH:mm:ss')
        })
        notify.value = data
        arrEvent()
      })
      if(organization && organization.value.calendar_enabled || role.value){
        store.dispatch('calendar/getEvent' , {
          organization_id : JSON.parse(localStorage.getItem('Organization')).id,
          all : true,
          board_id : boardId,
          start : moment(start).format('YYYY-MM-DD HH:mm:ss'),
          end : moment(end).format('YYYY-MM-DD HH:mm:ss'),
        }).
        then(response => {
          const { data }  =  response.data
          data.forEach(event => {
            event.type = 'event'
            event.start = moment(event.start).format('YYYY-MM-DD HH:mm')
            event.end = moment(event.end).format('YYYY-MM-DD HH:mm')
          })
          listEvent.value = data
          store.commit('setEventList', data)
          arrEvent()
          eventStore.value = [...memos.value ,...listEvent.value , ...notify.value]
          store.commit('TOGGLE_LOADING', false)

        })
      }
      return
    }
  }
  const EventStore = computed(() =>store.state.eventList)
  const arrEvent = () => {
    eventStore.value = [...memos.value ,...listEvent.value , ...notify.value]
  }
  const textDate = ref({
    startDateTime : trans('app.start_date_time'),
    endDateTime : trans('app.end_date_time'),
    startTime : trans('app.start_time'),
    endTime : trans('app.end_time'),
    response : trans('app.response_deadline'),
    startDate : trans('app.start_date'),
    endDate : trans('app.end_date'),
    responseDeadline : trans('app.response_deadline'),
  })


  return {role,state,listDepart, boardIdArr, tableList,textDate,getHoliday,holidays,stateLoading,readOnly,getNotify,listEvent,memos,notify,arrEvent,
    eventStore,dialogs,listBoard,tableColumn,tableColumnFolderAdd,tableColumnReplyEvent,tableColumnResultEvent,listTable, listActionResults,itemHoliday
  }
}
