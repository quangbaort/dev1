<template>
  <div>
    <v-dialog v-model="dialogs.addNew" @input="close" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
    <v-card class="px-8">
      <v-card-title class="headline">
        {{this.$trans('app.memo_setting')}}
      </v-card-title>
      <v-card-text>
          <v-row class="">
            <v-col md="12" class="mt-5">
            <v-text-field v-model="title" dense hide-details="auto"
              :rules="[validators.requiredField(title, labelTitle), validators.maxLengthValidator(title, 255, labelTitle)]"
              :error-messages="errorMessages.title">
              <template v-slot:label>
                {{ labelTitle }} <b class="required-field">*</b>
              </template>
            </v-text-field>
            </v-col>
          </v-row>
          <v-row class="">
            <v-col md="6" xs="12">
              <v-menu ref="menu" v-model="menu" :close-on-content-click="false"
                transition="scale-transition" width="270">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field  hide-details="auto" style="padding-top: 0" v-model="startDateFormated" readonly v-bind="attrs" v-on="on"
                  :rules="[validators.requiredField(startDateFormated, textDate.startDate)]"
                  :error-messages="errorMessages.start" >
                    <template v-slot:label>
                      {{ textDate.startDate }} <b class="required-field">*</b>
                    </template>
                  </v-text-field>
                </template>
                <v-date-picker hide-details="auto" @input="menu = false" locale="ja" no-title scrollable
                 :show-current="true"
                 :reactive="true"
                :day-format="startDate => new Date(startDate).getDate()" @change="$event => startDate = $event">
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col md="6" xs="12">
              <v-menu ref="menu1" v-model="menu1" :close-on-content-click="false"
                transition="scale-transition" width="270">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field hide-details="auto" style="padding-top: 0" v-model="endDateFormated" readonly v-bind="attrs" v-on="on"
                    :rules="[validators.requiredField(endDateFormated, textDate.endDate), validators.endDateAfter(startDate, endDate)]"
                    :error-messages="errorMessages.end" >
                    <template v-slot:label>
                      {{ textDate.endDate }} <b class="required-field">*</b>
                    </template>
                  </v-text-field>
                </template>
                <v-date-picker hide-details="auto" @input="menu1 = false"  locale="ja" no-title scrollable
                :day-format="endDate => new Date(endDate).getDate().toString()" @change="$event => endDate = $event">
                </v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
          <v-row class="">
            <v-col md="12">
            <v-text-field v-model="detail" :label="this.$trans('app.detail')" dense hide-details="auto"
                          :rules="[validators.maxLengthValidator(detail, 1023, this.$trans('app.detail'))]"
                          :error-messages="errorMessages.detail"
            ></v-text-field>
            </v-col>
          </v-row>
          <v-row class="">
            <v-col cols="12" md="6">
              <h3 class="mx-4">{{ this.$trans('app.color') }}</h3>
              <v-color-picker v-model="theme_color" hide-canvas></v-color-picker>
            </v-col>
            <v-col cols="12" md="6" class="pt-8">
              <v-img class="preview-image" style="max-height: 200px" :lazy-src="image" :src="image" ></v-img>
              <v-file-input v-model="imageSelected" accept="image/png, image/jpeg, image/bmp"  class="mt-4" @change="onFileChange" :label="this.$trans('app.image_file_select')" ></v-file-input>
            </v-col>
            <v-col cols="12" md="12">
              <v-list shaped class="rounded" style="border : 1px solid #ccc">
                <v-list-item-group v-model="icon" class="item-group">
                  <template v-for="(item, i) in items">
                    <v-divider v-if="!item" :key="`divider-${i}`"></v-divider>
                    <v-list-item class="un_border" v-else :key="`item-${i}`" :value="item" active-class="deep-purple--text text--accent-4" style="padding : 0 0px !important;">
                      <template v-slot:default="">
                        <v-list-item-content>
                          <v-list-item-title>
                            <v-icon v-if="item">{{ item }}</v-icon>
                          </v-list-item-title>
                        </v-list-item-content>
                      </template>
                    </v-list-item>
                    <v-list-item v-if="i == 7" :key="i" style="padding : 0 0 !important; color: #5d3495 !important;">
                      &nbsp;
                    </v-list-item>
                  </template>
                </v-list-item-group>
              </v-list>
            </v-col>
          </v-row>
          <v-row class="">
            <v-col md="12">
            <v-text-field v-model="url" label="URL" dense
                          :rules="[validators.maxLengthValidator(url, 1023, 'URL'), validators.urlValidator(url)]"
                          :error-messages="errorMessages.url"
            ></v-text-field>
            </v-col>
          </v-row>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="justify-right py-3">
        <v-btn color="default" class="mx-2" @click="close">
          {{ this.$trans('app.close') }}
        </v-btn>
        <v-btn color="success" class="mx-2" type="submit">
          {{ this.$trans('app.save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
      </v-form>
    </v-dialog>
    <alert-component :snackbar="snackbar"></alert-component>

    <loading-component :loading="loadingComponent"></loading-component>
  </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { requiredField , maxLengthValidator , lengthValidator , integerValidator, startDateBefore, endDateAfter, urlValidator } from '@core/utils/validation'
import store from '@/store'
import useStore from './useStore'
import AlertComponent from '@/common/components/snackBar.vue'
import { PerfectScrollbar } from 'vue2-perfect-scrollbar'
import pagination  from '@/common/components/Pagination.vue'
import { formatTimeToDate , organizationActive } from "@/common";
import moment from 'moment'
import loadingComponent from '@/common/components/loading.vue'
import {
  mdiPhone,
  mdiHeartOutline,
  mdiAccountBoxOutline,
  mdiAccountCircle,
  mdiOfficeBuildingOutline,
  mdiAccountSupervisorOutline,
  mdiLockOutline,
  mdiPlus,
  mdiMapMarkerOutline,
  mdiPhoneInTalkOutline,
  mdiNotebookEditOutline,
  mdiAirplane,
  mdiCarHatchback,
  mdiAlertCircle,
  mdiPencil,
  mdiEmoticonExcitedOutline, mdiEmoticonDeadOutline,mdiLiquor

} from '@mdi/js'
import "@mdi/font/css/materialdesignicons.css";
export default {
  components: {
    AlertComponent,
    PerfectScrollbar,
    pagination,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { addNew : false}
    },
  },
  data(){
    return {
      labelTitle: this.$trans('app.title'),
      loadingComponent : false,
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      image : '',
      organization_id: '',
      title : '',
      startDate : null,
      startDateFormated: '',
      endDateFormated: '',
      endDate : null,
      detail : '',
      theme_color : '',
      icon : '',
      imageSelected :null,
      url : '',
      errorMessages : {},
      menu: false,
      menu1: false,
    }
  },
  watch: {
    startDate (val) {
      this.startDateFormated = this.formatTimeToDate(this.startDate)
    },
    endDate (val) {
      this.endDateFormated = this.formatTimeToDate(this.endDate)
    },
  },
  methods: {
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      let formData = new FormData();
      const queryParams = {
        organization_id: JSON.parse(localStorage.getItem('Organization')).id,
        title : this.title,
        start : moment(this.startDate).format('YYYY-MM-DD hh:mm:ss'),
        end : moment(this.endDate).format('YYYY-MM-DD hh:mm:ss'),
        detail : this.detail,
        theme_color : this.theme_color,
        icon : this.icon,
        file : this.imageSelected,
        url: this.url,
      }
      for (const key in queryParams) {
        if(queryParams[key]){
          formData.append(key, queryParams[key])
        }
      }
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('memos/addNew' , formData)
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          this.dialogs.addNew = false
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'success'
          this.snackbar.message =  trans('message.add_success')
          this.startDate = null
          this.endDate = null
          this.$refs.form.reset()
          this.$emit('refetch-data')
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'error'
          this.snackbar.message = trans('message.error')
        })
      }
    },
    close() {
      this.startDate = null
      this.endDate = null
      this.dialogs.addNew = false
      this.$refs.form.reset()
    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image =  ''
      }
    }
  },
  setup() {
    // Perfect Scrollbar
    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
      wheelSpeed: 0.7,
    }
    const { textDate } = useStore()
    const items = [
      'mdi-airplane',
      'mdi-car-hatchback',
      'mdi-liquor',
      'mdi-alert-circle',
      'mdi-pencil',
      'mdi-emoticon-excited-outline',
      'mdi-emoticon-dead-outline',
      ''
    ]
    return {
      textDate,formatTimeToDate,
      mdiAirplane,mdiCarHatchback,
      mdiAlertCircle,mdiPencil,
      mdiEmoticonExcitedOutline,
      mdiEmoticonDeadOutline,
      organizationActive,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        startDateBefore,
        endDateAfter,
        urlValidator
      },
      items,
      icons: {
        mdiPhone,
        mdiHeartOutline,
        mdiAccountBoxOutline,
        mdiAccountCircle,
        mdiOfficeBuildingOutline,
        mdiAccountSupervisorOutline,
        mdiLockOutline,
        mdiPlus,
        mdiMapMarkerOutline,
        mdiPhoneInTalkOutline,
        mdiNotebookEditOutline,
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 450px !important;
}
.justify-right{
  justify-content: right !important;
}
.item-group{
  display: flex;
  min-height: 20px !important;
}

.v-application--is-ltr .v-list.v-sheet--shaped .v-list-item, .v-application--is-ltr .v-list.v-sheet--shaped .v-list-item::before, .v-application--is-ltr .v-list.v-sheet--shaped .v-list-item > .v-ripple__container{
  border-bottom-right-radius: 0 !important;
  border-top-right-radius: 0 !important;
  border-right: 1px solid #ccc;
}
.v-list {
  padding: 0 !important;
}
.un_border:last-child {
  border-right: none !important;

}
.un_border {
  min-height: 20px;
  padding: 0 5px;
  text-align: center;
}
</style>
<style>
.required-field {
  color : red !important;
}
.preview-image{
  height: 150px;
  border: 1px solid rgb(168, 166, 166);
}
</style>
