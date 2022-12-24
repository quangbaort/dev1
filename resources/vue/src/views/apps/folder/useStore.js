import store from '@/store'
import { ref } from '@vue/composition-api'
import { lang, itemPerPage } from '@/common'
import {
  mdiDeleteOutline,
  mdiExportVariant
} from '@mdi/js'
const trans = lang
export default function useStore() {
  const listFolder = ref([])
  const itemFolder = ref([
  ])
  const tableColumnsCalendar = [
    { text: 'No', value: 'no', sortable: false, align: "center pl-1 pr-1", width: '4%'},
    { text: trans('app.event_name'), value: 'name', sortable: true, width: '36%' },
    { text: trans('app.heading'), value: 'title', sortable: true, width: '30%' },
    { text: trans('app.event_date'), value: 'start', align: "right", sortable: true, width: '30%' },
  ]
  const tableColumnsNotify = [
    { text: 'No', value: 'no', sortable: false, align: "center pl-1 pr-1", width: '4%'},
    { text: trans('app.title'), value: 'name', sortable: true, width: '36%' },
    { text: trans('app.heading'), value: 'title', sortable: true, width: '30%' },
    { text: trans('app.update_date'), value: 'updated_at', align: "right", sortable: true, width: '30%' },
  ]
  const tableColumns = ref([
    { text: 'No', value: 'no', sortable: false, width: '4%', align: "center pl-1 pr-1" },
    { text: trans('app.file_name'), value: 'title', sortable: true, width: '36%' },
    { text: trans('app.registrant'), value: 'creator.name', sortable: false, width: '30%' },
    { text: trans('app.register_date'), value: 'created_at', align: "right", sortable: true, width: '20%' },
    {
      text: trans('app.delete'),
      value: 'actions',
      align: 'right',
      sortable: false,
      width: '10%'
    },
  ])
  const paginate = ref({})
  const dialogs = ref({
    dialog: false,
  });
  const getFolders = (type = 'event') => {
    itemFolder.value = []
    store.commit('TOGGLE_LOADING', true);
    store.dispatch('fileManger/getFolders', {
      type: type,
    }).then(res => {
      getFileList(1, type)
      store.commit('TOGGLE_LOADING', false);
      listFolder.value = res.data.data;
    });
  }
  const perPage = ref(itemPerPage[0])
  const idFile = ref(0)
  const getFileList = (page = 1, nameFolder = null, path = null, title = '', keyword = '', sortBy = '') => {
    if (nameFolder) {
      store.commit('TOGGLE_LOADING', true);
      let queryParams = {
        limit: paginate.value.itemsPerPage ?? perPage.value,
        page: page,
        type: nameFolder,
        keyword: keyword,
        title: title,
        sort : sortBy,
      }
      if (path) {
        queryParams = {
          limit: paginate.value.itemsPerPage ?? perPage.value,
          page: page,
          type: nameFolder,
          path: path,
          keyword: keyword,
          title: title,
          sort: sortBy
        }
      }
      store.dispatch('fileManger/getFiles', queryParams).then(res => {
        store.commit('TOGGLE_LOADING', false);
        const { data, meta } = res.data;
        let idFile = 0
        if(meta.current_page > 1){
          idFile = (meta.current_page - 1) * meta.per_page
        }
        data.forEach(item => {
          idFile++;
          item.no = idFile;
        })
        paginate.value = {
          total: meta.total,
          currentPage: parseInt(meta.current_page),
          itemsPerPage: parseInt(meta.per_page),
          pageStart: meta.from,
          pageStop: meta.last_page,
          pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength: meta.last_page
        }
        itemFolder.value = data;
      });
    }
  }
  const changeItemPerPage = (limit = 5, nameFolder = null, path = null, title = '', keyword = '') => {
    if (nameFolder) {
      store.commit('TOGGLE_LOADING', true);
      let queryParams = {
        limit: limit,
        page: 1,
        type: nameFolder,
        keyword: keyword,
        title: title
      }
      if (path) {
        queryParams = {
          limit: paginate.value.itemsPerPage ?? perPage.value,
          page: paginate.value.currentPage ?? page,
          type: nameFolder,
          path: path,
          keyword: keyword,
          title: title
        }
      }
      store.dispatch('fileManger/getFiles', queryParams).then(res => {
        store.commit('TOGGLE_LOADING', false);
        const { data, meta } = res.data;
        let idFile = 0;
        data.forEach(item => {
          idFile++;
          item.no = idFile;
        })
        paginate.value = {
          total: meta.total,
          currentPage: parseInt(meta.current_page),
          itemsPerPage: parseInt(meta.per_page),
          pageStart: meta.from,
          pageStop: meta.last_page,
          pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength: meta.last_page
        }
        itemFolder.value = data;
      });
    }
  }
  const textNew = trans('app.new')
  const truncateFileName = (value, size) => {
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
  return {
    getFolders, getFileList, paginate, changeItemPerPage, truncateFileName, countLength,
    listFolder, tableColumnsCalendar, itemFolder, tableColumns, tableColumnsNotify, dialogs, textNew
  }
}
