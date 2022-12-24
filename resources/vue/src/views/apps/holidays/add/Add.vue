<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="400" persistent>
      <v-card class="py-4">
        <v-card-title>
          <span class="headline"> {{ this.$trans('app.holiday_setting') }} </span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" @submit.prevent="handleFormSubmit">
            <v-row>
              <v-col cols="12" class="mt-5">
                <v-menu ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition" width="270">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field  style="padding-top: 0" v-model="dateFormated" v-bind="attrs" v-on="on" readonly
                      :rules="[validators.requiredField(dateFormated, dateField.text)]"
                      :error-messages="errorMessages.date">
                      <template v-slot:label>
                        {{ dateField.text }} <b class="required-field">*</b>
                      </template>
                    </v-text-field>
                  </template>
                  <v-date-picker
                      @change="$event => date = $event"
                      :show-current="true"
                      :reactive="true"
                      locale="ja"
                      :day-format="date => new Date(date).getDate()"
                      @input="menu = false" no-title scrollable>
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="12" class="">
                <v-text-field v-model="name" dense
                  :rules="[validators.requiredField(name, labelName), validators.maxLengthValidator(name, 255, labelName)]"
                  :error-messages="errorMessages.name">
                  <template v-slot:label>
                    {{ labelName }} <b class="required-field">*</b>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>
            <v-card-actions>
              <v-row class="py-3 justify-end mt-5">
                <v-btn color="default" class="mx-2" @click="close" type="button">
                  {{ this.$trans('app.close') }}
                </v-btn>
                <v-btn color="success" class="mx-2" type="submit"> {{ this.$trans('app.save') }}</v-btn>
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
import store from "@/store";
import { formatDate , organizationActive } from "@/common";
import AlertComponent from '@/common/components/snackBar.vue'
export default {
  props: {
    dialogs: {
      type: Object,
      default: {addNew: false}
    }
  },
  data () {
    return {
      labelName: trans('app.holiday_name'),
      errorMessages : {},
      date : null,
      dateFormated: null,
      menu: false,
      name: ''
    }

  },
  watch: {
    date (val) {
      this.dateFormated = this.formatDate(this.date)
    },
  },
  methods: {
    handleFormSubmit() {
      if(!this.$refs.form.validate()){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
        const queryParams = {
        name: this.name,
        date: this.date,
        organization_id: JSON.parse(localStorage.getItem('Organization')).id
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('holiday/addNew', queryParams)
        .then(response => {
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : this.$trans('message.add_success')
          })
          store.commit('TOGGLE_LOADING' , false)
          this.dialogs.addNew = false
          this.date = null
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
      this.date = null
      this.dialogs.addNew = false
      this.$refs.form.reset()
    }
  },
  setup() {
    const dateField = (
      { text : trans('app.date') }
    )
    return {
      dateField, formatDate ,organizationActive,
      validators: {
        requiredField,
        maxLengthValidator
      },
    }
  },

}
</script>
<style>
.required-field {
  color : red !important;
}
</style>

