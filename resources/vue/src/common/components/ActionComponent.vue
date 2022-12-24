<template>
  <div>
  <v-row class="ma-0">
    <v-col cols="12" sm="2">
      <select-handel :selectHandel="listHandel" @changeAction="changeAction"></select-handel>
    </v-col>
    <v-row class="ma-0">
      <v-col cols="12" sm="4">
        <v-btn color="primary" @click="executionAction" :disabled="disableAction">
          <span>{{ this.$trans('app.execution') }}</span>
        </v-btn>
      </v-col>
    </v-row>
      <!-- add new -->
    <v-row v-if="module != 'Log'" class="justify-right ma-0" style="justify-content: right">
      <v-col cols="12" sm="4" class="text-right" v-if="module == 'users'">
        <v-btn color="primary" @click="uploadCSV">
          <v-icon>{{ icons.mdiImport  }}</v-icon>
          <span>CSVインポート</span>
          </v-btn>
      </v-col>
      <form ref="form" v-if="module == 'users'">
        <input type="file" v-on:change="submit" id="uploadCSV" style="display:none">
      </form>
      <v-col cols="12" sm="4" class="text-right">
        <v-btn v-if="onAdd" color="primary" @click="onAdd">
          <v-icon>{{ icons.mdiPlus }}</v-icon>
          <span>{{ this.$trans('app.add_new') }}</span>
        </v-btn>

        <v-btn v-if="!onAdd" color="primary" @click="dialogs.addNew = true" :disabled="disabled">
          <v-icon>{{ icons.mdiPlus }}</v-icon>
          <span>{{ this.$trans('app.add_new') }}</span>
        </v-btn>
      </v-col>
    </v-row>
  </v-row>
  <v-row class="ma-0" v-if="module == 'users' && alert" >
    <v-col>
      <alert-component :results="results" :alert="alert"></alert-component>
    </v-col>
  </v-row>
  </div>


</template>

<script>
import selectHandel from "@/common/components/selectHandel";
import AlertComponent from '@/common/components/AlertComponent.vue'
import { mdiPlus, mdiImport  } from '@mdi/js'
import axios from '@axios';
import store from '@/store';
import { computed } from '@vue/composition-api';
export default {
  name: "ActionComponent",
  components: {
    selectHandel,
    AlertComponent
  },
  props : {
    listHandel : { type: Array , required: true },
    dialogs : { type: Object , required: true , default : false},
    executionAction: { type: Function , required: true},
    onAdd: { type:Function},
    disabled : {type:Boolean},
    module : { type : String},
    disableAction : { type : Boolean},
  },
  data() {
    return {
      action: '',
      file: null
    }
  },
  methods : {
    changeAction(action){
      this.$emit('changeAction', action)
    },
    uploadCSV(){
      document.getElementById('uploadCSV').click();
    },
    submit(e){
      store.commit('TOGGLE_LOADING', true);
      let files = e.target.files;
      const formData = new FormData();
      formData.append('file', files[0]);
      axios.post('users/import-csv', formData).then(res => {
        this.$refs.form.reset()
        store.commit('TOGGLE_LOADING', false)
        store.commit('TOGGLE_ALERT', true)
        const { errors, success } = res.data.data;
        store.commit('setRefetchUser', true)
        store.commit('SetResultsImport', {
          success,
          errors,
        })
      }).catch(error => {
        this.$refs.form.reset()
        files = null;
      })
    }
  },
  setup(){
    const results = computed(() => {
      return store.state.resultsImport
    })
    const alert = computed(() => {
      return store.state.alert
    })
    return {
      results,
      alert,
      icons: {
        mdiPlus,
        mdiImport
      }
    }
  }
}
</script>

<style scoped>
button {
  width: 100% !important;
}
</style>
