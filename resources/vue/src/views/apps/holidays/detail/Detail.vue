<template>
  <div>
  <v-dialog v-model="dialogs.detail" max-width="400" persistent>
    <v-card class="py-4">
      <v-card-title>
        <span class="headline"> {{ this.$trans('app.holiday_setting') }} </span>
      </v-card-title>
      <v-card-text>
        <v-form ref="form" @submit.prevent="handleFormSubmit">
          <v-row>
            <v-col cols="12" class="mt-5">
              <v-menu ref="menu" v-model="menu" :close-on-content-click="false"
                  transition="scale-transition" min-width="auto">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field v-model="inputVal.date" readonly v-bind="attrs" v-on="on"
                    :rules="[validators.requiredField(inputVal.date, dateField.text)]"
                    :error-messages="errorMessages.date">
                    <template v-slot:label>
                      {{ dateField.text }} <b class="required-field">*</b>
                    </template></v-text-field>
                </template>
                <v-date-picker locale="ja" no-title scrollable @input="menu = false"
                  :day-format="date => new Date(date).getDate()" @change="$event => inputVal.date = formatDate($event)">
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-text-field v-model="inputVal.name" dense
                  :rules="[validators.requiredField(inputVal.name, labelName), validators.maxLengthValidator(inputVal.name, 255, labelName)]"
                  :error-messages="errorMessages.name">
                  <template v-slot:label>
                    {{ labelName }} <b class="required-field">*</b>
                  </template>
              </v-text-field>
            </v-col>
          </v-row>
          <v-card-actions>
            <v-row class="py-3 justify-end mt-5">
              <v-btn color="default" class="mx-2" @click="dialogs.detail = false" type="button">
                {{ this.$trans('app.close') }}
              </v-btn>
              <v-btn color="success" class="mx-2" type="submit">
                {{ this.$trans('app.save') }}
              </v-btn>
            </v-row>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
  </div>
</template>
<script>
import { requiredField , maxLengthValidator} from '@core/utils/validation'
import store from "@/store"
import { formatDate } from '@/common'
export default {
  props: {
    dialogs: {
      type: Object,
      default: {addUser: false}
    },
    inputVal: {
      required: true
    }
  },
  data: () => ({
    labelName: trans('app.holiday_name'),
    errorMessages : {},
    loadingComponent: false,
    menu: false,
    date: null,
    // name: ''
  }),
  watch: {
    inputVal (val) {
      this.inputVal.date = formatDate(this.inputVal.date)
    }
  },
  methods: {
    changeDate(e) {
      this.inputVal.date = formatDate(e)
      this.menu = false
    },
    handleFormSubmit() {
      if(!this.$refs.form.validate()){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      if(this.startDate > this.endDate){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.start_date_must_before_end_date')
        })
        return
      }
      const queryParams = {
      id : this.inputVal.id,
      name: this.inputVal.name,
      date: this.inputVal.date,
      organization_id: JSON.parse(localStorage.getItem('Organization')).id
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('holiday/addNew', queryParams)
        .then(response => {
          store.commit('TOGGLE_LOADING' , false)
          this.dialogs.detail = false
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : this.$trans('message.update_success')
          })
          this.$refs.form.reset()
          this.$emit('refetch-data')
        })
        .catch(error => {
          store.commit('TOGGLE_LOADING' , false)
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'error',
            message : error.response.data.errors
          })
        })
      }
    },
    close() {
      this.dialogs.addNew = false
      this.$refs.form.reset()
    }
  },
  setup() {
     const dateField = (
      { text : trans('app.date') }
    )
    return {
      dateField,formatDate,
      validators: {
        requiredField,
        maxLengthValidator
      },
    }
  },

}
</script>

