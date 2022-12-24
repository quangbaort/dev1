<template>
  <div>
    <v-dialog v-model="dialogs.detail" max-width="600"  persistent scrollable>
      <v-form ref="form" @submit.prevent="handleFormSubmit">
        <v-card>
          <v-card-title>
            <span class="headline">{{ this.$trans('app.branch_committee_setting') }}</span>
          </v-card-title>
          <v-tabs v-model="currentTab" icons-and-text grow>
            <v-tab>
              {{ this.$trans('app.name_title') }}
              <v-icon>{{ icons.mdiHumanMaleBoard  }}</v-icon>
            </v-tab>
            <v-tab>
              {{ this.$trans('app.organization_level') }}
              <v-icon>{{ icons.mdiFamilyTree }}</v-icon>
            </v-tab>
          </v-tabs>
          <v-card-text>
            <v-tabs-items v-model="currentTab" class="px-8">
              <v-tab-item class="pt-6" :transition="false">
                <v-row>
                  <template v-slot:prepend-item>
                  <v-text-field hide-details="auto" v-model="name" :placeholder="this.$trans('app.search')" >
                    <template v-slot:label>
                      {{ labelName }} <b class="required">*</b>
                    </template>
                </v-text-field>
              </template>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="inputVal.name"
                      :label="this.$trans('app.name_title') + this.$trans('app.required_valid')" dense
                      hide-details="auto" :rules="[validators.requiredField(inputVal.name, this.$trans('app.name_title')), validators.maxLengthValidator(inputVal.name , 255, this.$trans('app.name_title'))]"
                      :error-messages="errorMessages.name"
                    >
                      <template v-slot:label>
                      {{ labelTitle}} <b class="required">*</b>
                    </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="inputVal.name_kana"
                      :label="this.$trans('app.name_title_kana') + this.$trans('app.required_valid')"
                      dense
                      hide-details="auto"
                      :rules="[validators.requiredField(inputVal.name_kana, this.$trans('app.name_title_kana')), validators.maxLengthValidator(inputVal.name_kana , 255, this.$trans('app.name_title_kana'))]"
                      :error-messages="errorMessages.name_kana"
                    >
                    <template v-slot:label>
                    {{ labelKaNa }} <b class="required">*</b>
                  </template>
                    </v-text-field>
                  </v-col>
                </v-row>
              </v-tab-item>
              <v-tab-item class="pt-6" :transition="false">
              <v-treeview v-model="inputVal.parent_id" :items="listBoard" item-key="id"  :value="inputVal.parent_id"
              selectable open-all open-on-click activatable selection-type="independent" @input="selectParent"></v-treeview>
              </v-tab-item>
            </v-tabs-items>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions class="justify-right py-3">
            <div class="d-flex"></div>
            <v-btn color="default" class="mx-2" @click="close()">
              {{ this.$trans('app.close') }}
            </v-btn>
            <v-btn color="success" class="mx-2" type="submit">
              {{ this.$trans('app.save') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
  </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { requiredField , maxLengthValidator , lengthValidator , integerValidator } from '@core/utils/validation'
import store from '@/store'
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
  mdiSourceBranch,
  mdiFamilyTree,
  mdiHumanMaleBoard
} from '@mdi/js'

export default {
  props : {
    dialogs: {
      type : Object,
      default : { detail : false}
    },
    inputVal : { required : true },
    listBoard: {
      required: true
    }
  },
  data(){
    return {
      loadingComponent : false,
      labelName : this.$trans('app.search'),
      labelTitle : this.$trans('app.name_title'),
      labelKaNa : this.$trans('app.name_title_kana'),
      currentTab : null,
      errorMessages : {},
    }
  },
  methods: {
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        this.snackbar.isColorSnackbarVisible = true
        this.snackbar.color = 'error'
        this.snackbar.message = trans('message.input_error')
        return
      }
      const queryParams = {
        id : this.inputVal.id,
        name : this.inputVal.name,
        name_kana : this.inputVal.name_kana,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        parent_id : this.inputVal.parent_id[0]
      }
      if(!queryParams.parent_id ){
          queryParams.parent_id = ''
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('apps/addNew' , queryParams)
        .then(response => {
          this.loadingComponent = false
          this.dialogs.detail = false
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.add_success')
          })
          this.$refs.form.reset()
          this.$emit('refetch-data')
          this.$emit('refetch-tree')
          store.commit('TOGGLE_LOADING' , false)
          this.currentTab = null
        })
      }
    },
    close() {
      this.dialogs.detail = false
      this.$refs.form.reset()
      this.currentTab = null
      this.inputVal.parent_id = []
    },
    selectParent(value) {
      if(value.length > 1){
        value.shift()
      }
    }
  },
  setup() {
    return {
      // validation rules
      validators: {
        requiredField,
        maxLengthValidator,
        lengthValidator,
        integerValidator
      },
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
        mdiSourceBranch,mdiFamilyTree,mdiHumanMaleBoard
      }
    }
  },
}
</script>
<style scoped>
.v-item-group{
  min-height: 350px !important;
}
.justify-right{
  justify-content: right !important;
}
.required {
  color : red !important;
}
</style>
