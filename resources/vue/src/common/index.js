require('./lodash-install')
require('./translator')
import {
    mdiDeleteOutline,
    mdiExportVariant
} from '@mdi/js'
import store from '@/store'
import { onUnmounted } from '@vue/composition-api'
export const lang = trans
import moment from "moment"
import { isBuffer } from 'lodash'
export const perPageChoices = [
    { text: '5', value: 5 },
    { text: '10', value: 10 },
    { text: '20', value: 20 },
]
export const maxSizeFile = 20971520;
export const role = () => {
    const userData = JSON.parse(localStorage.getItem('userData')) ?? null
    return userData ? userData.is_super_admin : false
}
export const itemPerPage = [
    10, 20, 50, 100
]
export const listHandel = [
    { title: trans('app.export'), value: 'export', icon: mdiExportVariant },
    { title: trans('app.delete'), value: 'delete', icon: mdiDeleteOutline },
]
export const listAction = {
    result: trans('app.confirm_status'),
    delete: trans('app.delete'),
    detail: trans('app.detail'),
    replyNotify: trans('app.reply_notify')
}
export const asset = (pathFile) => {
    return `/${pathFile}`
}
export const registerModule = (module, nameModule = 'apps') => {
    const APP_STORE_MODULE_NAME = nameModule
        // Register module
    if (!store.hasModule(APP_STORE_MODULE_NAME)) {
        store.registerModule(APP_STORE_MODULE_NAME, module)
    }
    // UnRegister on leave
    onUnmounted(() => {
        store.commit('setModuleName', null)
        if (store.hasModule(APP_STORE_MODULE_NAME)) store.unregisterModule(APP_STORE_MODULE_NAME)
        store.commit('TOGGLE_ALERT', false)
    })
}
export const formatDate = dates => {
    if (!dates) return null
    const [year, month, day] = dates.split('-')

    return `${year}/${month}/${day}`
}
export const formatTimeToDate = dates => {
    if (!dates) return null
    return moment(dates).format("YYYY/MM/DD")
}
export const formatDateInput = dates => {
    if (!dates) return null
    return moment(dates).format("YYYY-MM-DD")
}
export const parseDate = dates => {
    if (!dates) return null
    const [month, day, year] = dates.split('/')

    return `${year}-${month}-${day}`
}
export const textNoData = trans('app.no_data')

export const loadingItem = trans('app.loading_item')
export const organizationActive = JSON.parse(localStorage.getItem('Organization')) ?? null

export const replacePrefecture = (Pref_code, address) => {
    if (!Pref_code) return null
    for (let i = 0; i < address.length; i++) {
        if (Pref_code === address[i].Pref_code) return address[i].Pref_name.toString()
    }
}
export const functionToScreen = (screen_code, screen) => {
        if (!screen_code) return null
        return screen[screen_code]
    }
    // export const permissions = () => {
    //   const user = JSON.parse(localStorage.getItem('userData'))
    //   let permissions = {}
    //   if(user.roles.length > 0){
    //      permissions = user.roles.filter( item =>{
    //       return item.user_id == user.id && item.organization_id == JSON.parse(localStorage.getItem('Organization')).id
    //     });
    //   }
    //   return permissions[0]
    // }
export const bytesToSize = (bytes) => {
    if (!bytes) return ''
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}
export const truncateFileName = (value, size) => {
    let ext = value.split('.').pop()
    if (value && countLength(value) > size) {
        value = value.substring(0, size - 4 - ext.length) + '....' + ext;
    }
    return value
}
const countLength = (str) => {
    let r = 0;
    for (let i = 0; i < str.length; i++) {
        const c = str.charCodeAt(i);
        // Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
        // Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
        if ((c >= 0x0 && c < 0x81) || (c === 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
            r += 1;
        } else {
            r += 2;
        }
    }
    return r;
}

export const deparmentActive = JSON.parse(localStorage.getItem('userData')) && JSON.parse(localStorage.getItem('userData')).departments &&
    JSON.parse(localStorage.getItem('userData')).departments.length > 0 ?
    JSON.parse(localStorage.getItem('userData')).departments.filter(department => {
        return department.organization_id == organizationActive.id
    })[0].id : "";
export const headerProps = {
  sortByText: "並び替え"
}
