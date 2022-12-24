import store from "@/store";
import { ref, watch } from "@vue/composition-api";
import { lang, itemPerPage } from "@/common";
import { mdiDeleteOutline } from "@mdi/js";
const trans = lang;
export default function useStore() {
    const listHandel = ref([{ title: trans("app.delete"), value: "delete", icon: mdiDeleteOutline }]);
    const textDate = ref({
        startDate: trans("app.start_date"),
        endDate: trans("app.end_date")
    });
    /**Setup data table */
    const tableColumns = ref([
        { text: trans("app.answered_at"), value: "answered_at", sortable: true },
        { text: trans("app.answer"), value: "answer", align: 'center', sortable: false },
        { text: trans("app.name"), value: "name", sortable: false },
        { text: trans("app.company"), value: "company", sortable: false },
        { text: trans("app.response"), value: "response", align: 'center', sortable: false },
        { text: "", value: "actions", align: "center", sortable: false }
    ]);
    const listTable = ref([]);
    const dialogs = ref({
        comment: false
    });

    const listActionSafety = ref({
        during: trans("app.during_response"),
        acp: trans("app.accepted")
    });

    const paginate = ref({});
    const startDate = ref("");
    const endDate = ref("");

    return {
        paginate,
        listHandel,
        listTable,
        dialogs,
        tableColumns,
        textDate,
        listActionSafety
    };
}
