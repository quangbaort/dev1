import {
  mdiHumanMaleBoard,
  mdiOfficeBuildingOutline,
  mdiAccountSupervisorOutline,
  mdiEmailAlertOutline,
  mdiCalendarClockOutline,
  mdiTextBoxCheckOutline,
  } from '@mdi/js'

export default [
  {
    subheader: trans('app.menu_setting'),
    role : [1],
    function : 'settings',
    type : 'divider'
  },
  {
    title: trans('app.branch_committee'),
    icon: mdiHumanMaleBoard,
    to: 'apps-board-list',
    role : [1],
    function : 'Board',
    type : 'link'
  },
  {
    title: trans('app.company'),
    icon: mdiOfficeBuildingOutline,
    to: 'apps-company-list',
    role : [1],
    function : 'Company',
    type : 'link'
  },
  {
    title: trans('app.user'),
    icon: mdiAccountSupervisorOutline,
    to: 'apps-user-list',
    role : [1],
    function : 'User',
    type : 'link'
  },
  {
    title: trans('app.notify_destination'),
    icon: mdiEmailAlertOutline,
    to : { name: 'apps-notifyGroup' },
    role : [1],
    function : 'MailingList',
    type : 'link'
  },
  {
    title: trans('app.holiday'),
    icon: mdiCalendarClockOutline,
    to : { name: 'apps-holiday'},
    role : [1],
    function : 'Holiday',
    type : 'link'
  },
  {
    title: trans('app.memo'),
    icon: mdiTextBoxCheckOutline,
    to : { name: 'apps-memo-list' },
    role : [1],
    function : 'Memo',
    type : 'link'
  },
]
