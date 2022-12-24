// eslint-disable-next-line object-curly-newline
import {
  mdiCityVariantOutline,
  mdiHistory
} from '@mdi/js'
import { role } from '@/common'

const otherPage = [
  {
    subheader: trans('app.menu_admin'),
    role : [1],
    function : 'other',
    type : 'divider'
  },
  {
    title: trans('app.organization'),
    icon: mdiCityVariantOutline,
    to : 'apps-organizations',
    role : [1],
    function : 'Organization',
    type : 'link'
  },
  {
    title: trans('app.log'),
    icon: mdiHistory,
    to : 'apps-log-list',
    role : [1],
    function : 'Logs',
    type : 'link'
  }
]

export default otherPage

