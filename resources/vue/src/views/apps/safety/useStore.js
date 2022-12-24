import store from "@/store";
import { ref, watch } from "@vue/composition-api";
import { lang, itemPerPage } from "@/common";
import { mdiDeleteOutline } from "@mdi/js";
import moment from "moment";
const trans = lang;
const treeToList = require('tree-to-list');
export default function useStore() {
  /** Setup columns table*/
  const tableColumns = [
    { text: trans("app.support_required"), value: "alert", sortable: false },
    { text: trans("app.notified_at"), value: "notified_at", sortable: false },
    { text: trans("app.title"), value: "title", sortable: true },
    { text: trans("app.branch_committee"), value: "department", sortable: false },
    { text: trans("app.answer"), value: "answer", sortable: true },
    { text: trans("app.response_rate"), value: "percent", sortable: false },
    { text: "", value: "actions", align: "center", sortable: false }
  ];



  /** Setup answer list */
  const answerList = {
    safe: trans("app.safe"),
    spr: trans("app.support_required"),
  };
  /** Setup list actions safety*/
  const listActionSafety = ref({
    during: trans("app.during_response"),
    acp: trans("app.accepted")
  });
  const loading = ref(false); /** Setup loading */
  const perPage = ref(itemPerPage[0]); /** Setup perPage */
  const paginate = ref({}); /** Setup paginate*/
  const titleSearch = ref(null); /** Setup title search */
  const departmentSearch = ref(null); /** Setup department search */
  /** Setup textDate *//** Setup textDate */
  const textDate = ref({
    startDate: trans("app.start_date"),
    endDate: trans("app.end_date")
  });
  const userData = JSON.parse(localStorage.getItem('userData'))
  const listTable = ref([]); /** Setup listTable */
  const admin = ref(store.state.user.role == 1 ? true : false)
  /** Setup queryParams */
  const queryParams = ref({});
  const column = ref('');
  /** Get list Safety */
  const listSafeties = (page = 1, limit = 10) => {
    store.commit("TOGGLE_LOADING", true);
    loading.value = true
    queryParams.value.limit = paginate.value.itemsPerPage ?? perPage.value,
    queryParams.value.page = paginate.value.currentPage ?? page,

    store
      .dispatch('safeties/fetchAll', queryParams.value)
      .then(response => {
        admin.value = store.state.user.role != 5|| userData.is_super_admin ? true : false
        store.commit("TOGGLE_LOADING", false);
        const { data, meta } = response.data
        data.filter(item => {
          item.notified_at = item.notify_at ? item.notify_at : moment(Date.now()).format('YYYY/MM/DD');
          item.department = item.department ? item.department.name : "";
        })
        listTable.value = data
        paginate.value = {
          total: meta.total,
          currentPage: parseInt(meta.current_page),
          itemsPerPage: parseInt(meta.per_page),
          pageStart: meta.from,
          pageStop: meta.last_page,
          pageCount: parseInt(Math.ceil(meta.total / meta.per_page)),
          itemsLength: meta.last_page
        }
        loading.value = false
        return getAllDepartments()
      })
  }
  const changeItemPerPage = (limit = 5) => {
    store
      .dispatch("safeties/fetchAll", {
        limit: limit,
        page: paginate.value.currentPage
      })
      .then(response => {
        const data = response.data;
        listTable.value = data.data;
        paginate.value = {
          total: data.meta.total,
          currentPage: parseInt(data.meta.current_page),
          itemsPerPage: parseInt(data.meta.per_page),
          pageStart: data.meta.from,
          pageStop: data.meta.last_page,
          pageCount: parseInt(Math.ceil(data.meta.total / data.meta.per_page)),
          itemsLength: data.meta.last_page
        };
        loading.value = false;
      });
  };

  const listAction = ref({
    delete: trans("app.delete"),
    detail: trans("app.detail"),
    toAnswers: trans("app.to_answer"),
    resultAnswers: trans("app.result_answer"),
  });

  const listHandel = [{ title: trans("app.delete"), value: "delete", icon: mdiDeleteOutline }];
  const dialogs = ref({
    dialog: false,
    addNew: false,
    detail: false,
    answer: false,
    resultAnswer: false,
    comment: false,
  });

  const listBoard = ref([])
  const getAllDepartments = () =>{
    store
    .dispatch('safeties/getAllDepartments', {
      organization_id: JSON.parse(localStorage.getItem('Organization')).id
    })
      .then(response => {
        const { data , meta } = response
        listBoard.value = treeToList.default(data, 'children')
        loading.value = false
      })
  }

  return {
    listBoard,
    column,
    getAllDepartments,
    listActionSafety,
    listHandel,
    listTable,
    tableColumns,
    loading,
    listSafeties,
    paginate,
    perPage,
    titleSearch,
    departmentSearch,
    changeItemPerPage,
    listAction,
    answerList,
    dialogs,
    admin,
    queryParams
  };
}
