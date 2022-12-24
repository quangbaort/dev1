import {
  mdiHomeOutline ,
  mdiBellOutline ,
  mdiCalendarMonthOutline,
  mdiAccountQuestionOutline,
  mdiFolderOpenOutline
} from '@mdi/js'
import {computed, ref} from "@vue/composition-api";
import axios from '@axios';

const countNotify =
  JSON.parse(localStorage.getItem('totalUnansweredNotify')) !== null ?
    JSON.parse(localStorage.getItem('totalUnansweredNotify')) : {
      news  : 0,
      event : 0,
      safetyCheck  : 0
    }

const userData = localStorage.getItem('userData')  ? JSON.parse(localStorage.getItem('userData')) : null;
if(userData){
  axios.get('/total-unanswered-notify').then(res => {
    localStorage.setItem('totalUnansweredNotify', JSON.stringify(res.data.count))
  })
}
export default [
  {
    title: trans('app.notify'),
    icon: mdiBellOutline,
    badge: countNotify != null && countNotify.news != 0 ? countNotify.news.toString() : null ,
    badgeColor: 'warning',
    to: 'apps-notify',
    function : 'Notify',
    type : 'link'
  },
  {
    title : trans('app.event'),
    icon : mdiCalendarMonthOutline,
    badge:  countNotify != null && countNotify.event != 0  ? countNotify.event.toString() : null,
    badgeColor: 'warning',
    to : "apps-calendar",
    function : 'Calendar',
    type : 'link'
  },
  {
    title : trans('app.safe_confirm'),
    icon : mdiAccountQuestionOutline,
    badge: countNotify != null &&  countNotify.safetyCheck != 0 ?  countNotify.safetyCheck.toString() : null,
    badgeColor: 'warning',
    to : "apps-safety",
    function : 'Safety',
    type : 'link'
  },
  {
    title : trans('app.archive'),
    icon : mdiFolderOpenOutline,
    to : "apps-folder",
    function : 'Folder',
    type : 'link'
  }

]
