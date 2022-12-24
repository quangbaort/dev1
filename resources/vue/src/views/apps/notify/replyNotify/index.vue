<template>
    <v-dialog v-model="dialogs.replyNotify" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
      <v-card>
        <v-img v-if="image" class="image-header" :lazy-src="image" :src="image"></v-img>
        <v-card-title class="headline lighten-2" primary-title back>
          <span class="headline">{{inputVal.title}}{{this.$trans('app.notify_of')}}</span>
        </v-card-title>
        <v-tabs v-model="currentTab" icons-and-text grow >
          <v-tab>{{this.$trans('app.basic_information')}}
            <v-icon>{{ icons.mdiFileDocumentEditOutline }}</v-icon>
          </v-tab>
          <v-tab>{{this.$trans('app.detail')}}
            <v-icon>{{ icons.mdiFileDocumentOutline }}</v-icon>
          </v-tab>
          <v-tab>{{this.$trans('app.address')}}
            <v-icon>{{ icons.mdiMapMarkerOutline }}</v-icon>
          </v-tab>
        </v-tabs>
        <v-card-text>
        <v-tabs-items v-model="currentTab" >
          <v-tab-item class="pt-6 px-8" :transition="false">
            <v-row>
              <v-col cols="6" md="2">
                <h4>{{this.$trans('app.title')}}</h4>
              </v-col>
              <v-col cols="6" md="10">
                <p class="my-0">{{inputVal.title}}{{this.$trans('app.notify_of')}}</p>
              </v-col>
              <v-col cols="6" md="2">
                <h4>{{this.$trans('app.period')}}</h4>
              </v-col>
              <v-col cols="6" md="10">
                <p class="my-0">{{formatTimeToDate(inputVal.start)}}　～　{{formatTimeToDate(inputVal.end)}}</p>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="pt-6 px-8" :transition="false">
            <v-row>
              <v-col cols="12" md="12">
<!--                <p class="my-0">{{inputVal.title}}{{this.$trans('app.notify_of')}}</p>?-->
                <p>{{inputVal.detail}}</p>
              </v-col>
              <v-col md="12">
                <a class="pl-7" :href="inputVal.detail_url" target="_blank">{{inputVal.detail_url}}</a>
              </v-col>
              <v-col md="12" v-if="inputVal.file">
                <v-icon>{{ icons.mdiLinkVariant }}</v-icon>
                <a :href="inputVal.file.download_url" target="_blank">{{inputVal.file.title}}</a>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="pt-6 px-8" :transition="false">
            <v-row class="">
              <v-col md="2">
                <h4>{{this.$trans('app.place')}}</h4>
              </v-col>
              <v-col md="10">
                <p class="my-0">{{inputVal.place}}</p>
              </v-col>
            </v-row>
            <v-row class="">
              <v-col md="2">
                <h4>URL</h4>
              </v-col>
              <v-col md="10">
                <a :href="inputVal.place_url" target="_blank">{{inputVal.place_url}}</a>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs-items>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-row class="justify-end py-3">
            <v-btn color="default" class="mx-2" @click="close">{{this.$trans('app.close')}}</v-btn>
            <v-btn v-if="inputVal.can_response" color="success" class="mx-2" type="submit">{{this.$trans('app.already_read')}}</v-btn>
            <v-btn v-if="!inputVal.can_response" disabled color="success" class="mx-2" type="submit">{{this.$trans('app.already_read')}}</v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
      </v-form>
    </v-dialog>
</template>
<script>
import store from '@/store'
import { mdiFileDocumentOutline , mdiFileDocumentEditOutline ,mdiMapMarkerOutline , mdiLinkVariant } from '@mdi/js'
import { formatTimeToDate } from '@/common/'
import { ref } from '@vue/composition-api'
import axios from '@axios'
export default {
  props : {
    dialogs : { type : Object, required: true },
    module: {type :String},
    inputVal : { required : true },
  },
  watch: {
    inputVal() {
      if (this.inputVal.image_url) {
        this.image = this.inputVal.image_url
      }
      else {
        this.image = null
      }
    }
  },
  data() {
    return {
      image: '',
    }
  },
  methods: {
    handleFormSubmit(){
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('notify/readNotify' , this.inputVal.url_mark_read)
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          this.dialogs.replyNotify = false
          // this.$emit('refetch-data')
          axios.get('/total-unanswered-notify').then(response => {
            const { count } = response.data
            localStorage.setItem('totalUnansweredNotify', JSON.stringify(count))
            window.location.reload()
          })
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
        })
      }
    },
    close() {
      this.dialogs.replyNotify = false
      this.currentTab = null
    }
  },
  setup() {
    const currentTab = ref(null)
    return {
      currentTab,formatTimeToDate,
      icons : {
        mdiFileDocumentOutline,mdiFileDocumentEditOutline,mdiMapMarkerOutline,mdiLinkVariant
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 200px !important;
}
.image-header {
  height: 110px;
}
</style>
