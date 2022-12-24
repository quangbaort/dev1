<template>
  <div>
    <v-dialog v-model="dialogs.replyEvent" max-width="800px" persistent scrollable>
      <v-card>
        <v-card-title>
          {{event.startDateTime}}　{{event.title}} の出欠確認
        </v-card-title>
        <v-tabs v-model="currentTab" icons-and-text grow>
          <v-tab>
            {{ this.$trans('app.basic_information') }}
            <v-icon>
              {{ icons.mdiFileDocumentEditOutline }}
            </v-icon>
          </v-tab>
          <v-tab>
            {{ this.$trans('app.place') }}
            <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
          </v-tab>
          <v-tab>
            {{ this.$trans('app.detail_information') }}
            <v-icon>
                {{ icons.mdiFileDocumentOutline }}
            </v-icon>
          </v-tab>
          <v-tab>
            {{ this.$trans('app.file') }}
            <v-icon>
              {{ icons.mdiFolderOutline }}
            </v-icon>
          </v-tab>
        </v-tabs>
        <v-card-text>
          <v-tabs-items v-model="currentTab">
            <v-tab-item class="pt-6 px-8" :transition="false">
              <div class="">
                <v-row class="">
                  <v-col md="2" cols="2">
                    <h4>{{ this.$trans('app.title') }} : </h4>
                  </v-col>
                  <v-col md="8" cols="8">
                    <span>{{ event.title }}</span>
                  </v-col>
                </v-row>
                <v-row class="">
                  <v-col md="2" cols="2">
                    <h4>{{ this.$trans('app.date_time') }}：</h4>
                  </v-col>
                  <v-col md="4" cols="3">
                    <span>{{ event.startDateTime }}</span>
                  </v-col>
                  <v-col md="1" cols="1"> ~ </v-col>
                  <v-col md="4" cols="3">
                    <span>{{ event.endDateTime }}</span>
                  </v-col>
                </v-row>
              </div>
            </v-tab-item>
            <v-tab-item class="pt-6 px-8" :transition="false" >
              <v-row class="">
                <v-col md="12" cols="12">
                  <h4>{{ event.place }}</h4>
                </v-col>
                <v-col md="12" cols="12">
                  <v-icon>{{ icons.mdiLinkVariant }}</v-icon>
                  <a target="_blank" :href="event.place_url"> {{ event.place_url }}</a>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="px-8 pt-6" :transition="false">
              <v-row>
                <v-col cols="12" md="12">
                  <v-textarea hide-details="auto" :readonly="true" v-model="event.detail" :label="this.$trans('app.detail_content')" auto-grow outlined rows="10" row-height="15"></v-textarea>
                </v-col>
                <v-col cols="12" md="12">
                  <v-icon>{{ icons.mdiLinkVariant }}</v-icon>
                  <a target="_blank" :href="event.detail_url">{{ event.detail_url }}</a>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="pt-6" :transition="false">
              <v-col cols="12" md="12">
                <v-data-table :items="event.files" :headers="tableColumnFolderAdd" :footer-props="{itemsPerPageOptions: itemPerPage , 'items-per-page-text':this.$trans('app.itemPerPage') }">
                  <template v-slot:no-data>
                    {{ textNoData }}
                  </template>
                  <template #[`item.name`]="{item}">
                    <a target="_blank"  :href="item.url">{{ truncateFileName(item.name,35) }}</a>
                  </template>
                  <template #[`item.size`]="{item}">
                    {{ item.file_size + ' KB' }}
                  </template>
                </v-data-table>
              </v-col>
            </v-tab-item>
          </v-tabs-items>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-row class="justify-end py-3">
            <v-btn color="default" class="mr-2" @click="close()">{{this.$trans('app.close')}}</v-btn>
            <v-btn color="error" @click="repEvent(2)" class="mr-2">{{this.$trans('app.absent')}}</v-btn>
            <v-btn color="warning" @click="repEvent(3)" class="mr-2">{{this.$trans('app.on_hold')}}</v-btn>
            <v-btn color="success"  @click="repEvent(1)" class="mr-2">{{this.$trans('app.presence')}}</v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mdiFileDocumentOutline , mdiLinkVariant , mdiMapMarkerOutline , mdiFileEditOutline ,mdiFileDocumentEditOutline , mdiFolderOutline} from '@mdi/js'
import useStore from "../useStore";
import { itemPerPage, textNoData,bytesToSize,truncateFileName  } from '@/common'
import store from "@/store";
import moment from "moment";
import axios from '@axios'

export default {
  name: "index",
  data() {
    return {
      currentTab : null
    }
  },
  props : {
    dialogs : {
      type : Object,
      require : true
    },
    event : {
      type : Object,
      // require : true
    }
  },
  watch: {
    event(value){
      if(value.is_allday){
        this.event.start = moment(value.start).format('YYYY-MM-DD');
        this.event.end = moment(value.end).format('YYYY-MM-DD');
        this.event.startDateTime =  moment(value.start).locale('ja').format('YYYY年MM月DD日 (ddd) ');
        this.event.endDateTime =  moment(value.end).locale('ja').format('YYYY年MM月DD日 (ddd)');
      }else {
        this.event.startDateTime =  moment(value.start).locale('ja').format('YYYY年MM月DD日 (ddd) HH:mm');
        this.event.endDateTime =  moment(value.end).locale('ja').format('YYYY年MM月DD日 (ddd) HH:mm');
      }
    }
  },
  methods : {
    close() {
      this.dialogs.replyEvent = false
      this.currentTab = null
    },
    repEvent(value){
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING',true)
        store.dispatch('calendar/replyEvent',{
          id : this.event.id,
          answer : value,
        }).then(response =>{
          this.dialogs.replyEvent = false;
          this.currentTab = null;
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar', {
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.update_success')
          })
          axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
            window.location.reload()
          })
        })
      }
    }
  },
  setup(){
    const { tableColumnReplyEvent,tableColumnFolderAdd } = useStore()
    return {
      itemPerPage,
      tableColumnReplyEvent,
      textNoData,
      tableColumnFolderAdd,
      bytesToSize,
      truncateFileName,
      icons : {
        mdiFileDocumentOutline,
        mdiMapMarkerOutline,
        mdiFileEditOutline,
        mdiFileDocumentEditOutline,
        mdiFolderOutline,
        mdiLinkVariant
      }
    }
  }

}
</script>

<style scoped>
.v-item-group {
  min-height: 300px;
}
</style>
