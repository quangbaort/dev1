<template>
  <div>
    <v-alert v-model="alert" border="left" :type="results.errors.length > 0 ? 'error' : 'success' " dismissible  close-text="Close Alert" @input="close">
      <p class="ma-0">{{ results.success.company }}社を追加しました。</p>
      <p class="ma-0">{{ results.success.users }}ユーザーを追加しました。</p>
      <div v-for="(messages) in results.errors" v-if="results.errors.length > 0">
        <div v-for="message in messages" v-if="Array.isArray(messages)">
          {{ message }}
        </div>
      </div>
    </v-alert>
  </div>
</template>

<script>

import { mdiClose } from '@mdi/js';
import store from '@/store';
export default {
  props: {
    alert: {
      type: Boolean,
      default: true
    },
    results: {
      type: Object,
      default: () => ({
        count: {
          users: 0,
          company: 0
        },
        errors: [],
      })
    }
  },
  data(){
    return {
      type: 'error'
    }
  },
  methods: {
    close(){
      store.commit('TOGGLE_ALERT', false)
    }
  },
  watch: {
    results(value){
      if(value.errors.length > 0){
        this.type = 'error'
      }else{
        this.type = 'success'
      }
    }
  },
  setup() {
    return {
      icons : {
        mdiClose
      }
    }
  }
}
</script>
<style>
.theme--light.v-data-table td {
  color: rgba(0,0,0,.87) !important;
}
</style>
