<template>
  <div>
    <v-dialog v-model="dialogs.detailMemo" max-width="800" persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
    <v-card class="mx-2 px-8">
        <v-card-title class="headline">
          {{this.$trans('app.memo_setting')}}
        </v-card-title>
      <v-card-text class="">
        <v-row class="">
          <v-col md="12" class="mt-5">
          <v-text-field v-model="inputVal.title" dense :disabled="readOnly"
              :rules="[validators.requiredField(inputVal.title, labelTitle), validators.maxLengthValidator(inputVal.title, 255, labelTitle)]"
              :error-messages="errorMessages.title">
          <template v-slot:label>
            {{ labelTitle }} <b class="required-field">*</b>
          </template></v-text-field>
          </v-col>
        </v-row>
        <v-row class="">
          <v-col md="6" xs="12">
            <v-menu ref="menu" v-model="menuStart" :close-on-content-click="false"
              transition="scale-transition" width="270">
              <template v-slot:activator="{ on, attrs }">
                <v-text-field  hide-details="auto" style="padding-top: 0" v-model="startDateFormated"  v-bind="attrs" v-on="on"
                :rules="[validators.requiredField(startDateFormated, textDate.startDate)]" :disabled="readOnly"
                  :error-messages="errorMessages.start">
                  <template v-slot:label>
                    {{ textDate.startDate }} <b class="required-field">*</b>
                  </template></v-text-field>
              </template>
              <v-date-picker @input="menuStart = false" hide-details="auto" locale="ja" no-title scrollable :disabled="readOnly"
                :day-format="startDate => new Date(startDate).getDate()" @change="$event => startDate = $event">
              </v-date-picker>
            </v-menu>
          </v-col>
          <v-col md="6" xs="12">
            <v-menu ref="menuEnd" v-model="menuEnd" :close-on-content-click="false" transition="scale-transition" offset-y min-width="auto">
              <template v-slot:activator="{ on, attrs }">
                <v-text-field hide-details="auto" v-model="endDateFormated" dense
                  v-bind="attrs" v-on="on" :disabled="readOnly"
                  :rules="[validators.requiredField(endDateFormated, textDate.endDate), validators.endDateAfter(startDate, endDate)]"
                  :error-messages="errorMessages.end">
                  <template v-slot:label>
                    {{ textDate.endDate }} <b class="required-field">*</b>
                  </template></v-text-field>
              </template>
                <v-date-picker @input="menuEnd = false" hide-details="auto" locale="ja" no-title scrollable :disabled="readOnly"
                  :day-format="endDate => new Date(endDate).getDate()" @change="$event => endDate = $event">
              </v-date-picker>
            </v-menu>
          </v-col>
        </v-row>
        <v-row class="">
          <v-col md="12">
          <v-text-field v-model="inputVal.detail" :label="this.$trans('app.detail')" dense
                        :rules="[validators.maxLengthValidator(inputVal.detail, 1023, this.$trans('app.detail'))]"
                        :error-messages="errorMessages.detail" :disabled="readOnly"
          ></v-text-field>
          </v-col>
        </v-row>
        <v-row class="">
          <v-col cols="12" md="6">
            <h3 class="mx-4">{{ this.$trans('app.color') }}</h3>
            <v-color-picker v-model="inputVal.theme_color" hide-canvas :disabled="readOnly"></v-color-picker>
          </v-col>
          <v-col cols="12" md="6" class="pt-8">
              <v-img class="preview-image" style="max-height: 200px" :lazy-src="image" :src="image" ></v-img>
              <v-file-input :disabled="readOnly" v-model="file" accept="image/png, image/jpeg, image/bmp" class="mt-4" @change="onFileChange" :label="this.$trans('app.image_file_select')" ></v-file-input>
            </v-col>
          <v-col cols="12" md="12">
            <v-list shaped class="rounded" style="border : 1px solid #ccc" :disabled="readOnly">
              <v-list-item-group v-model="inputVal.icon" class="item-group">
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
                  <v-list-item v-if="i == 7" style="padding : 0 0 !important; color: #6200ea !important;">
                    &nbsp;
                  </v-list-item>
                </template>
              </v-list-item-group>
            </v-list>
          </v-col>
        </v-row>
        <v-row class="">
          <v-col md="12">
          <v-text-field v-model="inputVal.url" label="URL" dense :disabled="readOnly"
                        :rules="[validators.maxLengthValidator(inputVal.url, 1023, 'URL'), validators.urlValidator(inputVal.url)]"
                        :error-messages="errorMessages.url"
          ></v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="justify-right">
        <div class="d-flex"></div>
        <v-btn color="default" class="py-3 px-4" @click="dialogs.detailMemo = false" >
          {{ this.$trans('app.close') }}
        </v-btn>
        <v-btn color="success" class="py-3 px-4" type="submit" v-if="!readOnly">
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
import { requiredField , maxLengthValidator , lengthValidator , integerValidator, urlValidator, endDateAfter } from '@core/utils/validation'
import store from '@/store'
import useStore from '../add/useStore'
import AlertComponent from '@/common/components/snackBar.vue'
import { formatTimeToDate, organizationActive, formatDateInput } from '@/common'
import moment from 'moment'
import {computed} from '@vue/composition-api'
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
  mdiEmoticonExcitedOutline, mdiEmoticonDeadOutline
} from '@mdi/js'
import "@mdi/font/css/materialdesignicons.css";
export default {
   watch: {
    inputVal () {
      this.startDate = formatDateInput(this.inputVal.start)
      this.endDate = formatDateInput(this.inputVal.end)
      if(this.inputVal.image_url){
        this.image = this.inputVal.image_url
        var myBlob = new Blob();
        this.file = this.blobToFile(myBlob, this.inputVal.name_image);
        this.oldFile = this.inputVal.image_url
      }
      else {
        this.image = ''
        this.file = null
      }
    },
    startDate () {
      this.startDateFormated = this.formatTimeToDate(this.startDate)
    },
    endDate () {
      this.endDateFormated = this.formatTimeToDate(this.endDate)
    },
  },
  components: {
    AlertComponent,
    loadingComponent
  },
  props : {
    dialogs: {
      type : Object,
      default : { detailMemo : false}
    },
    inputVal : { required : true },
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
      startDate : '',
      startDateFormated: '',
      endDateFormated: '',
      endDate : '',
      menuStart: false,
      menuEnd: false,
      file : null,
      oldFile: null,
      errorMessages : {},
    }
  },
  methods: {
    blobToFile(theBlob, fileName){
      theBlob.lastModifiedDate = new Date();
      theBlob.name = fileName;
      return theBlob;
    },
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      let formData = new FormData();
      if(this.oldFile){
        this.file = null
      }
      const queryParams = {
        id: this.inputVal.id,
        organization_id: this.inputVal.organization_id,
        title : this.inputVal.title,
        start : moment(this.startDate).format('YYYY-MM-DD hh:mm:ss'),
        end : moment(this.endDate).format('YYYY-MM-DD hh:mm:ss'),
        detail : this.inputVal.detail,
        theme_color : this.inputVal.theme_color,
        icon : this.inputVal.icon,
        file : this.file,
        url: this.inputVal.url,
        old_file: this.oldFile
      }
      for (const key in queryParams) {
        if(queryParams[key] ){
            formData.append(key, queryParams[key])
        }
        else {
          if (key != 'old_file' && key != 'file') {
            formData.append(key, '')
          }
        }
        }
      if(!store.state.loading) {
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('memos/addNew' , formData)
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          this.dialogs.detailMemo = false
          this.snackbar.isColorSnackbarVisible = true
          this.snackbar.color = 'success'
          this.snackbar.message = this.$trans('message.update_success')
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
      this.dialogs.detailMemo = false
      this.$refs.form.reset()
    },
    onFileChange(payload) {
      const file = payload; // in case vuetify file input
      if (file) {
        this.image = URL.createObjectURL(file);
      } else {
        this.image =  ''
      }
      this.oldFile = null
    }
  },
  setup() {
    const {textDate} = useStore()
    const readOnly = computed(() => {
      if(store.state.user.role){
        return store.state.user.role == 1 ? false : true
      }else if(store.state.user.is_super_admin){
        return false
      }
    })
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
      textDate,organizationActive,
      mdiAirplane,mdiCarHatchback,
      mdiAlertCircle,mdiPencil,
      mdiEmoticonExcitedOutline,
      mdiEmoticonDeadOutline,
      readOnly,
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator,
        endDateAfter,
        urlValidator
      },
      items,formatTimeToDate,formatDateInput,
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
        mdiNotebookEditOutline
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
