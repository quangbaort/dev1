<template>
  <div>
    <v-dialog v-model="dialogs.addNew" max-width="600" persistent scrollable>
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
        <v-tab  v-if="listBoard.length > 0">
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
                  v-model="name" :label="this.$trans('app.name_title') + this.$trans('app.required_valid')"
                  dense hide-details="auto" :rules="[validators.requiredField(name, this.$trans('app.name_title')), validators.maxLengthValidator(name , 255, this.$trans('app.name_title'))]"
                  :error-messages="errorMessages.name"
                >
                  <template v-slot:label>
                    {{ labelTitle}} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="nameKana" :label="this.$trans('app.name_title_kana') + this.$trans('app.required_valid')"
                  dense hide-details="auto" :rules="[validators.requiredField(nameKana, this.$trans('app.name_title_kana')), validators.maxLengthValidator(nameKana , 255, this.$trans('app.name_title_kana'))]"
                  :error-messages="errorMessages.nameKana"
                >
                  <template v-slot:label>
                    {{ labelKaNa }} <b class="required">*</b>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item class="pt-6" :transition="false">
            <v-treeview v-model="parentId" :items="listBoard" item-key="id"  selectable open-all open-on-click activatable
              selection-type="independent" @input="selectParent"></v-treeview>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-row class="justify-end py-3">
          <v-btn color="default" class="mx-2" @click="close()">
            {{ this.$trans('app.close') }}
          </v-btn>
          <v-btn color="success" class="mx-2" type="submit">
            {{ this.$trans('app.save') }}
          </v-btn>
        </v-row>
      </v-card-actions>
      </v-card>
    </v-form>
    </v-dialog>
  </div>
</template>

<script>
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
      default : { addNew : false}
    },
    listBoard: {
      required : true
    }
  },
  data(){
    return {
      loadingComponent : false,
      labelName : this.$trans('app.search'),
      labelTitle : this.$trans('app.name_title'),
      labelKaNa : this.$trans('app.name_title_kana'),
      snackbar : {
        isColorSnackbarVisible : false,
        message : '',
        color : 'error'
      },
      currentTab : null,
      name : '',
      nameKana : '',
      parentId : [],
      dispOrder: '1',
      errorMessages : {},
    }
  },

  methods: {
    handleFormSubmit(){
      if(!this.$refs.form.validate()){
        store.commit('setSnackbar' ,{
          isColorSnackbarVisible : true,
          color : 'error',
          message : trans('message.input_error')
        })
        return
      }
      const queryParams = {
        name : this.name,
        name_kana : this.nameKana,
        organization_id : JSON.parse(localStorage.getItem('Organization')).id,
        parent_id : this.parentId[0],
        disp_order : this.dispOrder
      }
      if(!store.state.loading){
        store.commit('TOGGLE_LOADING' , true)
        store.dispatch('apps/addNew' , queryParams)
        .then(response => {
          this.dialogs.addNew = false
          store.commit('setSnackbar' ,{
            isColorSnackbarVisible : true,
            color : 'success',
            message : trans('message.add_success')
          })
          this.$refs.form.reset()
          this.parentId = []
          this.$emit('refetch-data')
          this.$emit('refetch-tree')
          store.commit('TOGGLE_LOADING' , false)
          this.currentTab = null
        })
      }
    },
    close() {
      this.dialogs.addNew = false
      this.currentTab = null
      this.$refs.form.reset()
      this.parentId = []
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
