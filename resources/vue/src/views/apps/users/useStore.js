import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang, itemPerPage } from '@/common'
const trans = lang
import { address } from '@/common/address'
export default function useStore() {
    const listBoard = ref([])
    const listTable = ref([])
    const roles = ref([])
    const tableColumns = [
        { text: trans('app.belong'), value: 'departments', sortable: false },
        { text: trans('app.name'), value: 'name', sortable: true },
        { text: trans('app.company_name'), value: 'company', sortable: false },
        { text: trans('app.prefectures'), value: 'prefecture', sortable: false },
        { text: trans('app.municipalities'), value: 'city', sortable: false },
        {
            text: '',
            value: 'actions',
            align: 'right',
            sortable: false,
        },
    ]
    const tableColumnsCompanies = [
        { text: trans('app.company_name'), value: 'organization', sortable: true },
        { text: trans('app.prefectures'), value: 'belong', sortable: true },
        { text: trans('app.municipalities'), value: 'company_name', sortable: true },
        { text: trans('app.tel'), value: 'prefectures', sortable: true },
    ]
    const loading = ref(false)
    const perPage = ref(itemPerPage[0])
    const paginate = ref({})
    const nameOrganization = ref(null)
    const prefecture = ref(null)
    const city = ref(null)
    const company = ref(null)
    const organization = ref(null)
    const prefectureArr = address
    const isDisabled = ref(false)
    const nameDepartment = ref(null)
    const checkCount = () => {
        store
            .dispatch('users/checkCountUser', {
                organization_id: JSON.parse(localStorage.getItem('Organization')).id ?? null,
            }).then((response) => {
                const { disabled } = response.data
                isDisabled.value = disabled
            }).catch(error => {
                isDisabled.value = true
            })
    }
    const sortBy = ref('')
    const fetchAll = (page = 1) => {
        loading.value = true
        store.commit('TOGGLE_LOADING', true)
        store
            .dispatch('users/fetchAll', {
                limit: paginate.value.itemsPerPage ?? perPage.value,
                page: paginate.value.currentPage ?? page,
                name: nameOrganization.value,
                prefecture: prefecture.value,
                city: city.value,
                department: nameDepartment.value,
                company: company.value,
                organization_id: JSON.parse(localStorage.getItem('Organization')).id ?? null,
                sort: sortBy.value,
            })
            .then(response => {
                const { data, meta } = response.data
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
                store.commit('TOGGLE_LOADING', false)
            })
            .catch(error => {
                loading.value = false
            })
    }
    const changeItemPerPage = (limit = 5) => {
        loading.value = true
        store
            .dispatch('users/fetchAll', {
                limit: limit,
                page: paginate.value.currentPage ?? page,
                name: nameOrganization.value,
                prefecture: prefecture.value,
                department: nameDepartment.value,
                city: city.value,
                company: company.value,
                organization_id: JSON.parse(localStorage.getItem('Organization')).id ?? null
            })
            .then(response => {
                const { data, meta } = response.data
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
            })
            .catch(error => {
                loading.value = true
            })
    }
    const getTree = () => {
        store.commit('TOGGLE_LOADING', true)
        store
            .dispatch('users/getTree', {
                organization_id: JSON.parse(localStorage.getItem('Organization')).id
            })
            .then(response => {
                listBoard.value = JSON.parse(JSON.stringify(response.data))
                store.commit('TOGGLE_LOADING', false)
            })

    }
    const fetchRoles = () => {
        store.commit('TOGGLE_LOADING', true)
        store
            .dispatch('users/fetchRoles').then(response => {
                const { data } = response.data
                let role = data.filter(item => {
                    return item.key = item.key.toString()
                })
                roles.value = role
                store.commit('TOGGLE_LOADING', false)
            })
    }
    watch: {
        getTree()
        fetchAll()
        checkCount()
        fetchRoles()
    }
    return {
        listBoard,
        tableColumnsCompanies,
        company,
        prefectureArr,
        checkCount,
        isDisabled,
        sortBy,
        roles,
        fetchRoles,
        nameDepartment,
        listTable,
        tableColumns,
        loading,
        fetchAll,
        paginate,
        perPage,
        nameOrganization,
        prefecture,
        city,
        changeItemPerPage,
        organization
    }
}
